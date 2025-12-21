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

            // If Gemini API Key is available, use it for better parsing
            $geminiKey = env('GEMINI_API_KEY');

            if ($geminiKey) {
                return $this->parseWithGemini($text, $geminiKey);
            }

            // Fallback to basic regex parsing (very limited)
            return response()->json([
                'success' => false,
                'message' => 'Gemini API Key tidak ditemukan. Silakan tambahkan GEMINI_API_KEY di file .env untuk hasil scan yang akurat.',
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
}
