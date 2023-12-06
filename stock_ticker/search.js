var http = require('http');
var url = require('url');
var fs = require('fs');
const { MongoClient } = require('mongodb');
const { resourceLimits } = require('worker_threads');

const uri = "mongodb+srv://dbuser1:void-10ne@cluster0.cix9qnf.mongodb.net/?retryWrites=true&w=majority";
const client = new MongoClient(uri);
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

async function searchDB(byCompany, val) {
    let resultArr = null;
    try {
        await client.connect();
        const db = client.db('stock_ticker');
        const collection = db.collection('Companies');

        try {
            const target = {};
            if (byCompany) {
                target['company'] = {'$regex': val, '$options': 'i'};
            } else {
                target['ticker'] = {'$regex': val, '$options': 'i'};
            }

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
        // serve the html for the form
        fs.readFile('form.html', 'utf8', function(err, data) {
            if (err) {
                console.error(err);
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
}).listen(8080);