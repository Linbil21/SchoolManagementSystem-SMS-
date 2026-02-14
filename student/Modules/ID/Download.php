<?php
session_start();
// Security check
require_once '../../../auth/Security.php';
checkRole(['student']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download ID</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #2563eb;
            --secondary: #64748b;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #1e293b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: var(--bg);
            display: flex;
            min-height: 100vh;
        }

        .main-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        .content-area {
            padding: 40px;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        .page-header {
            margin-bottom: 30px;
        }

        .page-title {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--text-main);
        }

        .download-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            max-width: 600px;
            margin: 0 auto;
        }
        
        .preview-box {
            background: #f1f5f9;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            border: 2px dashed #cbd5e1;
        }
        
        .preview-icon {
            font-size: 3rem;
            color: #94a3b8;
            margin-bottom: 10px;
        }
        
        .format-options {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .format-btn {
            padding: 15px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            background: white;
            cursor: pointer;
            transition: all 0.2s;
            text-align: left;
        }
        
        .format-btn:hover {
            border-color: var(--primary);
            background: #eff6ff;
        }
        
        .format-btn.active {
            border-color: var(--primary);
            background: #eff6ff;
            position: relative;
        }
        
        .format-btn h4 {
            margin: 0;
            color: #1e293b;
            font-size: 1rem;
        }
        
        .format-btn p {
            margin: 0;
            font-size: 0.8rem;
            color: #64748b;
        }

        .download-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 15px 40px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: background 0.2s;
        }
        
        .download-btn:hover {
            background: #1d4ed8;
        }

    </style>
</head>

<body>
    <?php include '../../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../../Components/Header.php'; ?>
        <div class="content-area">
            <div class="page-header">
                <h1 class="page-title">Download ID</h1>
                <p style="color: #64748b;">Save a copy of your student ID for offline use.</p>
            </div>

            <div class="download-card">
                <div class="preview-box">
                    <i class="fas fa-id-card preview-icon"></i>
                    <p style="color: #64748b;">ID Card Preview Ready</p>
                </div>
                
                <h3 style="margin-bottom: 15px; text-align: left; color: #1e293b;">Select Format</h3>
                <div class="format-options">
                    <button class="format-btn active">
                        <h4><i class="fas fa-file-pdf" style="color: #ef4444; margin-right: 8px;"></i> PDF Document</h4>
                        <p>Best for printing</p>
                    </button>
                    <button class="format-btn">
                        <h4><i class="fas fa-file-image" style="color: #3b82f6; margin-right: 8px;"></i> PNG Image</h4>
                        <p>Best for digital use</p>
                    </button>
                </div>
                
                <button class="download-btn" onclick="startDownload()">
                    <i class="fas fa-download"></i> Download Now
                </button>
            </div>

        </div>
    </div>
    
    <!-- Hidden container for ID Card generation -->
    <div id="idCapture" style="position: absolute; left: -9999px; top: -9999px;">
        <div id="idCardContent" style="width: 320px; height: 500px; background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%); border-radius: 20px; position: relative; overflow: hidden; display: flex; flex-direction: column; color: white;">
            <?php
            require_once '../../../Database/config.php';
            $stmt = $pdo->prepare("SELECT * FROM students WHERE student_id = ?");
            $stmt->execute([$_SESSION['student_id'] ?? '']);
            $student_data = $stmt->fetch();
            
            $s_name = "STUDENT NAME";
            $s_id = "STUDENT-ID";
            $s_course = "COURSE";
            $s_photo = "/Assets/image/logo.png";
            
            if ($student_data) {
                $s_name = strtoupper($student_data->first_name . ' ' . $student_data->last_name);
                $s_id = $student_data->student_id;
                $s_course = strtoupper($student_data->course);
                $s_photo = $student_data->profile_image ? "/" . $student_data->profile_image : "https://ui-avatars.com/api/?name=" . urlencode($s_name);
            }
            ?>
            <div style="padding: 20px; text-align: center; background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(5px);">
                <div style="font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">UNIVERSITY OF TECHNOLOGY</div>
            </div>
            
            <div style="padding: 20px 0; display: flex; justify-content: center;">
                <div style="width: 130px; height: 130px; border-radius: 50%; border: 4px solid #fbbf24; overflow: hidden; background: white; padding: 3px;">
                    <img src="<?php echo $s_photo; ?>" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                </div>
            </div>
            
            <div style="text-align: center; padding: 0 20px; flex-grow: 1;">
                <div style="font-size: 1.2rem; font-weight: 800; text-transform: uppercase; margin-bottom: 5px; color: #fbbf24;"><?php echo $s_name; ?></div>
                <div style="font-size: 0.9rem; opacity: 0.9; margin-bottom: 5px;"><?php echo $s_id; ?></div>
                <div style="font-size: 0.75rem; background: rgba(255, 255, 255, 0.15); padding: 5px 10px; border-radius: 20px; display: inline-block; margin-bottom: 20px;"><?php echo $s_course; ?></div>
                
                <div style="display: flex; justify-content: center; margin-top: 10px;">
                    <div style="background: white; padding: 5px; border-radius: 8px;">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?php echo $s_id; ?>" style="width: 50px; height: 50px;">
                    </div>
                </div>
            </div>
            
            <div style="font-size: 0.7rem; opacity: 0.7; position: absolute; bottom: 20px; width: 100%; text-align: center;">
                VALID UNTIL: JULY 2027<br>STUDENT SIGNATURE
            </div>
        </div>
    </div>

    <!-- External Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <script>
        let selectedFormat = 'pdf';

        document.querySelectorAll('.format-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.format-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                // Determine format
                if (this.innerHTML.includes('PDF')) {
                    selectedFormat = 'pdf';
                } else {
                    selectedFormat = 'png';
                }
            });
        });

        async function startDownload() {
            const btn = document.querySelector('.download-btn');
            const originalText = btn.innerHTML;
            const card = document.getElementById('idCardContent');
            
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            btn.style.opacity = '0.8';
            btn.style.cursor = 'not-allowed';

            try {
                // Ensure images are loaded
                const images = card.getElementsByTagName('img');
                for (let img of images) {
                    if (!img.complete) await new Promise(resolve => img.onload = resolve);
                }

                const canvas = await html2canvas(card, {
                    scale: 3, // Higher quality
                    useCORS: true,
                    backgroundColor: null
                });

                if (selectedFormat === 'png') {
                    const link = document.createElement('a');
                    link.download = `Student_ID_<?php echo $s_id; ?>.png`;
                    link.href = canvas.toDataURL('image/png');
                    link.click();
                } else {
                    const { jsPDF } = window.jspdf;
                    const pdf = new jsPDF('p', 'mm', [320 * 0.264583, 500 * 0.264583]); // Convert px to mm (approx)
                    const imgData = canvas.toDataURL('image/png');
                    pdf.addImage(imgData, 'PNG', 0, 0, 320 * 0.264583, 500 * 0.264583);
                    pdf.save(`Student_ID_<?php echo $s_id; ?>.pdf`);
                }

                btn.innerHTML = '<i class="fas fa-check"></i> Downloaded!';
                btn.style.background = '#16a34a';
                
                setTimeout(() => {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                    btn.style.background = 'var(--primary)';
                    btn.style.opacity = '1';
                    btn.style.cursor = 'pointer';
                }, 2000);

            } catch (err) {
                console.error('Download error:', err);
                alert('Failed to generate file. Please try again.');
                btn.disabled = false;
                btn.innerHTML = originalText;
                btn.style.opacity = '1';
                btn.style.cursor = 'pointer';
            }
        }
    </script>
</body>

</html>
