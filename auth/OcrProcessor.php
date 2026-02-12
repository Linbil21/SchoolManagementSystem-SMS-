<?php
require_once '../Database/config.php';

class OcrProcessor {
    private $apiKey;
    private $apiUrl = "https://vision.googleapis.com/v1/images:annotate?key=";

    public function __construct() {
        $this->apiKey = defined('GOOGLE_CLOUD_VISION_API_KEY') ? GOOGLE_CLOUD_VISION_API_KEY : '';
    }

    /**
     * Process an image using Google Cloud Vision API
     * @param string $imagePath Local path to the image file
     * @return array Extracted data
     */
    public function scanDocument($imagePath) {
        if (empty($this->apiKey) || $this->apiKey === 'YOUR_GOOGLE_CLOUD_API_KEY_HERE') {
            return ['error' => 'Google Cloud Vision API Key is not configured.'];
        }

        $imageData = base64_encode(file_get_contents($imagePath));

        $requestBody = [
            "requests" => [
                [
                    "image" => [
                        "content" => $imageData
                    ],
                    "features" => [
                        [
                            "type" => "TEXT_DETECTION"
                        ]
                    ]
                ]
            ]
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl . $this->apiKey);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestBody));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            return ['error' => 'API Request failed with code ' . $httpCode, 'raw' => $response];
        }

        $result = json_decode($response, true);
        $text = $result['responses'][0]['fullTextAnnotation']['text'] ?? '';

        if (empty($text)) {
            return ['error' => 'No text detected in the document.'];
        }

        return $this->parseExtractedText($text);
    }

    /**
     * Simple parser for extracting fields from PSA Birth Certificate or IDs
     * This is a basic implementation and can be improved with better regex
     */
    private function parseExtractedText($text) {
        $data = [
            'raw_text' => $text,
            'first_name' => '',
            'middle_name' => '',
            'last_name' => '',
            'birthdate' => '',
            'gender' => ''
        ];

        // Example regex for PSA birth certificate (very simplified)
        // Note: Real OCR parsing typically requires more sophisticated logic or specific document AI models
        
        // Try to find Birthdate (Format: Month Day, Year or similar)
        if (preg_match('/(?:Date of Birth|DATE OF BIRTH)[:\s]*([A-Za-z]+ \d{1,2}, \d{4})/i', $text, $matches)) {
            $data['birthdate'] = date('Y-m-d', strtotime($matches[1]));
        }

        // Try to find Gender
        if (preg_match('/(?:Sex|SEX)[:\s]*(Male|Female|M|F)/i', $text, $matches)) {
            $val = strtoupper($matches[1]);
            $data['gender'] = ($val === 'M' || $val === 'MALE') ? 'Male' : 'Female';
        }

        // Try to find Names (This is tricky with plain OCR without high-perf models)
        // Usually PSA has labels like "First Name", "Middle Name", "Last Name"
        if (preg_match('/(?:First Name|FIRST NAME)[:\s]*([A-Z\s]+)/i', $text, $matches)) {
            $data['first_name'] = trim(explode("\n", $matches[1])[0]);
        }
        if (preg_match('/(?:Middle Name|MIDDLE NAME)[:\s]*([A-Z\s]+)/i', $text, $matches)) {
            $data['middle_name'] = trim(explode("\n", $matches[1])[0]);
        }
        if (preg_match('/(?:Last Name|LAST NAME)[:\s]*([A-Z\s]+)/i', $text, $matches)) {
            $data['last_name'] = trim(explode("\n", $matches[1])[0]);
        }

        return $data;
    }
}
