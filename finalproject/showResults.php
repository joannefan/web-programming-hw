<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $first_name = $_POST['fname'];
    $last_name = $_POST['lname'];
    $email = $_POST['email'];
    $card = $_POST['card'];
    $package = $_POST['package'];

    $data_map = array();

    foreach ($_POST as $key => $value) {
        $data_map[$key] = $value;
    }
} else {
    // redirect if accessed directly without submitting the form
    // header('Location: tripForm.html');
    // exit();
}

//storing the required for data
//read data from the form
// $servername = "localhost";
// $username = "us9rh6dk7loi9";
// $password = "5m2Dm^1xo^2D";
// $dbname = "dbwalyrbstfogx";

// // Create connection
// $conn = new mysqli($servername, $username, $password, $dbname);
// // Check connection
// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }

// //store data in database
// $first_name =  $_REQUEST['first'];
// $last_name = $_REQUEST['last'];
// $email = $_REQUEST['email'];
// $card = $_REQUEST['cnum'];
// $package = $_REQUEST['package'];

// // Performing insert query execution
// $sql = "INSERT INTO users  VALUES ('$first_name', 
//         '$last_name','$email','$card', '$package', 'hello')";
        
// if (!mysqli_query($conn, $sql)){
//     echo "ERROR: Sorry $sql. " . mysqli_error($conn);
// }
// $conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Results</title>
    <script src="https://code.jquery.com/jquery-3.7.1.slim.js" integrity="sha256-UgvvN8vBkgO0luPSUl2s8TIlOSYRoGFAX4jlCIm9Adc=" crossorigin="anonymous"></script>
    <style type="text/css">
        body {
            width: 100%;
            background-color: #1C6B80;
            font-family:'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
        }
        h1 {
            margin-top: 50px;
            color: #FFA85C;
            text-align: center;
        }
        h2 {
            margin: 10px 0px;
            color: #1C6B80;
            text-align: center;
            width: 100%;
        }


        #cats-container {
            display: block;
            width: 100%;
            margin: 0 auto;
            padding: 0px;
        }
        .catResults {
            width: 100%;
            padding: 10px 0px 30px 0px;
            margin: 0px;
            margin-bottom: 80px;
        }
        .catResults input[type='button'] {
            padding: 10px; /* Adjust the padding as needed */
            display: inline-block;
            border-radius: 0; 
            border: 1px solid grey;
            background-color: white;
        }
        .catResults input[type='button']:hover {
            cursor: pointer;
        }

        .panes {
            display: flex;
            flex-wrap: wrap;
            font-size: 20px;
            width: 100%;
            margin: 0 auto;
            overflow: scroll;
            overflow-x: hidden;
            height: 400px;
        }
        #activity-container {
            background-color: #79ADC0;
        }
        #commercial-container {
            background-color: #79ADC0;
        }
        #natural-container {
            background-color:#79ADC0;
        }
        #cultural-container {
            background-color: #79ADC0;
        }
        #entertain-container {
            background-color: #79ADC0;
        }
        /* hotels */
        #hotels {
            display: block;
            width: 100%;
            margin: 0 auto;
            padding: 0px;
        }
        #hotel-container {
            background-color: #79ADC0;
            width: 100%;
            padding: 10px 0px 30px 0px;
            margin: 0px;
            display: block;
            margin-bottom: 80px;
        }
        /* flights */
        #flights {
            display: block;
            width: 100%;
            margin: 0 auto;
            padding: 0px;
        }
        #flight-container {
            background-color: #79ADC0;
            width: 100%;
            padding: 10px 0px 30px 0px;
            margin: 0px;
            margin-bottom: 80px;
            display: block;
        }

        .place-info {
            text-align: left;
            display: block;
            font-size: 18px;
            padding: 10px;
            padding-bottom: 0px;
            border-radius: 5px;
            line-height: 1.5em;
            margin: 6px;
            width: 300px;
            display: block;
            background-color: white;
            pointer-events: all;
        }

        a {
            text-decoration: none;
        }
        a:hover {
            cursor: pointer;
            color: #b6a75e;
        }
        ul {
            line-height: 1.8em;
            padding-left: 0px;
        }
        ul li {
            margin: 20px 0px;
            list-style: none;
            line-height: 1.3em;
        }
        .place-name {
            font-weight: bold;
        }
        b {
            font-family: 'Courier New', Courier, monospace;
        }

        /* tag icon with text for labeling results */
        .tag-container {
            position: relative;
            display: inline-block;
        }

        .tag-container img {
            width: 100px;
        }

        .tag-container .tag-text {
            position: absolute;
            top: 40%;
            left: 60%;
            transform: translate(-50%, -50%);
            color: black;
            font-size: 16px;
            text-align: center;
        }        
    </style>
</head>
<body>
    <div id="user-info"></div>

    <div id="flights">
        <div id='flight-container'>
            <h2>Flights you saved</h2>
            <div class="panes"></div>
        </div>
    </div>

    <div id="hotels">
        <div id='hotel-container'>
            <h2>Hotels you saved</h2>
            <div class="panes"></div>
        </div>
    </div>

    <div id="cats-container">
        <div class='catResults' id='activity-container'>
            <h2>Activities you saved</h2>
            <div class="panes"></div>
        </div>
    </div>
    <script>
        const savedPanes = <?php echo json_encode($data_map); ?>;

        const fname = savedPanes['fname'];
        const lname = savedPanes['lname'];
        const email = savedPanes['email'];
        const card = savedPanes['card'];
        const package = savedPanes['package'];
        console.log("first name is " + fname);
        
        const flightsArr = [];
        const hotelsArr = [];
        const activitiesArr = [];

        console.log(savedPanes);

        for (const key in savedPanes) {
            console.log(key);
            const value = savedPanes[key];

            if (key.startsWith("hotel")) {
                hotelsArr.push(value);
            } else if (key.startsWith("flight")) {
                flightsArr.push(value);
            } else if (key.startsWith("activity")) {
                activitiesArr.push(value);
            }
        }

        $(document).ready(function () {
            const userInfo = `<h1>Your Saved Trip Ideas</h1><h3>Package: ${package}</h3>`;
            $("#user-info").html(userInfo);

            if (flightsArr.length == 0) {
                $(`#flight-container .panes`).html("You didn't save any flights."); 
            } else {
                const flightsHtml = flightsArr.join('');
                $(`#flight-container .panes`).html(flightsHtml); 
            }

            if (hotelsArr.length == 0) {
                $("#hotel-container .panes").html("You didn't save any hotels."); 
            } else {
                const hotelsHtml = hotelsArr.join('');
                $("#hotel-container .panes").html(hotelsHtml);
            }

            if (activitiesArr.length == 0) {
                $("#activity-container .panes").html("You didn't save any activities."); 
            } else {
                const activitiesHtml = activitiesArr.join('');
                $("#activity-container .panes").html(activitiesHtml); 
            }
        });
    </script>
</body>
</html>