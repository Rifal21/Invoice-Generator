<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ScanController extends Controller
{
    public function scan(Request $request)
    {
        $request->validate([
            'file' => 'nullable|mimes:pdf,jpg,jpeg,png|max:10000',
            'text_input' => 'nullable|string',
        ]);

        if (!$request->hasFile('file') && !$request->filled('text_input')) {
            return response()->json([
                'success' => false,
                'message' => 'Harap unggah file (PDF/Gambar) atau masukkan teks.',
            ], 400);
        }

        try {
            $extractedText = null;
            $imageData = null;
            $mimeType = null;

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $mimeType = $file->getMimeType();
                Log::info('Scanning file: ' . $file->getClientOriginalName() . ' (' . $mimeType . ')');

                if ($file->extension() === 'pdf' || $mimeType === 'application/pdf') {
                    $parser = new Parser();
                    $pdf = $parser->parseFile($file->getPathname());
                    $extractedText = $pdf->getText();
                    Log::info('Extracted PDF text length: ' . strlen($extractedText));
                } elseif (str_starts_with($mimeType, 'image/')) {
                    $imageData = base64_encode(file_get_contents($file->getPathname()));
                }
            } elseif ($request->filled('text_input')) {
                $extractedText = $request->text_input;
                Log::info('Processing raw text input length: ' . strlen($extractedText));
            }

            // Try Gemini first (Supports Text & Images)
            $geminiKey = env('GEMINI_API_KEY');
            if ($geminiKey) {
                $response = $this->parseWithGemini($extractedText, $imageData, $mimeType, $geminiKey);
                $result = json_decode($response->getContent());
                if ($result && isset($result->success) && $result->success) {
                    return $response;
                }
                $errorMsg = $result->message ?? 'Unknown error';
                Log::warning('Gemini parsing failed, trying fallback if available: ' . $errorMsg);
            }

            // Fallback to Groq (Text Only for now, unless we switch to vision model)
            // Groq fallback only works if we have extracted text (PDF/Text Input).
            if ($extractedText) {
                $groqKey = env('GROQ_API_KEY');
                if ($groqKey) {
                    return $this->parseWithGroq($extractedText, $groqKey);
                }
            } elseif ($imageData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memproses gambar dengan Gemini, dan Groq (fallback) hanya mendukung teks.',
                ], 500);
            }

            return response()->json([
                'success' => false,
                'message' => 'API Key tidak ditemukan atau kuota habis.',
                'raw_text' => $extractedText
            ]);
        } catch (\Exception $e) {
            Log::error('Scan Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses data: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function parseWithGemini($text, $imageData, $mimeType, $apiKey)
    {
        $prompt = "Ekstrak data dari input berikut (bisa berupa teks pesanan, invoice, atau gambar) ke dalam format JSON.
        Identifikasi item, jumlah, harga (jika ada), dan satuannya.
        
        Format JSON harus memiliki struktur:
        {
            \"date\": \"YYYY-MM-DD (masukkan tanggal hari ini jika tidak ada tanggal spesifik di input)\",
            \"customer_name\": \"string (nama pelanggan/pemesan jika ada, kosongkan jika tidak ada)\",
            \"items\": [
                {
                    \"product_name\": \"string (nama produk)\",
                    \"quantity\": number (hanya angka),
                    \"price\": number (harga satuan jika ada, 0 jika tidak ada),
                    \"unit\": \"string (kg, pcs, pack, btr, dll)\"
                }
            ]
        }
        
        Penting:
        - Jika input berupa daftar seperti '1. beras 150 kg', ekstrak 'beras' sebagai product_name, 150 sebagai quantity, 'kg' sebagai unit.
        - Identifikasi satuan (unit) sesuai standar umum (kg, pcs, pack, btr, ikat, btl, ltr, dst) jika memungkinkan.
        - Abaikan penomoran baris.
        ";

        if ($text) {
            $prompt .= "\n\nTeks Input:\n" . $text;
        }

        $contents = [];
        $parts = [['text' => $prompt]];

        if ($imageData) {
            $parts[] = [
                'inline_data' => [
                    'mime_type' => $mimeType,
                    'data' => $imageData
                ]
            ];
        }

        $contents[] = ['parts' => $parts];

        try {
            $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                'contents' => $contents,
                'generationConfig' => [
                    'response_mime_type' => 'application/json',
                ]
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $jsonText = null;
                if (is_array($result) && isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                    $jsonText = (string) $result['candidates'][0]['content']['parts'][0]['text'];
                }

                if (!$jsonText) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Format respon AI tidak sesuai atau kosong.',
                    ], 500);
                }

                $data = json_decode($jsonText, true);

                return response()->json([
                    'success' => true,
                    'data' => $data
                ]);
            }

            $errorMessage = 'Gagal menghubungi Gemini API';
            if ($response instanceof \Illuminate\Http\Client\Response) {
                $errorMessage .= ': ' . ($response->body() ?: 'Unknown error');
            }

            return response()->json([
                'success' => false,
                'message' => $errorMessage,
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat parsing dengan AI: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function parseWithGroq($text, $apiKey)
    {
        $prompt = "Ekstrak data dari teks berikut ini ke dalam format JSON.
        Format JSON harus memiliki struktur:
        {
            \"date\": \"YYYY-MM-DD (gunakan tanggal hari ini jika tidak ditemukan)\",
            \"customer_name\": \"string (atau null)\",
            \"items\": [
                {
                    \"product_name\": \"string\",
                    \"quantity\": number,
                    \"price\": number (0 jika tidak ada),
                    \"unit\": \"string (kg, pcs, pack, dll)\"
                }
            ]
        }
        
        Teks Input:
        " . $text;

        try {
            $response = Http::withToken($apiKey)->post("https://api.groq.com/openai/v1/chat/completions", [
                'model' => 'llama-3.3-70b-versatile',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a helpful assistant that extracts structured data from text.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'response_format' => ['type' => 'json_object']
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $jsonText = $result['choices'][0]['message']['content'] ?? null;

                if (!$jsonText) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Format respon Groq tidak sesuai atau kosong.',
                    ], 500);
                }

                $data = json_decode($jsonText, true);

                return response()->json([
                    'success' => true,
                    'data' => $data
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghubungi Groq API: ' . ($response->body() ?: 'Unknown error'),
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat parsing dengan Groq: ' . $e->getMessage(),
            ], 500);
        }
    }
}
