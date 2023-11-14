<!-- 
    This php page displays a menu of food items and a form for the user to order.
 -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order</title>
    <link href="styles.css" rel="stylesheet" type="text/css" />
    <script src="https://code.jquery.com/jquery-3.7.1.slim.js" integrity="sha256-UgvvN8vBkgO0luPSUl2s8TIlOSYRoGFAX4jlCIm9Adc=" crossorigin="anonymous"></script>
    <style type="text/css">
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
            background-color: #7b5946;
            color: #efe6e0;
            font-size: 20px;
            border-radius: 10px;
        }
        input[type="submit"]:hover {
            background-color: #efe6e0;
            color: #A87363;
        }
        textarea {
            width: 200px;
            height: 100px;
        }
        #error {
            font-size: 18px;
            color: #b14444;
            padding: 20px 0px 40px 0px;
            text-align: center;
            line-height: 1.4em;
        }
    </style>
    <script>
        // validation functions
        /** 
         * checks that the user has ordered at least one item.
         * 
         * @return {boolean} - true if user ordered at least one item, else false.
         */
        function isValidOrder() {
            let allZeroQty = true;

            $('.select-qty select').each(function() {
                if ($(this).prop("selectedIndex") != 0) {
                    allZeroQty = false;
                    return false; // quit loop if there is an item in the order
                }
            });

            if (allZeroQty) {
                return false;
            }
            return true;
        }

        /** 
         * validates the form input (that at least one item is ordered and 
         * the user’s name is provided) and writes error messages if not.
         * 
         * @return {boolean} - true if order meets requirements, otherwise false.
         */
        function validate() {
            let err = "";

            if (!isValidOrder()) {
                err += "At least one item must be ordered.<br />";
            }

            // customer's first and last name are required
            reqFields = {
                fname: "First Name",
                lname: "Last Name"
            };
            for (const id in reqFields) {
                if (document.getElementById(id).value == "") {
                    err += reqFields[id] + " is required.<br />";
                }
            }

            if (err != "") {
                document.getElementById('error').innerHTML =
                    "Fix the following errors to continue:<br><br>" + err;
                return false;
            }
            return true;
        }
    </script>
</head>

<body>
<?php
// header
include 'header.php';

// functions related to building form:

/** 
 * creates a table data element with the given content and class name.
 * 
 * @param {string} content - the content to display in the td.
 * @param {string} className - the class to assign to the td.
 * @return {string} - a <td> with the given content and class.
 */
function td($content, $className = "") {
    return "<td class='$className'>$content</td>";
}

/** 
 * creates a select element with the given name and range of numeric values.
 * 
 * @param {string} name - the name attribute of the select.
 * @param {number} minRange - the minimum value of the select options.
 * @param {number} maxRange - the maximum value of the select options.
 * @return {string} - a <select> with the given settings.
 */
function makeSelect($name, $minRange, $maxRange) {
    $result = "<select name='$name' size='1'>";
    for ($i = $minRange; $i <= $maxRange; $i++) {
        $result .= "<option>$i</option>";
    }
    return $result . "</select>";
}

/** 
 * creates an img element with the given image filename and alt.
 * 
 * @param {string} filename - the image file's name.
 * @param {string} alt - value of the alt attribute.
 * @return {string} - an <img> with the given attributes.
 */
function makeImg($filename, $alt) {
    return "<img src='images/$filename' alt='$alt'/>";
}

/** 
 * creates an input of type hidden with the given name and value.
 * 
 * @param {string} name - the name attribute of the input element.
 * @param {string} val - the value attribute of the input element.
 * @return {string} - an <input> of type 'hidden' with the given attributes.
 */
function hiddenField($name, $val) {
    return "<input type='hidden' name='$name' value='$val'/>";
}

/** 
 * writes the beginning of the form and table to the page.
 */
function echoFormBeg() {
    echo <<<HTML
    <form onSubmit="return validate()" method="GET" action="process.php">
        <table border="0" cellpadding="3">
            <tr>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>Unit Price</th>
                <th>Quantity</th>
            </tr>
    HTML;
}

/** 
 * writes the end of the table and remaining form elements to the page.
 */
function echoFormEnd() {
    echo <<<HTML
        </table>
        <div class="client-info">
            <label>First Name* </label><input type='text' name='first' id="fname" /><br />
            <label>Last Name* </label><input type='text' name='last' id="lname" /><br />
            <label>Special Instructions </label><textarea id="message"></textarea><br />
            <input type="submit" value="Submit Order"/>
            <div id="error">&nbsp;</div>
        </div>
    </form>
    HTML;
}

// connection info
$server = "localhost";
$userid = "uldx2rdrq1961";
$pw = "5u9qxtgljeft";
$db= "dbqhheflgmlha0";

// create and check connection
$conn = new mysqli($server, $userid, $pw);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// select the database and run query
$conn->select_db($db);
$sql = "SELECT * FROM menu";
$result = $conn->query($sql);

// read results
if ($result->num_rows > 0) {
    // write form beginning
    echoFormBeg();

    // for each row from the db, write the data as a tr element
    while($row = $result->fetch_assoc()) {
        extract($row);
        $infoCells = td(makeImg($image, "placeholder"), 'itemImg') .
                    td("<h5>$name</h5><br />$description", 'name-descript') . 
                    td("$$price", 'unit-price') . 
                    td(makeSelect("qty$id", 0, 10), 'select-qty');
        
        echo "<tr>$infoCells</tr>";
        
        // hidden fields to send the names and unit prices of menu items
        $hidden = hiddenField("name$id", $name) . hiddenField("price$id", $price);
        echo $hidden;
    }
} else {
    echo "Data not found.";
}

$conn->close();

// write form ending
echoFormEnd();
?>
</body>
</html>