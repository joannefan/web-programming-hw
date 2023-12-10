<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <title>Catalog</title>
    <link href="serviceStyle.css" rel="stylesheet" type="text/css" />
    <style>
        input[type=text] {
        width: 25%;
        margin-bottom: 20px;
        padding: 12px;
        border: 1px solid #ccc;
        border-radius: 3px;
        }

        .icon-container {
        margin-bottom: 20px;
        padding: 7px 0;
        font-size: 24px;
        } 

        .btn {
        background-color: rgb(36, 125, 155);
        color: white;
        padding: 12px;
        margin: 10px 0;
        border: none;
        width: 100%;
        border-radius: 3px;
        cursor: pointer;
        font-size: 17px;
        }

        .btn:hover {
        background-color: rgb(65, 153, 183);
        }

        .box{
            display: block;
            text-align: center;
            position: relative;
            box-sizing: border-box;
            background-color: rgb(115, 184, 208);
            color: rgb(255, 255, 255);
            float: left;
            width: 30%;
            height: 500px;
            margin: 5px;
            font-size: 22px;
        }

        .payBox{
            display: block;
            text-align: center;
            position: absolute;
            box-sizing: border-box;
            background-color: rgb(115, 184, 208);
            color: rgb(255, 255, 255);
            float: left;
            width: 75%;
            margin: 5px;
            font-size: 22px;
        }
    </style>
    <script>
        //card validation functions from w3resources
        //visa
        function validateVisa(cardNum)
        {
            var cardno = /^(?:4[0-9]{12}(?:[0-9]{3})?)$/;
            if(cardNum.match(cardno)){
                return true;
            }
            else{
                return false;
            }
        }

        //discover
        function validateDiscover(cardNum)
        {
            var cardno = /^(?:6(?:011|5[0-9][0-9])[0-9]{12})$/;
            if(cardNum.match(cardno)){
                return true;
            }
            else{
                return false;
            }
        }

        //american express
        function validateAE(cardNum)
        {
            var cardno = /^(?:3[47][0-9]{13})$/;
            if(cardNum.match(cardno)){
                return true;
            }
            else{
                return false;
            }
        }
        //master card
        function validateMaster(cardNum)
        {
            var cardno = /^(?:5[1-5][0-9]{14})$/;
            if(cardNum.match(cardno)){
                return true;
            }
            else{
                return false;
            }
        }

        function validatePhone() {
            const phone = document.getElementById("phone").value;
            if (phone == "") {
                return true;
            } else {
                return phone.match(/^\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/);
            }
        }

        function validateEmail() {
            const email = document.getElementById("email").value;
            return email.match(/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i);
        }

        function validate() {
            reqFields = {
                fname: "First Name",
                lname: "Last Name",
                email: "Email",
                cnum: "Card",
            };
            let err = "";
            for (const id in reqFields) {
                if (document.getElementById(id).value == "") {
                    err += reqFields[id] + " is required.<br />";
                }
            }

            if (!validatePhone()) {
                err += "Invalid phone number.<br />";
            }
            if (!validateEmail()) {
                err += "Email format does not match <em>username@domain.tld</em><br />";
            }

            //checking the credit card num depending on type of card used
           if (document.getElementById("visa").checked){
                if (!validateVisa(document.getElementById("cnum").value)){
                    err += "Not a valid Visa credit card number";
                }
           }
           else if (document.getElementById("discover").checked){
                if(!validateDiscover(document.getElementById("cnum").value)){
                    err += "Not a valid Discover credit card number";
                }
           }
           else if (document.getElementById("mastercard").checked){
                if(!validateMaster(document.getElementById("cnum").value)){
                    err += "Not a valid MasterCard credit card number";
                }
           }
           else if (document.getElementById("AE").checked){
                if(!validateAE(document.getElementById("cnum").value)){
                    err += "Not a valid American Eagle credit card number";
                }
           }
           else{
            err += "Please select a payment type"
           }

            if (err != "") {
                document.getElementById('error').innerHTML =
                    "Fix the following errors to continue:<br><br>" + err;
                return false;
            }
            //if no errors
            if (err == ""){
                document.getElementById('error').innerHTML = "";
                alert("Thanks for purchasing!");
                return true;
            }
        }

        //function to make the purchase pop up
        function purchase(name, cost){
            document.getElementById("catalog").style.display = 'none';
            document.getElementById("payBox").style.display = 'block';
            document.getElementById("packageType").innerHTML = "Package: "+name;
            document.getElementById("total").innerHTML = "Total: $"+cost;
        }

        //function to go back to packages
        function back(){
            document.getElementById("catalog").style.display = 'block';
            document.getElementById("payBox").style.display = 'none';
        }
    </script>
</head>

<body>


    <div id="catalog-backdrop"></div>
    <div class="banner-text">
        Catalog
    </div>

    <div class="catalog-center" id="catalog">
        <div class="box" onclick="purchase('package1', 0)" id ="box1">
            Basic Option
            <br><br>
            Our Basic Option makes planning logistics easy. You will get information about flights and hotels for
            your desired destination for free!
            <br><br>
            Cost: FREE
        </div>
        <div class="box" onclick="purchase('package2', 80)" id="box2">
            Gold Option
            <br><br>
            Our Gold Option adds events for you do at your destination. You will get information about flights, hotels, and some locations!
            
            <br><br>
            Cost: $
        </div>
        <div class="box" onclick="purchase('package3', 150)" id="box3">
            Premium Option
            <br><br>
            Our Premium Option provides the most user customization. You will get information about flights, hotels, locations, and best of all food! 
            We will have your perfect vacation planned prefectly and easy, from the flights and hotels to the restuarants and
            attractions you will visit!
            <br><br>
            Cost: $
        </div>
    </div>
    <div id="payBox" class="payBox" style="display:none">
        <div class="row">
            <div class="col-75">
              <div class="container">
                <form action = "tripForm.php" onSubmit="return validate()" method="POST">
                    <h3>Billing Info</h3>
                    Order Summary: 
                    <p id="packageType">Package:</p>
                    <p id="total">Total: $</p>
                    <h3>Billing Info</h3>
                    <label>First Name* </label><input type='text' name='first' id="fname"/>
                    <label>Last Name* </label><input type='text' name='last' id="lname"/><br/>
                    <label>Email* </label><input type='text' name='email' id="email"/>
                    <label>Phone Number </label><input type='text' name='phone' id="phone" /><br/>
          
                    <div class="col-50">
                      <h3>Payment</h3>
                      <label for="fname">Accepted Cards</label>
                      <div class="icon-container">
                        <input type="radio" id="visa" name="cardType" value="Visa">
                            <label for="visa">Visa</label>
                            <input type="radio" id="discover" name="cardType" value="discover">
                            <label for="discover">Discover</label>
                            <input type="radio" id="mastercard" name="cardType" value="mastercard">
                            <label for="mastercard">MasterCard</label>
                            <input type="radio" id="AE" name="cardType" value="AE">
                            <label for="AE">American Express</label>
                      </div>
                      <label for="cname">Name on Card</label>
                      <input type="text" id="cname" name="cardname" >
                      <label for="ccnum">Credit card number *</label>
                      <input type="text" name='cnum' id="cnum" ><br>
                      <label for="expmonth">Exp Month</label>
                      <input type="text" id="expmonth" name="expmonth" >
                          <label for="expyear">Exp Year</label>
                          <input type="text" id="expyear" name="expyear" ><br>
                          <label for="cvv">CVV</label>
                          <input type="text" id="cvv" name="cvv" >
                      </div>
                    </div>
                  </div>
                  <input type="submit" value="Checkout" class="btn">
                  <input value="back" class="btn" onclick="back()">
                  <div id="error">&nbsp;</div>
                </form>
              </div>
            </div>
            </div>
          </div>
    </div>
</body>
</html>