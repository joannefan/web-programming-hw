<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>Fibonacci</title>
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
            if (isset($_GET['length'])) {
                $length = $_GET['length'];

                if (is_numeric($length) == false) {
                    http_response_code(404);
                    echo json_encode(['error' => 'value of "length" is not a number']);
                } else {
                    $first = 0;
                    $second = 1;
                    $sequence = array($first);

                    for ($len = 2; $len <= $length; $len++) {
                        $sequence[] = $second;
                        $sum = $first + $second;
                        $first = $second;
                        $second = $sum;
                    }
                
                    // header('Content-Type: application/json');
                    echo json_encode(['fibonacci' => $sequence]);
                }
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'query string is missing "length"']);
            }
        ?>
    </body>
</html>
