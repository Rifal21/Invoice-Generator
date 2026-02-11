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
                Log::info('--- AI SCAN START ---');
                Log::info('File: ' . $file->getClientOriginalName() . ' Type: ' . $mimeType);

                if ($file->extension() === 'pdf' || $mimeType === 'application/pdf') {
                    $parser = new Parser();
                    $pdf = $parser->parseFile($file->getPathname());
                    $extractedText = $pdf->getText();
                    // Clean up text: remove multiple spaces and newlines
                    $extractedText = preg_replace('/\s+/', ' ', $extractedText);
                    Log::info('Extracted Text Samples: ' . substr($extractedText, 0, 100));
                } elseif (str_starts_with($mimeType, 'image/')) {
                    $imageData = base64_encode(file_get_contents($file->getPathname()));
                    Log::info('Image processed as Base64');
                }
            } elseif ($request->filled('text_input')) {
                $extractedText = $request->text_input;
                Log::info('Manual Text Input received');
            }

            // 1. Try Gemini (Primary - Best for both text and images)
            $geminiKey = env('GEMINI_API_KEY');
            if ($geminiKey) {
                Log::info('Attempting parsing with Gemini...');
                $response = $this->parseWithGemini($extractedText, $imageData, $mimeType, $geminiKey);
                $result = json_decode($response->getContent());

                if ($result && isset($result->success) && $result->success) {
                    Log::info('Gemini Success');
                    return $response;
                }
                Log::warning('Gemini Failed: ' . ($result->message ?? 'No message'));
            }

            // 2. Fallback to Groq (Secondary - Fast text parsing)
            if ($extractedText) {
                $groqKey = env('GROQ_API_KEY');
                if ($groqKey) {
                    Log::info('Attempting fallback with Groq...');
                    return $this->parseWithGroq($extractedText, $groqKey);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Layanan AI tidak tersedia (API Key missing or quota exceeded).',
            ], 500);
        } catch (\Exception $e) {
            Log::error('Scan Fatal Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses data: ' . $e->getMessage(),
            ], 500);
        }
    }
    private function parseWithGemini($text, $imageData, $mimeType, $apiKey)
    {
        $prompt = "You are a professional accounting assistant. Extract data from the provided input (text or image) into a precise JSON format.
        
        RULES:
        1. Extract: date, customer_name, and items list.
        2. For each item, extract: product_name, quantity (numeric), price (unit price), and unit (kg, pcs, etc.).
        3. If data is missing:
           - date: Use today's date (" . date('Y-m-d') . ") if not found.
           - customer_name: Leave as null or empty string if not found.
           - price: Use 0 if not stated.
           - unit: Use 'pcs' as default if not clear.
        4. Normalize units to standard abbreviations: kg, btr, pcs, ltr, box, pack, ikat, sachet.
        5. Clean product names (remove numbering like '1.', '2.', etc.).
        
        JSON STRUCTURE:
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
        
        Input data follows below.";

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
            /** @var \Illuminate\Http\Client\Response $response */
            $model = "gemini-2.5-flash"; // Use stable 1.5-flash model
            $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                'contents' => $contents,
                'generationConfig' => [
                    'response_mime_type' => 'application/json',
                ]
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $jsonText = null;
                // ... (rest of the logic remains similar but we can just return here to keep the chunk focused)
                if (is_array($result) && isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                    $jsonText = (string) $result['candidates'][0]['content']['parts'][0]['text'];
                }

                if (!$jsonText) {
                    Log::error('Gemini Empty Response: ' . json_encode($result));
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

            // Capture detailed error
            $statusCode = $response->status();
            $errorBody = $response->json();
            $errorMessage = $errorBody['error']['message'] ?? $response->body();

            Log::error("Gemini API Error ({$statusCode}): " . json_encode($errorBody));

            return response()->json([
                'success' => false,
                'message' => "Gagal menghubungi Gemini AI ({$statusCode}): " . $errorMessage,
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
            /** @var \Illuminate\Http\Client\Response $response */
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
