<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiService
{
    protected $geminiKey;
    protected $groqKey;

    public function __construct()
    {
        $this->geminiKey = env('GEMINI_API_KEY');
        $this->groqKey = env('GROQ_API_KEY');
    }

    /**
     * Analyze data using Gemini
     */
    public function analyze($prompt, $context = [])
    {
        if (!$this->geminiKey) {
            return $this->analyzeWithGroq($prompt, $context);
        }

        $systemPrompt = "Anda adalah AI Data Analyst Koperasi Jembar Rahayu Sejahtera (KJRS). 
        Tugas Anda adalah memberikan insight, ringkasan, dan saran berdasarkan data yang diberikan.
        Jawablah dengan sopan, profesional, dan dalam Bahasa Indonesia yang baik.
        Gunakan Markdown jika perlu membuat tabel atau daftar.

        DATA KONTEKS APLIKASI:
        " . json_encode($context, JSON_PRETTY_PRINT);

        $contents = [
            [
                'parts' => [
                    ['text' => $systemPrompt . "\n\nPertanyaan User: " . $prompt]
                ]
            ]
        ];

        try {
            // Reverted to stable 1.5-flash, as 2.5 does not exist yet.
            $model = env('GEMINI_MODEL', 'gemini-2.5-flash');
            $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$this->geminiKey}", [
                'contents' => $contents,
            ]);

            if ($response && $response->successful()) {
                $result = $response->json();
                return $result['candidates'][0]['content']['parts'][0]['text'] ?? "Maaf, AI tidak dapat memberikan jawaban.";
            }

            if ($response) {
                Log::error('Gemini Analysis Failed: ' . $response->body());
            }

            return $this->analyzeWithGroq($prompt, $context);
        } catch (\Exception $e) {
            Log::error('AiService Exception: ' . $e->getMessage());
            return $this->analyzeWithGroq($prompt, $context);
        }
    }

    protected function analyzeWithGroq($prompt, $context)
    {
        if (!$this->groqKey) {
            return "Maaf, konfigurasi AI (API Key) belum lengkap.";
        }

        try {
            $response = Http::withToken($this->groqKey)->post("https://api.groq.com/openai/v1/chat/completions", [
                'model' => 'llama-3.3-70b-versatile',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => "Anda adalah AI Data Analyst Koperasi Jembar Rahayu Sejahtera (KJRS). Tugas Anda memberikan insight data. Data Konteks: " . json_encode($context)
                    ],
                    ['role' => 'user', 'content' => $prompt]
                ],
            ]);

            if ($response && $response->successful()) {
                $result = $response->json();
                return $result['choices'][0]['message']['content'] ?? "Maaf, Groq AI gagal merespon.";
            }

            return "Maaf, semua layanan AI sedang sibuk.";
        } catch (\Exception $e) {
            return "Terjadi kesalahan pada sistem analisis: " . $e->getMessage();
        }
    }
}
