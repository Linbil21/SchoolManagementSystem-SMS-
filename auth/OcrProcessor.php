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
    /**
     * Process an image using Google Cloud Vision API
     * @param string $imagePath Local path to the image file
     * @param string $type The document type (generic, id_picture)
     * @param string $originalFilename The original name of the uploaded file (for simulation purposes)
     * @return array Extracted data
     */
    public function scanDocument($imagePath, $type = 'generic', $originalFilename = '') {
        // 1. Basic Image Validation (Aspect Ratio check for ID photos)
        if ($type === 'id_picture') {
            $imgSize = getimagesize($imagePath);
            if ($imgSize) {
                $width = $imgSize[0];
                $height = $imgSize[1];
                $aspect = $width / $height;

                // 2. Strict Orientation Check
                // Reject all Landscape photos (Width > Height)
                if ($width > $height) {
                    return [
                        'error' => 'Invalid Format. ID Photos must be Portrait (Vertical). Landscape (Horizontal) photos are not allowed.',
                        'is_valid' => false,
                        'debug_dims' => "$width x $height"
                    ];
                }

                // Aspect Ratio Check for Portrait/Square
                // Allow 3:4 (0.75) to 1:1 (1.0). 
                // We allow a bit of wiggle room: 0.6 to 1.05
                if ($aspect < 0.6 || $aspect > 1.05) {
                    return [
                        'error' => 'Invalid Aspect Ratio. Please upload a standard 2x2 or Passport Size photo.',
                        'is_valid' => false,
                        'debug_aspect' => $aspect
                    ];
                }
                
                // File Size Check - Increased to 15KB to filter out low-res thumbnails/icons
                if (filesize($imagePath) < 15000) { 
                     return [
                        'error' => 'Image resolution too low. Please upload a high-quality ID photo.',
                        'is_valid' => false
                    ];
                }
            }
        }

        // SIMULATION MODE
        if (empty($this->apiKey) || $this->apiKey === 'YOUR_GOOGLE_CLOUD_API_KEY_HERE') {
            if ($type === 'id_picture') {
                // SIMULATE ERROR: If filename contains specific keywords, trigger the rejection logic
                // This allows testing the error state even without a real API key.
                $forbiddenKeywords = ['anime', 'cartoon', 'drawing', 'sketch', 'art', 'fake', 'test', 'invalid', 'animation', 'admin', 'avatar', 'icon'];
                foreach ($forbiddenKeywords as $keyword) {
                    if (stripos($originalFilename, $keyword) !== false) {
                        return [
                            'error' => 'Invalid Photo (Simulated Check). Animated, cartoon, or generated images are not allowed. Please upload a actual formal ID photo.',
                            'is_valid' => false,
                            'is_simulation' => true
                        ];
                    }
                }

                return [
                    'is_simulation' => true,
                    'is_valid' => true,
                    'document_type' => 'Formal ID Photo',
                    'confidence' => 99.5,
                    'recommendation' => 'Valid ID Photo format detected.'
                ];
            }
            return $this->getSimulationData($originalFilename);
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
    private function getSimulationData($originalFilename = '') {
        // 1. Arrays for RANDOM generation (for generic files)
        $firstNames = ['JAMES', 'JOHN', 'MICHAEL', 'ANGELO', 'JOSHUA', 'MARK', 'CHRISTIAN', 'DANIEL', 'MARIA', 'JOY', 'ANGEL', 'JESSICA', 'NICOLE', 'GRACE'];
        $lastNames = ['SANTOS', 'REYES', 'CRUZ', 'BAUTISTA', 'OCAMPO', 'GARCIA', 'MENDOZA', 'TORRES', 'FLORES', 'CASTILLO'];
        $middleNames = ['D.', 'A.', 'S.', 'M.', 'L.', 'R.', 'P.'];
        $cities = ['Quezon City', 'Manila', 'Davao City', 'Cebu City', 'Zamboanga City', 'Antipolo', 'Pasig', 'Taguig', 'Cagayan de Oro', 'Parañaque'];
        $provinces = ['Metro Manila', 'Cebu', 'Davao del Sur', 'Rizal', 'Misamis Oriental', 'Cavite', 'Laguna', 'Bulacan'];

        // Default to SAMPLE Data
        $firstName = 'JUAN';
        $middleName = 'DELA';
        $lastName = 'CRUZ';
        $birthdate = '2000-01-01';
        $address = 'Manila, Philippines';
        $gender = 'Male';
        $contactNumber = '09' . mt_rand(100000000, 999999999);
        $guardian = 'MARIA CRUZ';
        $guardianContact = '09' . mt_rand(100000000, 999999999);
        $guardianEmail = 'maria.cruz@example.com';
        $relationship = 'Mother';

        // Add a simulation marker
        $recommendation = "SIMULATION MODE: Config API Key for real OCR.";

        // 2. Try to parse name from filename or specific demo keywords if provided
        if (!empty($originalFilename)) {
            $fn = strtolower($originalFilename);
            if (strpos($fn, 'lowell') !== false || strpos($fn, 'toribio') !== false || strpos($fn, 'alejaga') !== false) {
                 $firstName = 'LOWELL JR.';
                 $middleName = 'ALEJAGA';
                 $lastName = 'TORIBIO';
                 $birthdate = '2001-12-01';
                 $address = 'Camalaniugan, Cagayan';
                 $guardian = 'SHEILAH ALEJAGA';
                 $guardianContact = '09987654321';
                 $guardianEmail = 'sheilah.alejaga@example.com';
            } else {
                // Generic parsing from filename
                $namePart = pathinfo($originalFilename, PATHINFO_FILENAME);
                $namePart = preg_replace('/[_-]/', ' ', $namePart);
                $parts = array_filter(explode(' ', $namePart));
                
                // Check for HASH / GARBAGE filenames (purely numeric, UUIDs, or hex-like strings)
                $isGibberish = false;
                $allText = implode('', $parts);
                
                // If the whole filename looks like a UUID or Hex string (mix of numbers and A-F)
                if (preg_match('/^[0-9A-Fw-]+$/i', $allText) && (strlen($allText) > 10 || count($parts) > 3)) {
                    $isGibberish = true;
                }
                
                foreach ($parts as $part) {
                    if (preg_match('/^\d+$/', $part) && strlen($part) > 5) {
                        $isGibberish = true; 
                        break;
                    }
                }

                if (!$isGibberish && count($parts) >= 1) {
                    if (count($parts) >= 3) {
                        $lastName = strtoupper(array_pop($parts));
                        $middleName = strtoupper(array_pop($parts));
                        $firstName = strtoupper(implode(' ', $parts));
                    } elseif (count($parts) >= 2) {
                        $lastName = strtoupper(array_pop($parts));
                        $firstName = strtoupper($parts[0]);
                        $middleName = '';
                    } elseif (count($parts) == 1) {
                        $firstName = strtoupper($parts[0]);
                    }
                } else {
                    // Gibberish filename, use a nicer default for testing
                    $firstName = 'MANUAL';
                    $middleName = 'SCAN';
                    $lastName = 'REQUIRED';
                }
            }
        }

        // Return constructed data
        return [
            'is_simulation' => true,
            'is_valid' => true,
            'document_type' => 'PSA Birth Certificate',
            'confidence' => rand(95, 99) . '.' . rand(10, 99),
            'first_name' => $firstName,
            'middle_name' => $middleName,
            'last_name' => $lastName,
            'birthdate' => $birthdate,
            'gender' => $gender,
            'contact_number' => $contactNumber,
            'address' => $address,
            'guardian_name' => $guardian,
            'guardian_contact' => $guardianContact,
            'guardian_email' => $guardianEmail,
            'relationship' => $relationship,
            'recommendation' => $recommendation,
            'raw_text' => "OFFICE OF THE CIVIL REGISTRAR GENERAL. PSA Birth Certificate for $firstName $middleName $lastName. Born on $birthdate in $address. Gender: $gender. Name of Mother: $guardian."
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

        // Normalize text: replace multiple spaces and newlines
        $normalizedText = preg_replace('/\s+/', ' ', $text);

        // Check for Document Type
        if (preg_match('/(PHILIPPINE STATISTICS AUTHORITY|BIRTH CERTIFICATE|CERTIFICATE OF LIVE BIRTH|OFFICE OF THE CIVIL REGISTRAR GENERAL)/i', $text)) {
            $data['document_type'] = 'PSA Birth Certificate';
            $data['is_valid'] = true;
        } elseif (preg_match('/(FORM 137|FORM 138|REPORT CARD|PERMANENT RECORD|STUDENT CUMULATIVE RECORD)/i', $text)) {
            $data['document_type'] = 'School Transcript/Card';
            $data['is_valid'] = true;
        }

        // 1. IMPROVED NAME PARSING (PSA Specific)
        // PSA Format: 1. NAME (First) (Middle) (Last)
        // Often OCRed as: 1. NAME (First) (Middle) (Last) JUAN LUNA DELA CRUZ
        if (preg_match('/(?:NAME|Name)\s*\(?First\)?\s*\(?Middle\)?\s*\(?Last\)?\s*([A-Z\s,.-]{5,})/i', $normalizedText, $matches)) {
            $fullName = trim($matches[1]);
            $nameParts = explode(' ', $fullName);
            if (count($nameParts) >= 3) {
                $data['last_name'] = array_pop($nameParts);
                $data['middle_name'] = array_pop($nameParts);
                $data['first_name'] = implode(' ', $nameParts);
            }
        }

        // Fallback for names if specific PSA labels are found separately
        if (empty($data['first_name'])) {
            if (preg_match('/(?:First Name|FIRST NAME)[:\s]*([A-Z\s.-]+)/i', $text, $matches)) {
                $data['first_name'] = trim(explode("\n", $matches[1])[0]);
            }
            if (preg_match('/(?:Middle Name|MIDDLE NAME)[:\s]*([A-Z\s.-]+)/i', $text, $matches)) {
                $data['middle_name'] = trim(explode("\n", $matches[1])[0]);
            }
            if (preg_match('/(?:Last Name|LAST NAME)[:\s]*([A-Z\s.-]+)/i', $text, $matches)) {
                $data['last_name'] = trim(explode("\n", $matches[1])[0]);
            }
        }

        // Special PSA grid fallback: capturing Name after "1. NAME"
        if (empty($data['first_name']) && preg_match('/1\.\s+NAME\s+(?:(?:\()?First(?:\))?)?\s*(?:(?:\()?Middle(?:\))?)?\s*(?:(?:\()?Last(?:\))?)?[\s\n]+([A-Z\s]+)/i', $text, $matches)) {
             $lines = explode("\n", trim($matches[1]));
             if (count($lines) >= 3) {
                 $data['first_name'] = trim($lines[0]);
                 $data['middle_name'] = trim($lines[1]);
                 $data['last_name'] = trim($lines[2]);
             }
        }

        // 2. IMPROVED BIRTHDATE PARSING
        // PSA Format usually: 3. DATE OF BIRTH Day Month Year
        if (preg_match('/(?:Date of Birth|DATE OF BIRTH|Born on)[:\s]*([A-Za-z]+ \d{1,2}, \d{4})/i', $normalizedText, $matches)) {
            $timestamp = strtotime($matches[1]);
            if ($timestamp) $data['birthdate'] = date('Y-m-d', $timestamp);
        } elseif (preg_match('/(?:Date of Birth|DATE OF BIRTH)[:\s]*(\d{1,2})\s*([A-Za-z]+)\s*(\d{4})/i', $normalizedText, $matches)) {
            // Format: 01 December 2001
            $dobStr = $matches[1] . ' ' . $matches[2] . ' ' . $matches[3];
            $timestamp = strtotime($dobStr);
            if ($timestamp) $data['birthdate'] = date('Y-m-d', $timestamp);
        }

        // 3. GENDER PARSING
        if (preg_match('/(?:Sex|SEX)[:\s]*\s*(Female|Male|F|M)/i', $normalizedText, $matches)) {
            $val = strtoupper(trim($matches[1]));
            $data['gender'] = ($val === 'M' || $val === 'MALE') ? 'Male' : 'Female';
        }

        // If not a recognized document, lower the confidence
        if (!$data['is_valid']) {
            $data['confidence_mod'] = 0.5;
        }

        $data['recommendation'] = ""; 
        return $data;
    }
}
