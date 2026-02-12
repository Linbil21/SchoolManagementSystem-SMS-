<?php

class StudentPortal {
    private $api_url = "https://css.jampzdev.com/api/student-subject.php";

    /**
     * Fetches student subjects and schedule from the external dynamic API
     * @param string $student_id
     * @return array|null
     */
    public function getStudentSubjects($student_id) {
        $url = $this->api_url . "?student_id=" . urlencode($student_id);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && $response) {
            $data = json_decode($response, true);
            // Universal adapter for different API versions
            if (isset($data['status'])) {
                $data['success'] = ($data['status'] === 'success');
            }
            if (isset($data['data'])) {
                $data['subjects'] = $data['data'];
            }
            return $data;
        }

        return null;
    }

    /**
     * Formats raw API data into the structure expected by the Schedule UI
     * @param array $apiSubjects
     * @return array
     */
    public function formatSchedule($apiSubjects) {
        $formatted = [];
        $daysMapping = [
            'M' => 'Monday',
            'T' => 'Tuesday',
            'W' => 'Wednesday',
            'TH' => 'Thursday',
            'F' => 'Friday',
            'S' => 'Saturday',
            'Monday' => 'Monday',
            'Tuesday' => 'Tuesday',
            'Wednesday' => 'Wednesday',
            'Thursday' => 'Thursday',
            'Friday' => 'Friday',
            'Saturday' => 'Saturday'
        ];

        if (!is_array($apiSubjects)) return [];

        foreach ($apiSubjects as $subject) {
            // Handle both camelCase from screenshot and snake_case from previous iteration
            $subjectName = $subject['subjectName'] ?? $subject['subject_name'] ?? 'Unknown Subject';
            $teacherName = $subject['teacherName'] ?? $subject['instructor'] ?? 'TBA';
            $roomName = $subject['roomName'] ?? $subject['room_name'] ?? 'N/A';
            $startTime = $subject['startTime'] ?? $subject['start_time'] ?? 'N/A';
            $endTime = $subject['endTime'] ?? $subject['end_time'] ?? 'N/A';
            $rawDays = $subject['day'] ?? $subject['days'] ?? '';
            
            $splitDays = explode('/', $rawDays);

            foreach ($splitDays as $dayChar) {
                $dayChar = trim($dayChar);
                if (isset($daysMapping[$dayChar])) {
                    $dayName = $daysMapping[$dayChar];
                    
                    if (!isset($formatted[$dayName])) {
                        $formatted[$dayName] = [];
                    }

                    $formatted[$dayName][] = [
                        'time' => $startTime,
                        'end' => $endTime,
                        'subject' => $subjectName,
                        'room' => $roomName,
                        'teacher' => $teacherName,
                        'color' => $this->getRandomColor($subjectName)
                    ];
                }
            }
        }
        return $formatted;
    }

    /**
     * Consolidates a day-grouped schedule into a unique list of subjects with multiple days
     * Used for tabular displays like the COR
     */
    public function getConsolidatedSubjects($weeklySchedule) {
        $processed = [];
        foreach ($weeklySchedule as $day => $classes) {
            foreach ($classes as $class) {
                $sub_key = $class['subject'];
                if (!isset($processed[$sub_key])) {
                    $processed[$sub_key] = [
                        'subject' => $class['subject'],
                        'days' => [substr($day, 0, 3)],
                        'time' => $class['time'] . ' - ' . $class['end'],
                        'room' => $class['room'],
                        'teacher' => $class['teacher'],
                        'code' => strtoupper(substr($class['subject'], 0, 3)) . '-' . rand(100, 999),
                        'units' => rand(2, 3) . '.0'
                    ];
                } else {
                    if (!in_array(substr($day, 0, 3), $processed[$sub_key]['days'])) {
                        $processed[$sub_key]['days'][] = substr($day, 0, 3);
                    }
                }
            }
        }
        return $processed;
    }

    private function getRandomColor($seed) {
        $colors = ['#2563eb', '#9333ea', '#16a34a', '#db2777', '#f59e0b', '#ea580c', '#0ea5e9'];
        $index = abs(crc32($seed)) % count($colors);
        return $colors[$index];
    }
}
?>
