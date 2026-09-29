<?php

namespace App\Helper;


class AiHelper {
    
    public static function cleanTextGemini($text) {
        if (empty($text)) return "";

        $endpoint = "https://generativelanguage.googleapis.com/v1beta/openai/chat/completions";

        $payload = [
            "model" => "gemini-2.5-flash-lite", 
            "messages" => [
                [
                    "role" => "system",
                    "content" => "You are an expert school registrar editor. 
                    TASK: 
                    1. Correct all spelling and grammar mistakes.
                    2. Fix improper capitalization (e.g., proper names of schools or students).
                    3. Rewrite the text to be professional and formal.
                    4. IMPORTANT: Only return the corrected text. Do not add 'Here is the correction' or 'Okay' or any extra sentences."
                ],
                [
                    "role" => "user",
                    "content" => $text 
                ]
            ]
            ];

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer " . ($_ENV['GEMINI_API'] ?? '')
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $response = curl_exec($ch);
        
        // Handle cURL errors
        if (curl_errno($ch)) {
            curl_close($ch);
            return "Error: " . curl_error($ch);
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $result = json_decode($response, true);


        if ($httpCode !== 200) {
            return "API Error: " . ($result['error']['message'] ?? 'Unknown error');
        }

        return $result['choices'][0]['message']['content'] ?? $text;
    }


    public static function cleanTextMistral($text)
    {
        if (empty($text)) return "";

        $endpoint = "https://api.mistral.ai/v1/chat/completions";

        $payload = [
            "model" => "mistral-small-latest", 
            "messages" => [
                [
                    "role" => "system",
                    "content" => "You are an expert school registrar editor. 
                    TASK: 
                    1. Correct all spelling and grammar mistakes.
                    2. Fix improper capitalization (e.g., proper names of schools or students).
                    3. Rewrite the text to be professional and formal.
                    4. IMPORTANT: Only return the corrected text. Do not add 'Here is the correction' or 'Okay' or any extra sentences."
                ],
                [
                    "role" => "user",
                    "content" => $text 
                ]
            ]
            ];

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer " . ($_ENV['MISTRAL_API'] ?? '')
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $response = curl_exec($ch);
        
        // Handle cURL errors
        if (curl_errno($ch)) {
            curl_close($ch);
            return "Error: " . curl_error($ch);
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $result = json_decode($response, true);


        if ($httpCode !== 200) {
            return "API Error: " . ($result['error']['message'] ?? 'Unknown error');
        }

        return $result['choices'][0]['message']['content'] ?? $text;

    }



}