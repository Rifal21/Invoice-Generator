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
            'file' => 'required|mimes:pdf|max:10000',
        ]);

        $file = $request->file('file');

        try {
            $parser = new Parser();
            Log::info('Scanning file: ' . $file->getClientOriginalName());
            $pdf = $parser->parseFile($file->getPathname());
            $text = $pdf->getText();
            Log::info('Extracted text length: ' . strlen($text));

            // Try Gemini first
            $geminiKey = env('GEMINI_API_KEY');
            if ($geminiKey) {
                $response = $this->parseWithGemini($text, $geminiKey);
                $result = json_decode($response->getContent());
                if ($result && isset($result->success) && $result->success) {
                    return $response;
                }
                $errorMsg = $result->message ?? 'Unknown error';
                Log::warning('Gemini parsing failed, trying Groq fallback: ' . $errorMsg);
            }

            // Fallback to Groq
            $groqKey = env('GROQ_API_KEY');
            if ($groqKey) {
                return $this->parseWithGroq($text, $groqKey);
            }

            return response()->json([
                'success' => false,
                'message' => 'API Key (Gemini/Groq) tidak ditemukan atau kuota habis. Silakan periksa file .env.',
                'raw_text' => $text
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses PDF: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function parseWithGemini($text, $apiKey)
    {
        $prompt = "Ekstrak data dari teks invoice berikut ke dalam format JSON. 
        Format JSON harus memiliki struktur:
        {
            \"date\": \"YYYY-MM-DD\",
            \"customer_name\": \"string\",
            \"items\": [
                {
                    \"product_name\": \"string\",
                    \"quantity\": number,
                    \"price\": number,
                    \"unit\": \"string\"
                }
            ]
        }
        
        Teks Invoice:
        " . $text;

        try {
            $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
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
        $prompt = "Ekstrak data dari teks invoice berikut ke dalam format JSON. 
        Format JSON harus memiliki struktur:
        {
            \"date\": \"YYYY-MM-DD\",
            \"customer_name\": \"string\",
            \"items\": [
                {
                    \"product_name\": \"string\",
                    \"quantity\": number,
                    \"price\": number,
                    \"unit\": \"string\"
                }
            ]
        }
        
        Teks Invoice:
        " . $text;

        try {
            $response = Http::withToken($apiKey)->post("https://api.groq.com/openai/v1/chat/completions", [
                'model' => 'llama-3.3-70b-versatile',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a helpful assistant that extracts invoice data into structured JSON.'],
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
