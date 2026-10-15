<?php
// contact.php
?>
<!DOCTYPE html>
<html lang="tl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - Raizeness Inventory Anime System</title>
    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: 'Comic Sans MS', 'Chalkboard SE', 'Fredoka One', cursive, sans-serif;
            background-image: url('Mario Wallpaper/NSMBUMushroomKingdom.png');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            color: #000;
        }

        .main-container {
            max-width: 900px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.92);
            border: 3px solid #0052cc;
            border-radius: 15px;
            padding: 25px 35px;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
        }

        /* Navigation Menu */
        .nav-menu {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .nav-btn {
            background-color: #0052cc;
            color: #ffffff;
            padding: 10px 20px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: bold;
            font-size: 16px;
            border: 2px solid #000;
            box-shadow: 2px 2px 0px #000;
        }

        .nav-btn:hover {
            background-color: #003d99;
        }

        .nav-btn.active {
            background-color: #ffcc00;
            color: #000;
        }

        .nav-btn.logout {
            background-color: #dc3545;
        }

        .system-title {
            text-align: center;
            font-size: 28px;
            font-weight: 900;
            color: #ff3333;
            text-shadow: 2px 2px 0px #ffeb3b;
            margin-bottom: 5px;
        }

        .system-subtitle {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .rainbow-line {
            height: 5px;
            background: linear-gradient(to right, #ff0000, #ff9900, #ffff00, #00ff00, #00ffff, #0000ff);
            border-radius: 3px;
            margin-bottom: 25px;
        }

        .section-title {
            font-size: 24px;
            font-weight: 900;
            color: #0052cc;
            margin-bottom: 12px;
        }

        .content-box {
            font-size: 17px;
            line-height: 1.6;
            color: #222;
        }

        /* GIF Styling */
        .mario-gif-container {
            text-align: center;
            margin: 20px 0;
        }

        .mario-gif {
            max-width: 100%;
            height: auto;
            border: 4px solid #ffcc00;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>

<div class="main-container">
    <div class="rainbow-line"></div>

    <!-- Makipag-ugnayan Section -->
    <div class="section-title">Makipag-ugnayan</div>

    <!-- Plumbing Van GIF -->
    <div class="mario-gif-container">
        <img src="GIFs/Super_Mario_Bros_Plumbing_Commercial.gif" alt="Super Mario Bros Plumbing Commercial" class="mario-gif">
    </div>

    <div class="content-box">
        <p style="line-height: 1.6;">
            Para sa mga katanungan o suporta sa sistema, maaari kang mag-abot sa mga sumusunod:
        </p>
        <p><b>Email:</b> reneemadis28@gmail.com and lealavieannrivera@gmail.com</p>
        <p><b>Lokasyon:</b> National University Baliwag</p>
    </div>

</div>

</body>
</html>