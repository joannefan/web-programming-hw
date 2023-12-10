<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $destinationCity = $_POST["destination"]; 
    $originCity = $_POST["origin"]; 
    $departDate = $_POST["departureDate"];
    $returnDate = $_POST["returnDate"];

    if (isset($_POST['maxDist']) && !empty($_POST['maxDist'])) {
        $distanceLimit = $_POST['maxDist'];
    } else {
        $distanceLimit = 6.5;
    }

    $wheelchair = isset($_POST['accessibility']) ? $_POST['accessibility'] : '';
    $wifi = isset($_POST['wifi']) ? $_POST['wifi'] : '';

    // get the array of activity categories
    $catsWanted = [];
    if (isset($_POST["activity"])) {
        foreach ($_POST["activity"] as $activityCat) {
            $catsWanted[] = $activityCat;
        }
    }

    // if food category is selected, also check if user provided dietary restrictions
    $diet = [];
    if (in_array("catering", $catsWanted)) {
        if (isset($_POST["diet"])) {
            foreach ($_POST["diet"] as $dietType) {
                $diet[] = $dietType;
            }
        }
    }

    $first_name = $_POST['fname'];
    $last_name = $_POST['lname'];
    $email = $_POST['email'];
    $card = $_POST['card'];
    $package = $_POST['package'];
} else {
    // redirect if accessed directly without submitting the form
    header('Location: tripForm.html');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Build Your Trip</title>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
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
            display: none; /* not shown unless user chose the category in the form */
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
        #catering-container {
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

        form {
            display: block;
            margin-bottom: 10px;
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

        .heartBtn {
            background-color: #BAD5DE;
            display: inline-block;
            padding: 2px 20px;
            cursor: pointer;
            color: black;
            text-align: center;
        }

        /* .heartBtn:hover {
            background-color: pink;
        }

        .liked:hover {
            background-color: pink;
        } */

        #done-submit {
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

    <h1>Here's what we found for you!</h1><br />

    <div id="flights">
        <div id='flight-container'>
            <h2>FLIGHTS THERE</h2>
            <div class="panes"></div>
        </div>
    </div>

    <div id="hotels">
        <div id='hotel-container'>
            <h2>HOTELS</h2>
            <div class="panes"></div>
        </div>
    </div>

    <h1>Activities</h1>
    <div id="cats-container">
        <div class='catResults' id='catering-container'>
            <h2>FOOD & DRINK</h2>
            <form id="catering-form" action="#" method="#">&nbsp; &nbsp;Filter results: </form>
            <div class="panes"></div>
        </div>

        <div class='catResults' id='cultural-container'>
            <h2>CLASSIC TOURISM</h2>
            <form id="cultural-form" action="#" method="#">&nbsp; &nbsp;Filter results: </form>
            <div class="panes"></div>
        </div>

        <div class='catResults' id='commercial-container'>
            <h2>SHOPPING</h2>
            <form id="commercial-form" action="#" method="#">&nbsp; &nbsp;Filter results: </form>
            <div class="panes"></div>
        </div>

        <div class='catResults' id='natural-container'>
            <h2>NATURE</h2>
            <form id="natural-form" action="#" method="#">&nbsp; &nbsp;Filter results: </form>
            <div class="panes"></div>
        </div>
        
        <!-- <form id="entertain-form" action="#" method="#">
            <h4>More Fun</h4>
        </form>
        <div class='catResults' id='entertain-container'>
            <h2>Other Entertainment</h2>
            <div class="panes"></div>
        </div> -->
    </div>

    <form id="done-form" action="showResults.php" method="POST">
        <input type="submit" id="done-submit" value="I'm done!">
    </form>
</body>

<script>
    const fname = <?php echo json_encode($first_name); ?>;
    const lname = <?php echo json_encode($last_name); ?>;
    const email = <?php echo json_encode($email); ?>;
    const card = <?php echo json_encode($card); ?>;
    const package = <?php echo json_encode($package); ?>;
    const cityName = <?php echo json_encode($destinationCity); ?>;

    main(); 
    
    async function main(){
        var coordinates = await hotelCity(); 

        showActivities(coordinates);

        var departCity = await getLocation(<?php echo json_encode($originCity); ?>);
        var arriveCity = await getLocation(cityName);
        searchFlights(departCity, arriveCity);
    }

    /**
     * formats the given amount as a currency string.
     * 
     * @param {number} amount - a numeric amount to display as currency.
     * @throws - will throw type error if amount is not a number.
     * @return {string} - amount padded with up to 2 trailing zeros.
     */
    function currencyStr(amount) {
        if (typeof amount !== "number") {
            throw new TypeError("amount cannot be formatted as currency");
        }

        // round to 2 decimal places
        let rounded = Math.round(amount * 100) / 100;
        
        // if whole number, add two trailing zeros
        if (Math.round(amount) - rounded === 0) {
            rounded += ".00";
        } else { // if only one decimal place (eg, 2.5), add a trailing 0
            rounded += "";
            if (rounded.split(".")[1].length === 1) {
                rounded += "0";
            }
        }

        return rounded;
    }

    async function hotelCity() {
         city = <?php echo json_encode($destinationCity); ?>;
         console.log("city is: " + city);
         const options = {
         method: 'GET',
         url: 'https://priceline-com-provider.p.rapidapi.com/v1/hotels/locations',
         params: {
             name: city,
             search_type: 'CITY'
         },
         headers: {
             'X-RapidAPI-Key': '279c854fdbmsheb57c9c292c7a83p14f3e9jsn01a09f1170f4',
             'X-RapidAPI-Host': 'priceline-com-provider.p.rapidapi.com'
         }
         };
         try {
            const response = await axios.request(options);
            const cityID = response.data[0].cityID;

            console.log("hotel city id: " + cityID); 
            displayHotel(cityID);

            return {
                lat: response.data[0].lat,
                lon: response.data[0].lon
            };
         } catch (error) {
            console.error(error);
         }
     }

    async function displayHotel(cityID){
         const checkIn = <?php echo json_encode($departDate); ?>;
         const checkOut = <?php echo json_encode($returnDate); ?>;

         const options = {
             method: 'GET',
             url: 'https://priceline-com-provider.p.rapidapi.com/v1/hotels/search',
             params: {
                 date_checkout: checkOut,
                 sort_order: 'PROXIMITY',
                 location_id: cityID,
                 date_checkin: checkIn,
             },
             headers: {
                'X-RapidAPI-Key': '279c854fdbmsheb57c9c292c7a83p14f3e9jsn01a09f1170f4',
                'X-RapidAPI-Host': 'priceline-com-provider.p.rapidapi.com'
             }
        };

        try {
            $("#hotel-container .panes").html("Loading hotels...");
            const response = await axios.request(options);
            // console.log(response); 

            const hotelsList = response.data.hotels;
            if (hotelsList == null){
                $("#hotel-container .panes").html("No hotel availability in the area on those dates. Try adjusting the dates.");
            } else {
                const numHotels = Math.min(hotelsList.length, 5);

                let allHotelsHtml = "";
                for(let i = 0; i < numHotels; i++){
                    const currHotel = hotelsList[i];
                    const address = currHotel.location.address;

                    let hotelHtml = `<div class='place-info'><ul><li class='place-name'>${currHotel.name}</li><li>Average rating (${currHotel.totalReviewCount} reviews): ${currHotel.overallGuestRating}</li><li>Minimum available price: ${currHotel.ratesSummary.minCurrencyCode + currHotel.ratesSummary.minPrice}</li><li>📍 ${address.addressLine1}, ${address.cityName}, ${address.countryName} ${address.zip}</li>`;

                    if (currHotel.media) {
                        hotelHtml += `<li><img src='${currHotel.media.url}' width='250px'></li>`;
                    }

                    const saveId = heartId;
                    heartId += 1;

                    allData.hotel.push({
                        id: `heart${saveId}`, 
                        content: $('<div/>').text(hotelHtml + "</ul></div>").html()
                    });

                    hotelHtml += `<div id="heart${saveId}" class="heartBtn">Save</div>`;
                    hotelHtml += "</ul></div>";

                    allHotelsHtml += hotelHtml;
                }

                $(`#hotel-container .panes`).html(allHotelsHtml); 
            }            
        } catch (error) {
            console.error(error);
        }
    }

    async function getLocation(city) {
        const options = {
            method: 'GET',
            url: 'https://priceline-com-provider.p.rapidapi.com/v1/flights/locations',
            params: {
                name: city, 
            },
            headers: {
                'X-RapidAPI-Key': '279c854fdbmsheb57c9c292c7a83p14f3e9jsn01a09f1170f4',
                'X-RapidAPI-Host': 'priceline-com-provider.p.rapidapi.com'
            },
        };  
        try {
            const response = await axios.request(options);
            return response.data[0].id;
        } catch (error) {
            console.error(error);
        }
    }

    async function searchFlights(depart, arrive) {
        // Get form data
        const departDate = <?php echo json_encode($departDate); ?>;
        const returnDate =  <?php echo json_encode($returnDate); ?>;

        const options = {
            method: 'GET',
            url: 'https://priceline-com-provider.p.rapidapi.com/community/v1/flights/search',
            params: {
                date_departure: departDate,
                location_departure: depart,
                class_type: 'ECO',
                sort_order: 'TRAVELTIME',
                itinerary_type: 'ROUND_TRIP',
                // itinerary_type: 'ONE_WAY',
                location_arrival: arrive,
                price_max: '20000',
                price_min: '10',
                number_of_stops: '1',
                date_departure_return: returnDate,
                number_of_passengers: '1',
                duration_max: '3000'
            },
            headers: {
                'X-RapidAPI-Key':'279c854fdbmsheb57c9c292c7a83p14f3e9jsn01a09f1170f4',
                'X-RapidAPI-Host': 'priceline-com-provider.p.rapidapi.com'
            }
            };
            try {
                $("#flight-container .panes").html("Loading flights...");
                const response = await axios.request(options);
                // console.log(response);
                if(response.data.listings == null){
                    $("#flight-container .panes").html("No flight availability on those dates. Try adjusting the dates or where you're flying from.");
                }
                else {
                    let allFlightsHtml = "";
                    const numFlights = Math.min(4, response.data.listings.length);

                    for (let i = 0; i < numFlights; i++){
                        const flightEntry = response.data.listings[i];
                        const segments = flightEntry.slices[0].segments;
                        const dTime = new Date(segments[0].departInfo.time.dateTime);
                        const formattedTime = dTime.toLocaleString('en-US', {
                            year: 'numeric',
                            month: 'short',
                            day: '2-digit',
                            hour: '2-digit',
                            minute: '2-digit',
                            timeZoneName: 'short'
                        });

                        console.log(flightEntry);

                        let flightHtml = `<div class='place-info'><ul><li class='place-name'>Airline: ${flightEntry.airlines[0].name}</li><li>Seat type: ${flightEntry.allFareBrandNames}</li><li>Seats available: ${flightEntry.seatsAvailable}</li><li>Price: USD${currencyStr(flightEntry.totalPriceWithDecimal.price)}</li><li>Layovers: ${segments.length - 1}</li><li>Departure: ${segments[0].departInfo.airport.name} at ${formattedTime}</li>`;

                        const saveId = heartId;
                        heartId += 1;

                        allData.flight.push({
                            id: `heart${saveId}`, 
                            content: $('<div/>').text(flightHtml + "</ul></div>").html()
                        });

                        flightHtml += `<div id="heart${saveId}" class="heartBtn">Save</div>`;
                        flightHtml += "</ul></div>";

                        allFlightsHtml += flightHtml;
                    }

                    $(`#flight-container .panes`).html(allFlightsHtml); 
                }                
            } catch (error) {
                console.error(error);
            }
        }

    
        // ACTIVITIES

        const cateringTypes = [
            "catering.restaurant",
            "catering.cafe"
        ];

        const cateringTags = {
            "catering.restaurant": 'Restaurant',
            "catering.cafe": 'Cafe', 
            "vegetarian": "Vegetarian",
            "vegan": "Vegan", 
            "halal": "Halal", 
            "kosher": "Kosher"
        };

        const commercialTags = {
            "commercial.supermarket": "Supermarket",
            "commercial.marketplace": "Marketplace",
            "commercial.shopping_mall": "Mall",
            "commercial.clothing": "Clothing",
            // "service.financial.bank%2Cservice.financial.atm": "Banks/ATM",
            // "public_transport": "Transport",
        };

        const naturalTags = {
            "natural.forest": "Forest",
            "natural.water": "Water",
            "natural.mountain": "Mountain",
            "leisure.park": "Park",
            // "natural": "Any nature"
        };

        const culturalTags = {
            "tourism.sights": "Tourist",
            "entertainment.culture": "Theatre/Art",
            "entertainment.museum": "Museum",
            "tourism.sights.place_of_worship": "Religious",
            "building.historic": "Historical"
        };

        // for extracting info from result places
        const basicFields = [
            "year_of_construction",
            "opening_hours",
            "phone",
            "website",
        ];

        const distMeters = 10000;
        const wheelchair = <?php echo json_encode($wheelchair); ?>;   // empty string means false
        const wifi = <?php echo json_encode($wifi); ?>;               // empty string means false
        const dietRestrictions = <?php echo json_encode($diet); ?>;
        const allCategories = <?php echo json_encode($catsWanted); ?>;

        var heartId = 0;
        var allData = {
            flight: [],
            hotel: [],
            activity: []
        };

        // console.log("city:" + cityName);
        // console.log("wheelchair: " + wheelchair + ", wifi: " + wifi);

        // FUNCTION DEFINITIONS
        function getCatTags(containerId) {
            switch (containerId) {
                case "catering-container":
                    return cateringTags;
                case "commercial-container":
                    return commercialTags;
                case "natural-container":
                    return naturalTags;
                case "cultural-container":
                    return culturalTags;
                default:
                    return {};
            }
        }

        function makeTag(text) {
            text = text.replace(/_/g, ' ');
            capitalized = text[0].toUpperCase() + text.slice(1);

            const html = `<div class='tag-container'><img src='images/tag-icon.png' alt='grey tag with text'><div class='tag-text'>${capitalized}</div></div>`;

            return html;
        }

        function makeLink(classname, url, text) {
            return `<a href='${url}' class='${classname}'>${text}</a>`;
        }

        function makeChkBox(name, id, val) {
            return `<input type='checkbox' name='${name}' id='${id}' value='${val}' />`;
        }

        function makeFilter(classname, id, val) {
            return `<input type='button' value='${val}' class='${classname}' id='${id}' />`;
        }

        function formatWebsite(url) {
            return makeLink("weblink", url, "🔗 Read more");
        }

        function formatPhone(num) {
            return makeLink("phonelink", "tel:" + num, "📞 Call us"); 
        }

        function formatHours(hoursStr) {
            let formatted = hoursStr.replace(/;/g, '<br />&nbsp;&nbsp;&nbsp;&nbsp;');
            formatted = formatted.replace(/, /g, '<br />&nbsp;&nbsp;&nbsp;&nbsp;');
            return "🕒 " + formatted;
        }

        function formatAddress(addressStr) {
            const addressLower = addressStr.toLowerCase();
            const cityLower = cityName.toLowerCase();
            const cityIdx = addressLower.indexOf(cityLower);

            if (cityIdx !== -1) {
                // truncate the address up to the city
                return "📍 " + addressStr.substring(0, cityIdx + cityLower.length);
            } else {
                // if the city is not found, return the full address
                return "📍 " + addressStr;
            }
        }

        function metersToKm(meters) {
            const km = meters / 1000;
            const rounded = Math.round(km * 100) / 100;
            return rounded;
        }

        function formatbasicFields(type, val) {
            switch(type) {
                case "phone":
                    return formatPhone(val);
                case "website": 
                    return formatWebsite(val);
                case "opening_hours":
                    return formatHours(val);
                case "year_of_construction":
                    return "Year built: " + val;
                default: 
                    return "";
            }
        }
    
        async function loadResults(container, categories, conditions, coordinates) {
            const containerId = `#${container}-container`;
            $(`${containerId} .panes`).html("Searching...");

            if (wheelchair !== "") {
                conditions.push(wheelchair);
            }
            if (wifi !== "") {
                conditions.push(wifi);
            }

            const conditionStr = conditions.length == 0 ? "" : `&conditions=${ conditions.join("%2C") }`;
            const catStr = categories.join("%2C");
            
            // a lot of nature entries don't have enough detail - no guarantee, but we can get some extra entries
            // const maxResults = containerId == "natural-container" ? 30 : 4;
            const maxResults = 10; // TODO make bigger
            const url = `https://api.geoapify.com/v2/places?categories=${catStr}${conditionStr}&filter=circle%3A${coordinates.lon}%2C${coordinates.lat}%2C${distMeters}&bias=proximity%3A${coordinates.lon}%2C${coordinates.lat}&limit=${maxResults}&apiKey=0b813d154863412cb86acd4b37d93c3b`;

            console.log(url);

            fetch(url)
                .then(res => res.text())
                .then(data => 
                {
                    const locations = JSON.parse(data);
                    locationsArr = locations.features;

                    let dataHTML = "";
                    for (const place of locationsArr) {
                        console.log(place);
                        const info = place.properties;
                        const placeCats = info.categories;

                        // missing key info
                        if (!("name" in info) || !("distance" in info)) {
                            continue;
                        }

                        // features of this place to display as tags
                        const allTags = getCatTags(`${container}-container`);
                        let matchingTags = [];

                        for (const key in allTags) {                            
                            if (placeCats.includes(key)) {
                                matchingTags.push(allTags[key]);
                            }
                        }

                        const tagsHTML = matchingTags.map(makeTag).join('');
                        let list = `<div class='place-info'><ul><li class='tags'>${tagsHTML}</li><li class='place-name'>${info.name}</li><li>${metersToKm(info.distance)} km away</li><li>${formatAddress(info.formatted)}</li>`; // this is the full address

                        const moreinfo = info.datasource.raw;

                        for (const key of basicFields) {
                            if (moreinfo.hasOwnProperty(key)) {
                                list += `<li>${formatbasicFields(key, moreinfo[key])}</li>`;
                            }
                        }

                        const saveId = heartId;
                        heartId += 1;

                        allData.activity.push({
                            id: `heart${saveId}`, 
                            content: $('<div/>').text(list + "</ul></div>").html()
                        });

                        list += `<div id="heart${saveId}" class="heartBtn">Save</div>`;
                        list += "</ul></div>";

                        dataHTML += list;
                    }

                    // no results found
                    if (dataHTML == "") {
                        dataHTML = "We couldn't find any matching results.";
                    }

                    $(`${containerId} .panes`).html(dataHTML); 
                })
            .catch (error => console.log(error));
        }

        function makeCategoryFilters(filterNames, category) {
            const formId = `#${category}-form`;
            const containerId = `#${category}-container`;

            filterNames.forEach((k, idx) => {
                const filter = makeFilter(`${category}Chk`, `chk${idx}${category}`, k);
                $(formId).append(filter);
            });

            $(`${formId} input[type='button']`).each(function() {
                makeClickEvent($(this), $(containerId), $(formId));
            });
        }

        function loadCategory(category, tags, tagDisplayNames, conditions, coordinates) {
            $(`#${category}-container`).css("display", "block");
            // create filters
            makeCategoryFilters(tagDisplayNames, category);

            // make API call to populate this category's results
            loadResults(category, tags, conditions, coordinates);
        }

        function makeClickEvent(button, catContainer, catForm) {
            button.click(function(event) {
                event.preventDefault();

                button.toggleClass('on');
                button.css('background-color', button.hasClass('on') ? '#FFDEC2' : 'white');

                // check which categories have been selected
                const categories = [];
                catForm.find("input[type='button']").each(function() {
                    if ($(this).hasClass('on')) {
                        const selectedFilter = $(this).val();
                        categories.push(selectedFilter);
                    }
                });

                // display only results that match at least one of the selected categories. 
                // If no filters are selected, show everything
                if (categories.length == 0) {
                    catContainer.find('.place-info').each(function() {
                        $(this).show();
                    })
                } else {
                    catContainer.find('.place-info').each(function() {
                        const tagDivs = $(this).find('.tag-text');

                        // Check if any tag-text div has text that is in the categories array
                        let hasMatchingCategory = false;
                        
                        tagDivs.each(function() {
                            const tagContent = $(this).text();
                            if (categories.includes(tagContent)) {
                                hasMatchingCategory = true;
                                return false; // break out of the loop since we found a match
                            }
                        });

                        // If this place does not have a matching category, hide it
                        if (hasMatchingCategory) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                }
            });
        }

        async function showActivities(coordinates) {
            console.log("my coordinates: " + coordinates);

            // food
            if (allCategories.includes("catering")) {
                console.log(dietRestrictions);
                loadCategory("catering", cateringTypes, Object.values(cateringTags), dietRestrictions, coordinates);
            }
            
            // commercial
            if (allCategories.includes("commercial")) {
                loadCategory("commercial", Object.keys(commercialTags), Object.values(commercialTags), [], coordinates);
            }

            // natural
            if (allCategories.includes("natural")) {
                loadCategory("natural", Object.keys(naturalTags), Object.values(naturalTags), [], coordinates);
            }

            // tourist
            if (allCategories.includes("cultural")) {
                loadCategory("cultural", Object.keys(culturalTags), Object.values(culturalTags), [], coordinates);
            }
        }

        $(document).on('click', '.heartBtn', function(event) {
            event.preventDefault();

            $(this).toggleClass('liked');

            if ($(this).hasClass('liked')) {
                $(this).css("background-color", "pink");
            } else {
                $(this).css("background-color", "#BAD5DE");
            }
        });

        // $('.heartBtn').hover(
        //     function() {
        //         if ($(this).css("background-color") == "#BAD5DE") {
        //             $(this).css("background-color", "pink");
        //         } else {
        //             $(this).css("background-color", "#BAD5DE");
        //         }
        //     },
        //     function() {
        //         if ($(this).css("background-color") == "#BAD5DE") {
        //             $(this).css("background-color", "pink");
        //         } else {
        //             $(this).css("background-color", "#BAD5DE");
        //         }
        //     }
        // );

        $(document).ready(function() {
            let hiddenInfo = `<input type='hidden' name='fname' value='${fname}'/>`
            + `<input type='hidden' name='lname' value='${lname}'/>`
            + `<input type='hidden' name='email' value='${email}'/>`
            + `<input type='hidden' name='card' value='${card}'/>`
            + `<input type='hidden' name='package' value='${package}'/>`;

            $("#done-form").append(hiddenInfo);

            $('#done-form').submit(function(event) {
                console.log(allData);

                for (const category in allData) {
                    allData[category].forEach(function(obj) {
                        const id = obj.id;
                        const content = obj.content;

                        // check if the element with that id has class 'liked'
                        if ($(`#${id}`).hasClass('liked')) {
                            let idNum = 0;

                            // get the number from the id
                            const match = id.match(/\d+/);
                            if (match) {
                                idNum = parseInt(match[0], 10);
                            }
                            
                            const currName = category + idNum;
                            console.log(currName);
                            console.log(content);
                        
                            $('#done-form').append(`<input type="hidden" name="${currName}" value="${content}">`);
                        }
                    });
                }

                return true;
            });
        });
 </script>
</html>


<!-- TODO: TypeError: currHotel.location is undefined -->