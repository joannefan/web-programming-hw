<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>Timetable</title>
        <style>
            p {
                padding: 20px;
                font-family: Arial, Helvetica, sans-serif;
                line-height: 1.8em;
            }
        </style>
    </head>
    <body>
        <?php
            $n = $_GET["n"];

            echo "<p>";

            $x = 1;
            while($x <= 12) {
                echo "$x x $n = " . ($x * $n) . "<br />";
                $x++;
            }

            echo "</p>";
        ?>
    </body>
</html>
