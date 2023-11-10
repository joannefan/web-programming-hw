<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order</title>
    <style type="text/css">
        body {
            font-family: 'Averia Serif Libre', serif;
            color: #334d4d;
            height: 100%;
            width: 100%;
            padding: 0px;
            margin: 0px;
        }
        /* header styling */
        header {
            height: 110px;
            background-color: #f7d199;
        }
        h1 {
            display: inline-block;
            margin-left: 40px;
            margin-top: 30px;
            font-size: 40px;
        }
        .hours-container {
            font-size: 18px;
            float: right;
            margin: 10px 30px 10px 0px;
        }
        .day {
            text-align: left;
            display: inline-block;
            line-height: 1.8em;
        }
        .hours {
            color: #4a8484;
            font-style: italic;
            display: inline-block;
            line-height: 1.8em;
        }
        /* form styling */
        form {
            display: block;
            padding-top: 20px;
            background-color: blanchedalmond;
        }
        table {
            width: 90%;
            margin: 0 auto;
            border-collapse: collapse;
        }
        .name-descript {
            font-size: 18px;
            font-style: italic;
        }
        .unit-price, .total-cost {
            font-size: 20px;
        }
        img {
            height: 200px;
            width: 200px;
        }
        h5 {
            margin: 0px;
            font-size: 22px;
            color: #4a8484;
            font-style: normal;
        }
        .client-info {
            padding: 50px 0px;
            width: 400px;
            margin: 0 auto;
            display: block;
        }
        label {
            display: inline-block;
            width: 100px;
            padding: 10px 0px;
            font-size: 18px;
        }
        input {
            font-family: 'Averia Serif Libre', serif;
            padding: 5px;
        }
        input[type="submit"] {
            margin: 0 auto;
            display: block;
            background-color: #efe6e0;
            color: #A87363;
            font-size: 20px;
            border-radius: 10px;
        }
        input[type="submit"]:hover {
            background-color: #7b5946;
            color: #efe6e0;
        }
        textarea {
            width: 200px;
            height: 100px;
        }
    </style>
</head>

<body>
<header>
    <h1>🦉 Two Owls Café</h1>
    <div class='hours-container'>HOURS <br />
        <div class='day'>Mon - Fri:</div>
        <div class='hours'>8:00 AM - 4:00 PM</div><br />
        <div class='day'>Sat - Sun:</div>
        <div class='hours'>9:00 AM - 2:00 PM</div>
    </div>
</html>

</header>

<form>
    <table border="0" cellpadding="3">
        <tr>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>Unit Price</th>
            <th>Quantity</th>
            <th>Total Cost</th>
        </tr>
<?php
// functions
function td($content, $className = "") {
    return "<td class='$className'>$content</td>";
}

function makeSelect($name, $minRange, $maxRange) {
	$result = "<select name='$name' size='1'>";
	for ($i = $minRange; $i <= $maxRange; $i++) {
        $result .= "<option>$i</option>";
    }
	return $result . "</select>";
}

function makeImg($filename, $alt) {
    $result = "<img src='images/$filename' alt='$alt'/>";
    return $result;
}


	
// connection info
$server = "localhost";
$userid = "uldx2rdrq1961";
$pw = "5u9qxtgljeft";
$db= "dbqhheflgmlha0";
		
// Create connection
$conn = new mysqli($server, $userid, $pw);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// echo "Connected successfully<br>";
	
// select the database
$conn->select_db($db);

// run a query
$sql = "SELECT * FROM menu";
$result = $conn->query($sql);

// get results
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        extract($row);
        $infoCells = td(makeImg($image, "placeholder"), 'itemImg') .
                    td("<h5>$name</h5><br />$description", 'name-descript') . 
                    td("$$price", 'unit-price') . 
                    td(makeSelect("qty$id", 0, 10), 'select-qty') .
                    td("hello", 'total-cost');
        
        echo "<tr>$infoCells</tr>";
    }
} 
else {
    echo "no results";
}
	
// close the connection	
$conn->close();

?>
    </table>
    <div class="client-info">
        <label>First Name* </label><input type='text' name='first' id="fname" /><br />
        <label>Last Name* </label><input type='text' name='last' id="lname" /><br />
        <label>Special Instructions </label><textarea id="message"></textarea><br />
        <input type="submit" value="Submit Order"/>
    </div>
</form>
</body>
</html>