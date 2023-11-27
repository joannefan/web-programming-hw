<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>Message Confirmation</title>
    <link href="styles.css" rel="stylesheet" type="text/css" />
    <style>
        body {
            background-color: #FAE7C6;
        }
        .banner {
            display: block;
            margin: 0 auto;
            background-color: #a6cfd5;
            height: 250px;
            text-align: center;
        }
        img {
            margin-top: 20px;
            margin-bottom: 10px;
            height: 150px;
        }
        h1 {
            display: block;
            margin: 0 auto;
            width: 80%;
            font-size: 34px;
            text-align: center;
            color: #2a3d45;
        }
        p {
            display: block;
            margin: 0 auto;
            padding-top: 20px;
            width: 100%;
            max-width: 410px;
            text-align: center;
            font-size: 24px;
            color: #2a3d45;
        }
    </style>
</head>

<body>
<?php
    echo "<div class='banner'><div id='banner-overlay'><img src='contact-icon.png' alt='mail icon'><h1>Message Received!</h1></div></div>";
    echo "<p>Hey " . $_POST["first"] . ",<br /><br />Thank you for reaching out.<br />We received your message and will respond within 2 business days.</p>";
?>
</body>
</html>
