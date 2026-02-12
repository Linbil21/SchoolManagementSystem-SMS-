<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Help & FAQs</title>
    <link rel="icon" type="image/x-icon" href="../../Image/logo.png">
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
            max-width: 900px;
            /* Narrower for reading */
            margin: 0 auto;
            width: 100%;
        }

        .page-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 10px;
        }

        .search-hero {
            position: relative;
            max-width: 600px;
            margin: 0 auto;
        }

        .search-hero input {
            width: 100%;
            padding: 18px 25px 18px 50px;
            border-radius: 50px;
            border: none;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            font-size: 1rem;
            outline: none;
        }

        .search-hero i {
            position: absolute;
            left: 20px;
            top: 20px;
            font-size: 1.1rem;
            color: #94a3b8;
        }

        .faq-section {
            margin-bottom: 30px;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .faq-item {
            border-bottom: 1px solid #f1f5f9;
        }

        .faq-item:last-child {
            border-bottom: none;
        }

        .faq-question {
            padding: 20px 25px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
            color: #1e293b;
            transition: background 0.2s;
        }

        .faq-question:hover {
            background: #f8fafc;
        }

        .faq-question.active {
            color: var(--primary);
        }

        .faq-question i {
            font-size: 0.8rem;
            transition: transform 0.3s;
            color: #94a3b8;
        }

        .faq-question.active i {
            transform: rotate(180deg);
            color: var(--primary);
        }

        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
            background: #f8fafc;
        }

        .faq-answer p {
            padding: 20px 25px;
            color: #64748b;
            line-height: 1.6;
            font-size: 0.95rem;
            margin: 0;
        }

        .support-contact {
            text-align: center;
            margin-top: 50px;
            padding: 40px;
            background: #eff6ff;
            border-radius: 24px;
        }

        .support-contact h3 {
            color: #1e293b;
            margin-bottom: 10px;
        }

        .chat-btn-support {
            background: var(--primary);
            color: white;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 12px;
            font-weight: 600;
            display: inline-block;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <?php include '../../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../../Components/Header.php'; ?>
        <div class="content-area">
            <div class="page-header">
                <h1 class="page-title">How can we help you?</h1>
                <div class="search-hero">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search for answers...">
                </div>
            </div>

            <div class="faq-section">
                <!-- FAQ 1 -->
                <div class="faq-item">
                    <div class="faq-question">
                        How do I enroll in subjects?
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>To enroll, go to the <strong>Enrollment</strong> module in the sidebar, click on "Subject
                            Selection", choose your desired block or subjects, and proceed to assessment.</p>
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="faq-item">
                    <div class="faq-question">
                        How can I view my grades?
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Grades are typically released at the end of every semester. You can view them by navigating
                            to your Dashboard or Profile section once the Registrar has published them.</p>
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="faq-item">
                    <div class="faq-question">
                        What payment methods are accepted?
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>We accept payments via Bank Transfer (BDO, Landbank), GCash, and Over-the-Counter payments at
                            the cashier's office. Make sure to upload your receipt in the Payments module.</p>
                    </div>
                </div>

                <!-- FAQ 4 -->
                <div class="faq-item">
                    <div class="faq-question">
                        I lost my ID, what should I do?
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Go to the <strong>Student ID</strong> module and select "Replacement Request". Fill out the
                            form and submit an affidavit of loss. A replacement fee will apply.</p>
                    </div>
                </div>
            </div>

            <div class="support-contact">
                <h3>Still have questions?</h3>
                <p style="color: #64748b;">Can't find the answer you're looking for? Our support team is here to help.
                </p>
                <a href="Messages.php" class="chat-btn-support">Chat with Support</a>
            </div>

        </div>
    </div>

    <script>
        const faqs = document.querySelectorAll('.faq-question');

        faqs.forEach(faq => {
            faq.addEventListener('click', () => {
                const answer = faq.nextElementSibling;

                // Toggle active class
                faq.classList.toggle('active');

                // Toggle Max Height for slide effect
                if (answer.style.maxHeight) {
                    answer.style.maxHeight = null;
                } else {
                    answer.style.maxHeight = answer.scrollHeight + "px";
                }
            });
        });
    </script>
</body>

</html>