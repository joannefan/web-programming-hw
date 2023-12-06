<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $city = $_POST['desired-place'];
    $distanceLimit = $_POST['maxDist'];
    $wheelchair = isset($_POST['accessibility']) ? $_POST['accessibility'] : '';
    $wifi = isset($_POST['wifi']) ? $_POST['wifi'] : '';
} else {
    // Redirect if accessed directly without submitting the form
    header('Location: index.html');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Travel Ideas</title>
    <meta charset="utf-8" />
    <script src="https://code.jquery.com/jquery-3.7.1.slim.js" integrity="sha256-UgvvN8vBkgO0luPSUl2s8TIlOSYRoGFAX4jlCIm9Adc=" crossorigin="anonymous"></script>
    <style type="text/css">
        body {
            width: 100%;
            background-color: white;
            font-family:'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
        }
        h1 {
            margin-top: 50px;
            color: black;
            text-align: center;
        }
        h2 {
            margin: 10px 0px;
            color: grey;
            text-align: center;
            width: 100%;
        }
        .input-err {
            display: block;
            font-size: 18px;
            color: black;
        }
        #cats-container {
            display: block;
            width: 100%;
            margin: 0 auto;
            padding: 0px;
        }
        .catResults {
            width: 100%;
            padding: 5px;
            margin: 10px 0px;
        }
        .panes {
            display: flex;
            flex-wrap: wrap;
            font-size: 20px;
            width: 100%;
            margin: 0 auto;
        }
        #catering-container {
            background-color: #ffdbad;
        }
        #commercial-container {
            background-color: #e3c7cc;
        }
        #natural-container {
            background-color:#CED097;
        }
        #cultural-container {
            background-color: #E5BC9F;
        }
        #entertain-container {
            background-color: #bbd8b3ff;
        }

        .place-info {
            text-align: left;
            display: block;
            font-size: 18px;
            padding: 10px 8px;
            line-height: 1.5em;
            margin: 0px auto;
            width: 300px;
            display: block;
            background-color: white;
            pointer-events: all;
        }
        .place-container {
            display: block;
            margin: 10px;
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
    <script>
        // TODO validate city. If nonexistent, display error

        // for building forms
        const cateringOptions = {
            'Vegetarian-friendly': "vegetarian", 
            'Vegan-friendly': "vegan", 
            'Halal': "halal", 
            'Kosher': "kosher"
        };
        const commercialOptions = {
            "Supermarkets" : "commercial.supermarket",
            "Marketplaces": "commercial.marketplace",
            "Shopping malls": "commercial.shopping_mall",
            "Clothing": "commercial.clothing",
            "Banks/ATM": "service.financial.bank%2Cservice.financial.atm",
            "Public transportation": "public_transport",
        };
        const naturalOptions = {
            "Forests" : "natural.forest",
            "Water": "natural.water",
            "Mountains": "natural.mountain",
            "Camping Sites": "camping",
            "Parks": "leisure.park",
            "Any nature": "natural"
        };
        const culturalOptions = {
            "Famous Tourist Sites": "tourism.sights",
            "Theatre & Arts": "entertainment.culture",
            "Museums": "entertainment.museum",
            "Universities": "building.university%2Cbuilding.college",
            "Religious Sites": "tourism.sights.place_of_worship",
            "Historical Sites": "building.historic"
        };
        const entertainOptions = {
            "All sport centers": "sport",
            "Fitness centers": "sport.fitness",
            "Zoo": "entertainment.zoo",
            "Aquarium": "entertainment.aquarium",
            "Cinema": "entertainment.cinema",
            "Theme park": "entertainment.theme_park",
            "Spa": "leisure.spa",
            "Casino": "adult.casino",
        };

        // for extracting info from result places
        const basicFields = [
            "year_of_construction",
            "opening_hours",
            "phone",
            "website",
        ];

        // info from general form that will be used in api query string
        // document.getElementById('mythings').textContent = <?php echo json_encode($city); ?>;
        var cityName = "London";
        var lonLat = { lat: 51.50853, lon: -0.12574 };
        var distMeters = 3000;
        var wheelchair = ""; // empty string means false
        var wifi = ""; // empty string means false

        // form validation
        function validateDist(dist) {
            const distNum = parseFloat(dist);

            // if successfully converted into a numeric value, return in meters
            if (!isNaN(distNum)) {
                const meters = Math.round(distNum * 1000);
                return meters;
            } else {
                // if cannot be converted into a number, display error message
                $(".input-err").html("Error: radius must be numeric. Defaulted to 3 km.");
                return 3000;
            }
        }

        function getCatTags(containerId) {
            switch (containerId) {
                case "natural-container":
                    return {
                        water: ""
                    };
                case "catering-container":
                    return {
                        "cuisine": "", 
                        "diet:gluten_free": "gluten-free", 
                        "diet:halal": "halal", 
                        "diet:kosher": "kosher",
                        "diet:vegetarian": "vegetarian", 
                        "diet:vegan": "vegan",
                        "internet_access": "Wi-Fi",
                        "wheelchair": "wheelchair",
                        "internet_access": "Wi-Fi"
                    };
                default:
                    return {
                        "wheelchair": "wheelchair",
                        "internet_access": "Wi-Fi"
                    };
            }
        }

        // if the category is a hierarchy, e.g., catering.cafe, get the parent category
        // otherwise keep the whole category
        function getParentCat(category) {
            var periodIdx = category.indexOf(".");

            let parentCat = category;
            if (periodIdx !== -1) { 
                parentCat = category.substring(0, periodIdx);
            }
            return parentCat;
        }

        function makeTag(text) {
            text = text.replace(/_/g, ' ');
            capitalized = text[0].toUpperCase() + text.slice(1);

            const html = `<div class="tag-container">
                            <img src="tag-icon.png" alt="grey tag with text">
                            <div class="tag-text">${capitalized}</div>
                            </div>`;

            return html;
        }

        function makeLink(classname, url, text) {
            return `<a href="${url}" class="${classname}">${text}</a>`;
        }

        function makeChkBox(name, id, val) {
            return `<input type="checkbox" name="${name}" id="${id}" value="${val}" />`;
        }

        function makeLabel(forId, text) {
            return `<label for="${forId}">${text}</label>`;
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

        async function getLonLat(dest) {
            
            // $("input[name='box']:checked").each(function() {
            //     const selectedVal = $(this).val();
            //     if (selectedVal != "all") {
            //         catArr.push(selectedVal);
            //     }
            // });

            // TODO convert dest name to lowercase?

            const url = `https://opentripmap-places-v1.p.rapidapi.com/en/places/geoname?name=${dest}`;
            const options = {
                method: 'GET',
                headers: {
                    'X-RapidAPI-Key': '279c854fdbmsheb57c9c292c7a83p14f3e9jsn01a09f1170f4',
                    'X-RapidAPI-Host': 'opentripmap-places-v1.p.rapidapi.com'
                }
            };

            try {
                const res = await fetch(url, options);
                const data = await res.text();
                const result = JSON.parse(data);
                // TODO if no response? Partial matches? 
                
                const lat = result.lat;
                const lon = result.lon;
                return {
                    lat: lat, lon: lon
                };
            } catch (error) {
                console.log(error);
            }
        }
    </script>
</head>

<body>
    <h1>Build the Trip of a Lifetime</h1>
    <div id="cats-container">
        <form id="catering-form" action="#" method="#">
            <h4>Restaurants and Cafes</h4>
            <input type="radio" name="cateringCat" class="food-cat" id="chk-restaurant" value="catering.restaurant">
            <label for="chk-restaurant">Restaurants only</label><br />
            <input type="radio" name="cateringCat" class="food-cat" id="chk-cafe" value="catering.cafe">
            <label for="chk-cafe">Cafes only</label><br />
            <input type="radio" name="cateringCat" class="food-cat" id="chk-all" value="catering" checked>
            <label for="chk-all">Restaurants, cafes, fast food, bars</label><br /><br />
        </form>

        <div class='catResults' id='catering-container'>
            <h2>Culinary Expeditions</h2>
            <div class="panes"></div>
        </div>

        <form id="commercial-form" action="#" method="#">
            <h4>Commercial</h4>
        </form>
        <div class='catResults' id='commercial-container'>
            <h2>Retail & Commercial</h2>
            <div class="panes"></div>
        </div>

        <form id="cultural-form" action="#" method="#">
            <h4>Classic Tourist Sites</h4>
        </form>
        <div class='catResults' id='cultural-container'>
            <h2>Cultural Odyssey</h2>
            <div class="panes"></div>
        </div>

        <form id="natural-form" action="#" method="#">
            <h4>One With Nature</h4>
        </form>
        <div class='catResults' id='natural-container'>
            <h2>Adventures in Nature</h2>
            <div class="panes"></div>
        </div>
        
        <form id="entertain-form" action="#" method="#">
            <h4>More Fun</h4>
        </form>
        <div class='catResults' id='entertain-container'>
            <h2>Other Entertainment</h2>
            <div class="panes"></div>
        </div>
    </div>
    <script>
        async function loadResults(containerId, categories, filters, lon, lat) {
            if (categories.length == 0) { // TODO make into a error div later
                console.log("Error: At least one category required");
                return;
            }

            if (wheelchair !== "") {
                filters.push(wheelchair);
            }
            if (wifi !== "") {
                filters.push(wifi);
            }
            console.log("filters");
            console.log(filters);

            const conditions = filters.length == 0 ? "" : `&conditions=${ filters.join("%2C") }`;
            const catStr = categories.join("%2C");
            
            // a lot of nature entries don't have enough detail - no guarantee, but we can get some extra entries
            const maxResults = containerId == "natural-container" ? 30 : 4;
            const url = `https://api.geoapify.com/v2/places?categories=${catStr}${conditions}&filter=circle%3A${lon}%2C${lat}%2C${distMeters}&bias=proximity%3A${lon}%2C${lat}&limit=${maxResults}&apiKey=0b813d154863412cb86acd4b37d93c3b`;

            console.log(url);

            const catTags = getCatTags(containerId);

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

                        // missing key info
                        if (!("name" in info) || !("distance" in info)) {
                            continue;
                        }

                        const moreinfo = info.datasource.raw;

                        // features of this place to display as tags
                        let matchingTags = [];
                        for (const key in catTags) {
                            if (key in moreinfo) {
                                if (catTags[key] === "") {
                                    if (key == "cuisine") {
                                        const tagVals = moreinfo[key];
                                        const tagValsArr = tagVals.split(';');
                                        matchingTags = matchingTags.concat(tagValsArr);
                                    } else {
                                        matchingTags.push(moreinfo[key]);
                                    }
                                } else {
                                    matchingTags.push(catTags[key]);
                                }
                            }
                        }
                        console.log(matchingTags);

                        const tagsHTML = matchingTags.map(makeTag).join('');
                        let list = `<ul>
                            <li class='tags'>${tagsHTML}</li>
                            <li class='place-name'>${info.name}</li>
                            <li>${metersToKm(info.distance)} km away</li>
                            <li>${formatAddress(info.formatted)}</li>`; // this is the full address

                        for (const key of basicFields) {
                            if (moreinfo.hasOwnProperty(key)) {
                                list += `<li>${formatbasicFields(key, moreinfo[key])}</li>`;
                            }
                        }
                        
                        list += "</ul>";
                        dataHTML += "<div class='place-container'><div class='place-info'>" + list + "</div></div>";
                    }

                    // no results found
                    if (dataHTML === "") {
                        dataHTML = "We couldn't find any matching results. You may need to unselect wheelchair only or Wi-Fi only.";
                    }

                    $(`#${containerId} .panes`).html(dataHTML); 
                })
            .catch (error => console.log(error));
        }

        $(document).ready(function() {
            keys = Object.keys(cateringOptions);
            keys.forEach((k, idx) => {
                const chkBox = makeChkBox("cateringChk", `chk${idx}cater`, `${cateringOptions[k]}`) 
                                + makeLabel(`chk${idx}cater`, k) + "<br>";
                $('#catering-form').append(chkBox);
            });
            $("#catering-form").append('<input type="submit" value="Search">');

            // populate form for commercial            
            keys = Object.keys(commercialOptions);
            keys.forEach((k, idx) => {
                const chkBox = makeChkBox("commercialChk", `chk${idx}commerc`, `${commercialOptions[k]}`) 
                                + makeLabel(`chk${idx}commerc`, k) + "<br>";
                $('#commercial-form').append(chkBox);
            });
            $("#commercial-form").append('<input type="submit" value="Search">');

            // populate form for nature            
            keys = Object.keys(naturalOptions);
            keys.forEach((k, idx) => {
                const chkBox = makeChkBox("naturalChk", `chk${idx}nat`, `${naturalOptions[k]}`) 
                                + makeLabel(`chk${idx}nat`, k) + "<br>";
                $('#natural-form').append(chkBox);
            });
            $("#natural-form").append('<input type="submit" value="Search">');


            // populate form for cultural activities
            keys = Object.keys(culturalOptions);
            keys.forEach((k, idx) => {
                const chkBox = makeChkBox("culturalChk", `chk${idx}cul`, `${culturalOptions[k]}`) 
                                + makeLabel(`chk${idx}cul`, k) + "<br>";
                $('#cultural-form').append(chkBox);
            });
            $("#cultural-form").append('<input type="submit" value="Search">');

            // populate form for other entertainment
            keys = Object.keys(entertainOptions);
            keys.forEach((k, idx) => {
                const chkBox = makeChkBox("entertainChk", `chk${idx}ent`, `${entertainOptions[k]}`) 
                                + makeLabel(`chk${idx}ent`, k) + "<br>";
                $('#entertain-form').append(chkBox);
            });
            $("#entertain-form").append('<input type="submit" value="Search">');
        });
            
        $(document).ready(async function() {

            $("#general-form").submit(async function(event) {
                event.preventDefault();

                // const destCity = $("form #user-text").val();
                // lonLat = await getLonLat(destCity);    // TODO not working

                // distMeters = validateDist($("#dist-limit").val());
                distMeters = <?php echo json_encode($distanceLimit * 1000); ?>;
                console.log("meters from form: " + distMeters);

                // wheelchair = $("#chkWheelchair").prop("checked") ? $("#chkWheelchair").val() : "";
                // wifi = $("#chkWifi").prop("checked") ? $("#chkWifi").val() : "";
                wheelchair = <?php echo json_encode($wheelchair); ?>;
                wifi = <?php echo json_encode($wifi); ?>;
                console.log("wheelchair " + wheelchair + ", wifi " + wifi);
            });

            $('#catering-form').submit(async function (event) {
                console.log(distMeters);
                event.preventDefault();

                $("#catering-container .panes").html("Concocting a delicious trip for you...");
                
                const categories = [];
                const selected = $('input[name="cateringCat"]:checked').val();
                categories.push(selected);

                console.log(categories);
                
                const filters = [];
                $("input[name='cateringChk']:checked").each(function() {
                    const option = $(this).val();
                    filters.push(option);
                });
                console.log(filters);

                loadResults("catering-container", categories, filters, lonLat.lon, lonLat.lat);
            });

            $('#commercial-form').submit(async function (event) {
                event.preventDefault();

                $("#commercial-container .panes").html("Looking up the essentials for you...");
                
                const categories = [];
                $("input[name='commercialChk']:checked").each(function() {
                    const option = $(this).val();
                    categories.push(option);
                });
                console.log(categories);                

                loadResults("commercial-container", categories, [], lonLat.lon, lonLat.lat);
            });

            $('#natural-form').submit(async function (event) {
                event.preventDefault();

                $("#natural-container .panes").html("Don't miss out on the stunning outdoors...");
                
                const categories = [];
                $("input[name='naturalChk']:checked").each(function() {
                    const option = $(this).val();
                    categories.push(option);
                });
                console.log(categories);

                loadResults("natural-container", categories, [], lonLat.lon, lonLat.lat);
            });

            $('#cultural-form').submit(async function (event) {
                event.preventDefault();

                $("#cultural-container .panes").html("Looking for all the unmissable cultural spots...");
                
                const categories = [];
                $("input[name='culturalChk']:checked").each(function() {
                    const option = $(this).val();
                    categories.push(option);
                });
                console.log(categories);

                loadResults("cultural-container", categories, [], lonLat.lon, lonLat.lat);
            });

            $('#entertain-form').submit(async function (event) {
                event.preventDefault();

                $("#entertain-container .panes").html("Looking for fun? Don't worry, we got you...");
                
                const categories = [];
                $("input[name='entertainChk']:checked").each(function() {
                    const option = $(this).val();
                    categories.push(option);
                });
                console.log(categories);

                loadResults("entertain-container", categories, [], lonLat.lon, lonLat.lat);
            });
        });
    </script>
</body>
</html>
