<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
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
            height: 100vh;
            display: flex;
            overflow: hidden;
            /* Important for chat layout */
        }

        .main-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .chat-container {
            display: flex;
            flex: 1;
            margin: 20px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }

        /* Sidebar - Chat List */
        .chat-sidebar {
            width: 300px;
            border-right: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
        }

        .chat-header {
            padding: 20px;
            border-bottom: 1px solid #e2e8f0;
        }

        .chat-header h2 {
            font-size: 1.2rem;
            color: #1e293b;
        }

        .search-box {
            margin-top: 15px;
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 10px 10px 10px 35px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #f8fafc;
        }

        .search-box i {
            position: absolute;
            left: 12px;
            top: 13px;
            color: #94a3b8;
            font-size: 0.8rem;
        }

        .chat-list {
            flex: 1;
            overflow-y: auto;
        }

        .chat-item {
            padding: 15px 20px;
            border-bottom: 1px solid #f1f5f9;
            cursor: pointer;
            display: flex;
            gap: 15px;
            transition: background 0.2s;
        }

        .chat-item:hover {
            background: #f8fafc;
        }

        .chat-item.active {
            background: #eff6ff;
            border-right: 3px solid var(--primary);
        }

        .avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: #e2e8f0;
            flex-shrink: 0;
            overflow: hidden;
        }

        .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .chat-info {
            flex: 1;
            overflow: hidden;
        }

        .chat-name {
            font-weight: 600;
            color: #1e293b;
            font-size: 0.95rem;
            margin-bottom: 3px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .chat-msg {
            font-size: 0.8rem;
            color: #64748b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .chat-time {
            font-size: 0.7rem;
            color: #94a3b8;
        }

        /* Main Chat Area */
        .chat-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #f8fafc;
        }

        .chat-area-header {
            padding: 15px 25px;
            background: white;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .chat-messages {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .message-bubble {
            max-width: 70%;
            padding: 12px 18px;
            border-radius: 18px;
            font-size: 0.95rem;
            line-height: 1.5;
            position: relative;
        }

        .message-income {
            align-self: flex-start;
            background: white;
            color: #1e293b;
            border-bottom-left-radius: 4px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .message-outgoing {
            align-self: flex-end;
            background: var(--primary);
            color: white;
            border-bottom-right-radius: 4px;
        }

        .message-time {
            font-size: 0.7rem;
            margin-top: 5px;
            opacity: 0.7;
            display: block;
            text-align: right;
        }

        .chat-input-area {
            padding: 20px;
            background: white;
            border-top: 1px solid #e2e8f0;
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .chat-input {
            flex: 1;
            padding: 12px 20px;
            border: 1px solid #e2e8f0;
            border-radius: 25px;
            background: #f8fafc;
            outline: none;
            transition: border 0.2s;
        }

        .chat-input:focus {
            border-color: var(--primary);
        }

        .send-btn {
            width: 45px;
            height: 45px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
        }

        .send-btn:hover {
            background: #1d4ed8;
        }
    </style>
</head>

<body>
    <?php include '../../Components/Sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include '../../Components/Header.php'; ?>

        <div class="chat-container">
            <!-- Sidebar -->
            <div class="chat-sidebar">
                <div class="chat-header">
                    <h2>Messages</h2>
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search messages...">
                    </div>
                </div>
                <ul class="chat-list">
                    <li class="chat-item active">
                        <div class="avatar"><img src="https://ui-avatars.com/api/?name=Admin+Office&background=random"
                                alt=""></div>
                        <div class="chat-info">
                            <div class="chat-name">Admin Office</div>
                            <div class="chat-msg">Please submit your missing requirements.</div>
                        </div>
                        <div class="chat-time">10:30 AM</div>
                    </li>
                    <li class="chat-item">
                        <div class="avatar"><img src="https://ui-avatars.com/api/?name=Guidance&background=random"
                                alt=""></div>
                        <div class="chat-info">
                            <div class="chat-name">Guidance Counselor</div>
                            <div class="chat-msg">Your interview schedule is confirmed.</div>
                        </div>
                        <div class="chat-time">Yesterday</div>
                    </li>
                    <li class="chat-item">
                        <div class="avatar"><img src="https://ui-avatars.com/api/?name=IT+Dept&background=random"
                                alt=""></div>
                        <div class="chat-info">
                            <div class="chat-name">IT Department</div>
                            <div class="chat-msg">Welcome to the College of CS/IT!</div>
                        </div>
                        <div class="chat-time">Oct 20</div>
                    </li>
                </ul>
            </div>

            <!-- Chat Area -->
            <div class="chat-area">
                <div class="chat-area-header">
                    <div class="avatar" style="width: 40px; height: 40px;"><img
                            src="https://ui-avatars.com/api/?name=Admin+Office&background=random" alt=""></div>
                    <div>
                        <h3 style="font-size: 1rem; color: #1e293b; margin: 0;">Admin Office</h3>
                        <span style="font-size: 0.8rem; color: #22c55e;">Online</span>
                    </div>
                </div>

                <div class="chat-messages">
                    <div class="message-bubble message-income">
                        Hello! We noticed you haven't submitted your Original Good Moral Certificate yet.
                        <span class="message-time">10:28 AM</span>
                    </div>
                    <div class="message-bubble message-income">
                        Please upload it as soon as possible to avoid enrollment delays.
                        <span class="message-time">10:29 AM</span>
                    </div>
                    <div class="message-bubble message-outgoing">
                        Hi! Yes, I will upload it later this afternoon. Thank you for the reminder.
                        <span class="message-time">10:30 AM</span>
                    </div>
                </div>

                <div class="chat-input-area">
                    <button
                        style="border: none; background: none; font-size: 1.2rem; color: #94a3b8; cursor: pointer;"><i
                            class="fas fa-paperclip"></i></button>
                    <input type="text" class="chat-input" placeholder="Type a message...">
                    <button class="send-btn"><i class="fas fa-paper-plane"></i></button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>