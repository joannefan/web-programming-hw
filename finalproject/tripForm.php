<?php
    $first_name = $_POST['first'];
    $last_name = $_POST['last'];
    $email = $_POST['email'];
    $card = $_POST['cnum'];
    $package = $_POST['package'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Build Your Trip</title>
    <script src="https://code.jquery.com/jquery-3.7.1.slim.js" integrity="sha256-UgvvN8vBkgO0luPSUl2s8TIlOSYRoGFAX4jlCIm9Adc=" crossorigin="anonymous"></script>
    <style>
        body {
            font-family: Roboto, sans-serif;
            /* margin: 20px; */
            text-align: center; 
            background-color: white; 
        }

        form {
            max-width: 600px;
            margin: 0 auto;
            /* border-style: solid;  */
            padding: 10px; 
            border-radius: 6px; 
            background-color: #c9ebff; 
            color: black; 
            /* background-color:rgb(170, 225, 255);  */
        }

        #basic-questions label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold; 
        }

        #basic-questions input, #basic-questions select {
            width: 100%;
            padding: 8px;
            margin-bottom: 16px;
            box-sizing: border-box;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        /* Activities section styling */
        #dietQuestion {
            display: none;
        }

        #activity-questions {
            text-align: left;
        }

        input[type=submit]{
            background-color: black; 
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

    </style>
</head>
<body>

  <h1>Build Your Perfect Trip</h1>
  <p>
    Just a few questions so we can find the perfect flights, hotels, and activities for you! 
  </p>

    <form id="general-form" onSubmit="return validate()" action="processTripForm.php" method="post">
        <div id="basic-questions">
            <label for="destination">Where are you going?</label>
            <input type="text" id="destination" name="destination" required placeholder="city, state OR city, country">

            <label for="origin">Where are you coming from?</label>
            <input type="text" id="origin" name="origin" required placeholder="city, state OR city, country">

            <label for="departureDate">When are you leaving?</label>
            <input type="date" id="departureDate" name="departureDate" required>

            <label for="returnDate">When are you coming back?</label>
            <input type="date" id="returnDate" name="returnDate">
        </div>

        <div id="activity-questions">
            <p>I want ideas for...</p>
            <input type="checkbox" id="chkCultural" name="activity[]" value="cultural">
            <label for="chkCultural">Cultural and Tourist Activities</label><br />

            <input type="checkbox" id="chkNature" name="activity[]" value="natural">
            <label for="chkNature">Nature Exploration</label><br />

            <input type="checkbox" id="chkCommercial" name="activity[]" value="commercial">
            <label for="chkCommercial">Shopping and Spending</label><br />

            <input type="checkbox" id="chkFood" name="activity[]" value="catering">
            <label id="foodLabel" for="chkFood">Food and Drink</label><br />

            <input type="checkbox" id="chkAll">
            <label for="chkAll">All the above</label><br />

            <div id="dietQuestion">
                <p>(optional) My dietary restrictions are...</p>
                <input type="checkbox" id="chkVegetarian" name="diet[]" value="vegetarian">
                <label for="chkVegetarian">Vegetarian</label><br />

                <input type="checkbox" id="chkVegan" name="diet[]" value="vegan">
                <label for="chkVegan">Vegan</label><br />

                <input type="checkbox" id="chkHalal" name="diet[]" value="halal">
                <label for="chkHalal">Halal</label><br />

                <input type="checkbox" id="chkKosher" name="diet[]" value="kosher">
                <label for="chkKosher">Kosher</label><br />
            </div>

            <p>(optional) Limit all places to...</p>
            <input type="checkbox" id="chkWheelchair" name="accessibility" value="wheelchair"/>
            <label for="chkWheelchair">Wheelchair-friendly places</label><br />

            <input type="checkbox" id="chkWifi" name="wifi" value="internet_access"/>
            <label for="chkWifi">Places with internet access</label><br />

        </div>
        <input type='submit' value="Build my trip!">
        <div class="input-err"></div>
</form>
</body>

<script>
    const fname = <?php echo json_encode($first_name); ?>;
    const lname = <?php echo json_encode($last_name); ?>;
    const email = <?php echo json_encode($email); ?>;
    const card = <?php echo json_encode($card); ?>;
    // const package = <?php echo json_encode($package); ?>;
    // console.log("package" + package); // TODO package is null

    const package = "basic";
    if (package == "basic") {
        $("#activity-questions").hide();
    } else if (package == "gold") {
        $("#chkFood").hide();
        $("#foodLabel").hide();
    }

    // if the "All the above" checkbox is clicked, check all the other activities checkboxes.
    $('#chkAll').on('click', function() {
        $('input[name="activity[]"]').prop('checked', this.checked);
    });

    // Toggle the visibility of the question about dietary restrictions
    // based on whether the chkFood checkbox is checked
    $('#chkFood').on('change', function() {
        $('#dietQuestion').toggle(this.checked);
    });

    function validate() {
        let err = "";

        // date validation
        const departDate = new Date($('#departureDate').val());
        const returnDate = new Date($('#returnDate').val());

        // get today's date (excluding time)
        const today = new Date().toISOString().split('T')[0];
        const departDayOnly = departDate.toISOString().split('T')[0];

        // validate departure date
        console.log(departDate);
        if (departDayOnly <= today) {
            err += "Departure date cannot be today. Please select another date in the future.<br />";
        }

        // validate return date
        if (returnDate < departDate) {
            err += 'Return date cannot be before the departure date. Please select a valid return date.';
        }

        // validate destination is not same as origin location
        const origin = $('#origin').val().trim().toLowerCase();
        const destination = $('#destination').val().trim().toLowerCase();
        if (origin == destination) {
            err += 'Origin and destination cannot be the same place. Please select different locations.';
        }

        if ($('input[name="activity[]"]:checked').length === 0) {
            err += 'Please select at least one category of activities to generate ideas for.<br />';
        }

        if (err !== "") {
            $(".input-err").html("Fix the following errors to continue:<br /><br />" + err);
            return false;
        }

        $(".input-err").html("");
    
        const hiddenInfo = `<input type='hidden' name='fname' value='${fname}'/>`
        + `<input type='hidden' name='lname' value='${lname}'/>`
        + `<input type='hidden' name='email' value='${email}'/>`
        + `<input type='hidden' name='card' value='${card}'/>`
        + `<input type='hidden' name='package' value='${package}'/>`;

        $("#general-form").append(hiddenInfo);
    
        return true;
    }
</script>
</html>
