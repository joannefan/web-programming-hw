<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>Office Hours</title>
        <style>
            body {
                text-align: center;
                background-color: #f1fee4;
            }
            div {
                display: inline-block;
                line-height: 1.8em;
            }
            .hours-container {
                text-align: center;
                font-size: 22px;
            }
            .day {
                width: 100px;
                color: #334d4d;
                text-align: left;
            }
            .hours {
                width: 150px;
                color: #669999;
                font-style: italic;
            }
            h1 {
                font-size: 30px;
                color: #336600;
                text-align: center;
                margin-top: 60px;
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>
        <?php
            $allHours = array(
                'Monday' => '9am - 5pm', 
                'Tuesday' => '9am - 5pm',
                'Wednesday' => '9am - 5pm',
                'Thursday' => '9am - 4pm',
                'Friday' => '9am - 3pm',
                'Saturday' => '10am - 2pm',
                'Sunday' => 'Closed'
            );

            echo "<h1>Office Hours</h1><div class='hours-container'>";
            foreach($allHours as $day => $hours) {
                echo("<div class='day'>$day:</div><div class='hours'>$hours</div><br />");
            }
            echo "</div>";
        ?>
    </body>
</html>
