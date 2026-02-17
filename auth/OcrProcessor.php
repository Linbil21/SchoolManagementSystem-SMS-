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
    public function scanDocument($imagePath, $type = 'generic') {
        // 1. Basic Image Validation (Aspect Ratio check for ID photos)
        if ($type === 'id_picture') {
            $imgSize = getimagesize($imagePath);
            if ($imgSize) {
                $width = $imgSize[0];
                $height = $imgSize[1];
                $aspect = $width / $height;

                // ID Photo should be Portrait (approx 3:4) or Square (1:1)
                // Strict Range: 0.65 (Tall Portrait) to 1.05 (Square with margin)
                // Rejects Landscape photos immediately.
                if ($aspect < 0.65 || $aspect > 1.05) {
                    return [
                        'error' => 'Invalid ID Photo Format. Photo must be PORTRAIT or SQUARE (Passport Size/2x2). Landscape photos are not allowed.',
                        'is_valid' => false,
                        'debug_aspect' => $aspect
                    ];
                }
                
                // File Size Check (e.g. Reject very small images/thumbnails)
                if (filesize($imagePath) < 5000) { // < 5KB
                     return [
                        'error' => 'Image too small. Please upload a high-quality ID photo.',
                        'is_valid' => false
                    ];
                }
            }
        }

        // SIMULATION MODE
        if (empty($this->apiKey) || $this->apiKey === 'YOUR_GOOGLE_CLOUD_API_KEY_HERE') {
            if ($type === 'id_picture') {
                return [
                    'is_simulation' => true,
                    'is_valid' => true,
                    'document_type' => 'Formal ID Photo',
                    'confidence' => 99.5,
                    'recommendation' => 'Valid ID Photo format detected.'
                ];
            }
            return $this->getSimulationData();
        }

        $imageData = base64_encode(file_get_contents($imagePath));
        
        // Determine features based on document type
        $features = [["type" => "TEXT_DETECTION"]];
        if ($type === 'id_picture') {
            $features = [
                ["type" => "FACE_DETECTION"],
                ["type" => "SAFE_SEARCH_DETECTION"],
                ["type" => "LABEL_DETECTION"] // For anime/cartoon detection
            ];
        }

        $requestBody = [
            "requests" => [
                [
                    "image" => [ "content" => $imageData ],
                    "features" => $features
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
            return ['error' => 'API Request failed with code ' . $httpCode];
        }

        $result = json_decode($response, true);
        
        // Handle ID Photo Logic
        if ($type === 'id_picture') {
            // 1. Anime/Cartoon/Spoof Check
            $safeSearch = $result['responses'][0]['safeSearchAnnotation'] ?? [];
            $labels = $result['responses'][0]['labelAnnotations'] ?? [];
            
            // Check SafeSearch for Spoof (Cartoons/Drawings)
            if (($safeSearch['spoof'] ?? '') === 'LIKELY' || ($safeSearch['spoof'] ?? '') === 'VERY_LIKELY') {
                return [
                    'error' => 'Invalid Photo. Animated, cartoon, or generated images are not allowed. Please upload a actual formal ID photo.',
                    'is_valid' => false
                ];
            }

            // Check Labels for generic cartoons (Double Check)
            foreach ($labels as $label) {
                $desc = strtolower($label['description']);
                if (in_array($desc, ['anime', 'cartoon', 'animation', 'drawing', 'illustration', 'comics', 'fictional character']) && $label['score'] > 0.8) {
                    return [
                        'error' => 'Anime/Cartoon detected. Please upload a real formal ID photo.',
                        'is_valid' => false
                    ];
                }
            }

            $faces = $result['responses'][0]['faceAnnotations'] ?? [];
            if (empty($faces)) {
                return [
                    'error' => 'No face detected. Please ensure the photo is clear and contains a visible face.',
                    'is_valid' => false
                ];
            }
            if (count($faces) > 1) {
                return [
                    'error' => 'Multiple faces detected. Please upload a solo ID photo.',
                    'is_valid' => false
                ];
            }
            
            // Check for formal pose (Head tilt/pan) - Basic logic
            $face = $faces[0];
            $panAngle = abs($face['panAngle'] ?? 0);
            $tiltAngle = abs($face['tiltAngle'] ?? 0);
            
            if ($panAngle > 20 || $tiltAngle > 20) {
                 return [
                    'error' => 'Face is not facing forward. Please look directly at the camera for a formal photo.',
                    'is_valid' => false,
                    'debug_angles' => "Pan: $panAngle, Tilt: $tiltAngle"
                ];
            }

            return [
                'is_valid' => true,
                'document_type' => 'Formal ID Photo',
                'confidence' => ($face['detectionConfidence'] ?? 0.9) * 100,
                'recommendation' => 'Formal ID Photo Verified.'
            ];
        }
        
        // Default Text Logic
        $text = $result['responses'][0]['fullTextAnnotation']['text'] ?? '';
        $confidence = $result['responses'][0]['fullTextAnnotation']['pages'][0]['confidence'] ?? 0;

        if (empty($text)) {
            return ['error' => 'No text detected in the document.'];
        }

        $parsedData = $this->parseExtractedText($text);
        
        // Calculate confidence
        $finalConfidence = round($confidence * 100, 2);
        
        // Apply modifier if set (e.g. for unknown docs)
        if (isset($parsedData['confidence_mod'])) {
            $finalConfidence = $finalConfidence * $parsedData['confidence_mod'];
            unset($parsedData['confidence_mod']);
        }
        
        $parsedData['confidence'] = $finalConfidence;
        
        return $parsedData;
    }

    /**
     * Simulation data for demonstration when API key is missing
     */
    private function getSimulationData() {
        return [
            'is_simulation' => true,
            'is_valid' => true,
            'document_type' => 'PSA Birth Certificate',
            'confidence' => rand(95, 99) . '.' . rand(10, 99),
            'first_name' => 'JUAN',
            'middle_name' => 'PROTOTYPE',
            'last_name' => 'DELA CRUZ',
            'birthdate' => '2005-05-15',
            'gender' => 'Male',
            'contact_number' => '09123456789',
            'address' => '123 Street, City, Province',
            'guardian_name' => 'MARIA DELA CRUZ',
            'guardian_contact' => '09987654321',
            'guardian_email' => 'maria.delacruz@example.com',
            'relationship' => 'Mother',
            'recommendation' => '',
            'raw_text' => 'SIMULATED DATA: PHILIPPINE STATISTICS AUTHORITY Birth Certificate Juan Prototype Dela Cruz May 15, 2005 Male. Mother: Maria Dela Cruz. Address: 123 Street, City, Province. Contact: 09123456789. High honors in Computer Studies.'
        ];
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
            'gender' => '',
            'document_type' => 'Unknown',
            'is_valid' => false
        ];

        // Check for Document Type
        if (preg_match('/(PHILIPPINE STATISTICS AUTHORITY|BIRTH CERTIFICATE|CERTIFICATE OF LIVE BIRTH)/i', $text)) {
            $data['document_type'] = 'PSA Birth Certificate';
            $data['is_valid'] = true;
        } elseif (preg_match('/(FORM 137|PERMANENT RECORD|STUDENT CUMULATIVE RECORD)/i', $text)) {
            $data['document_type'] = 'Form 137';
            $data['is_valid'] = true;
        }

        // If not a recognized document, lower the confidence or mark as invalid
        if (!$data['is_valid']) {
            $data['confidence_mod'] = 0.5; // Reduce confidence for unknown documents
        }

        // Try to find Birthdate (Format: Month Day, Year or similar)
        if (preg_match('/(?:Date of Birth|DATE OF BIRTH|Born on)[:\s]*([A-Za-z]+ \d{1,2}, \d{4})/i', $text, $matches)) {
            $timestamp = strtotime($matches[1]);
            if ($timestamp) {
                $data['birthdate'] = date('Y-m-d', $timestamp);
            }
        }

        // Try to find Gender
        if (preg_match('/(?:Sex|SEX)[:\s]*(Male|Female|M|F)/i', $text, $matches)) {
            $val = strtoupper(trim($matches[1]));
            $data['gender'] = ($val === 'M' || $val === 'MALE') ? 'Male' : 'Female';
        }

        // Try to find Names (More robust parsing)
        if (preg_match('/(?:First Name|FIRST NAME)[:\s]*([A-Z\s.-]+)/i', $text, $matches)) {
            $data['first_name'] = trim(explode("\n", $matches[1])[0]);
        }
        if (preg_match('/(?:Middle Name|MIDDLE NAME)[:\s]*([A-Z\s.-]+)/i', $text, $matches)) {
            $data['middle_name'] = trim(explode("\n", $matches[1])[0]);
        }
        if (preg_match('/(?:Last Name|LAST NAME)[:\s]*([A-Z\s.-]+)/i', $text, $matches)) {
            $data['last_name'] = trim(explode("\n", $matches[1])[0]);
        }

        // Fallback name detection if specific labels aren't found
        if (empty($data['first_name']) && preg_match('/NAME OF CHILD[:\s]*([A-Z\s,]+)/i', $text, $matches)) {
             $nameParts = explode(',', $matches[1]);
             if (count($nameParts) >= 2) {
                 $data['last_name'] = trim($nameParts[0]);
                 $data['first_name'] = trim($nameParts[1]);
             }
        }

        // Try to find Guardian (Father/Mother on Birth Cert)
        if (preg_match('/(?:Father|FATHER|Mother|MOTHER)[:\s]*([A-Z\s,]+)/i', $text, $matches)) {
            $data['guardian_name'] = trim(explode("\n", $matches[1])[0]);
        }

        // SIMPLE RECOMMENDATION REMOVED AS REQUESTED BY USER
        $data['recommendation'] = ""; 

        return $data;
    }
}
