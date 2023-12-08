var http = require('http');
var url = require('url');
var fs = require('fs');
const { MongoClient } = require('mongodb');

var port = process.env.PORT || 3000;
// var port = 8080;
const uri = "mongodb+srv://dbuser1:void-10ne@cluster0.cix9qnf.mongodb.net/?retryWrites=true&w=majority";
const client = new MongoClient(uri);

// css will be applied when displaying the search results
const css = `
            body {
                background-color: #fcf6e0;
            }
            h1 {
                color: #5d9988;
                font-size: 30px;
                margin: 50px 0px 20px 0px;
                text-align: center;
            }
            ul {
                box-sizing: border-box;
                width: 400px;
                margin: 0 auto;
            }
            li {
                color: darkolivegreen;
                line-height: 1.4em;
                list-style: none;
                font-size: 18px;
                margin: 30px;
            }
            p {
                text-align: center;
                font-size: 18px;
            }
            b {
                width: 100px;
                display: inline-block;
                color: gray;
            }`;

/**
 * Connects to and searches the database for a stocker ticker or company name.
 * 
 * @param {boolean} byCompany - true if user wants to search by company name, 
 *                              false if user wants to search by ticker symbol.
 * @param {string} val - the company or ticker to search for.
 * @return {Array.<Object>} - array of objects representing the matching documents.
 */
async function searchDB(byCompany, val) {
    let resultArr = null;
    try {
        // connect to database
        await client.connect();
        const db = client.db('stock_ticker');
        const collection = db.collection('Companies');

        try {
            const target = {};

            // include partial (case-insensitive matches) in our search
            if (byCompany) {
                target['company'] = {'$regex': val, '$options': 'i'}; 
            } else {
                target['ticker'] = {'$regex': val, '$options': 'i'};
            }

            // search for matching documents and convert to array of objects
            const result = collection.find(target);
            resultArr = await result.toArray();
        } catch (err) {
            console.error("Error executing MongoDB query:", err);
        }
    } finally {
        // close the database connection when finished or an error occurs
        await client.close();

        return resultArr;
    }
}

/**
 * Writes a page containing data from the given matches as response to the client.
 * 
 * @param {Array.<Object>} matches - array of objects that matched our search.
 * @param {http.ServerResponse} res - http response object that server will send back to client.
 */
async function displayMatches(matches, res) {
    res.writeHead(200, {'Content-Type': 'text/html'});

    if (matches == null) { 
        res.write("Sorry, something wen't wrong!");
    } else {
        try {
            let content = "";

            if (matches.length == 0) {
                content = "<h1>No Matches Found.</h1>";
            } else {
                content = "<h1>Matching Results:</h1><ul>";

                // for each document that matched, skip id field,
                // but display all of its other info
                matches.forEach(doc => {
                    content += "<li>";
                    for (const [field, val] of Object.entries(doc)) {
                        if (field != "_id") {
                            content += `<b>${field}: </b>${val}<br />`;
                        }
                    }
                    content += "</li>";
                });
                content += "</ul>";
            }

            // send actual content of the response
            const resultsHtml = `<html><head><style>${css}</style></head><body>${content}</body></html>`;
            res.write(resultsHtml);
        } catch (err) {
            console.error("Error iterating over MongoDB cursor:", err);
        }
    }

    res.end();
}

http.createServer(async function (req, res) {
    var path = url.parse(req.url, true).pathname;
    
    if (path == "/") {
        // serve a form for user to enter ticker symbol or company name
        fs.readFile('form.html', 'utf8', function(err, data) {
            if (err) {
                res.writeHead(500, {'Content-Type': 'text/plain'});
                res.end('Internal Server Error');
            } else {
                res.writeHead(200, {'Content-Type': 'text/html'});
                res.write(data);
                res.end();
            }
        });
    } else if (path == "/process") {
        if (req.method === 'GET') {
            const queryStr = url.parse(req.url, true).query;

            const isCompany = (queryStr['key'] == 'company');
            const val = queryStr['target-val'].trim(); // trim leading and trailing whitespaces

            const results = await searchDB(isCompany, val);
            displayMatches(results, res);
        }
    }
}).listen(port);