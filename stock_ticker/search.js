var http = require('http');
var url = require('url');
var fs = require('fs');
const { MongoClient } = require('mongodb');
const { resourceLimits } = require('worker_threads');

const uri = "mongodb+srv://dbuser1:void-10ne@cluster0.cix9qnf.mongodb.net/?retryWrites=true&w=majority";
const client = new MongoClient(uri);

async function searchDB(byCompany, val) {
    let resultArr = null;
    try {
        await client.connect();
        const db = client.db('stock_ticker');
        const collection = db.collection('Companies');

        try {
            const target = {};
            if (byCompany) {
                target['company'] = val;
            } else {
                target['ticker'] = val;
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
    if (matches == null) { 
        res.write("Sorry, something wen't wrong!");
        return;
    } else if (matches.length == 0) {
        res.write("<h1>NO MATCHES FOUND.</h1>Did you select the correct kind of input?");
        return;
    }

    try {
        let displayStr = "<h1>MATCHING RESULTS:</h1><ul>";
        matches.forEach(doc => {
            displayStr += "<li>";
            for (const [field, val] of Object.entries(doc)) {
                if (field != "_id") {
                    displayStr += `<b>${field}: </b>${val}<br />`;
                }
            }
            displayStr += "</li>";
        });
        displayStr += "</ul>";

        res.write(displayStr);
    } catch (err) {
        console.error("Error iterating over MongoDB cursor:", err);
    }
}


http.createServer(async function (req, res) {
    res.writeHead(200, {'Content-Type': 'text/html'});
    var path = url.parse(req.url, true).pathname;
    
    if (path == "/") {
        // Read and write the html file containing the form
        fs.readFile('form.html', 'utf8', function(err, data) {
            if (err) {
                console.error(err);
                res.writeHead(500, {'Content-Type': 'text/plain'});
                res.end('Internal Server Error');
            } else {
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
            
            res.end();
        }
    }
}).listen(8080);