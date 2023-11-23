const { MongoClient } = require('mongodb');
const fs = require('fs');
const readline = require('readline');

const uri = "mongodb+srv://dbuser1:void-10ne@cluster0.cix9qnf.mongodb.net/?retryWrites=true&w=majority";
const client = new MongoClient(uri);

async function insertDocs() {
    try {
        await client.connect();
        const db = client.db('stock_ticker');
        const collection = db.collection('Companies');

        // read data line by line from csv file
        const fileStream = fs.createReadStream('companies-1.csv');
        const rl = readline.createInterface({
            input: fileStream,
            crlfDelay: Infinity,
        });

        let idx = 0;
        let keys;
        for await (const line of rl) {
            if (idx == 0) {
                keys = line.split(',');
                // make all field names lowercase for consistency
                for (let i = 0; i < keys.length; i++) {
                    keys[i] = keys[i].toLowerCase();
                }
            } else {
                console.log(line);
                const values = line.split(',');

                const newDoc = {};
                for (let i = 0; i < keys.length; i++) {
                    const key = keys[i];
                    const val = values[i];
                    // strings containing purely numeric values are converted
                    newDoc[key] = isNaN(val) ? val : parseFloat(val);
                }
                await collection.insertOne(newDoc);
            }
            idx++;
        }
    } finally {
        // close the database connection when finished or an error occurs
        await client.close();
    }
}

insertDocs().catch(console.error);
