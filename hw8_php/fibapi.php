<?php
    if (isset($_GET['length'])) {
        $length = $_GET['length'];

        if (!is_numeric($length)) {
            http_response_code(404);
            echo json_encode(['error' => 'value of "length" is not a number']);
        } else {
            $length = (int)$length;
            $first = 0;
            $second = 1;
            $sequence = array();

            for ($len = 1; $len <= $length; $len++) {
                $sequence[] = $first;
                $sum = $first + $second;
                $first = $second;
                $second = $sum;
            }
        
            $result = json_encode(['length' => $length, 'nums' => $sequence]);
            echo $result;
        }
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'query string is missing "length"']);
    }
?>
