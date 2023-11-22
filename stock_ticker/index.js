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

    const rl = readline.createInterface({
        input: fs.createReadStream('companies-1.csv'),
        crlfDelay: Infinity,
    });
      
    let idx = 0;
    let keys;
    rl.on('line', (line) => {
        if (idx == 0) {
            keys = line.split(',');
        } else {
            console.log("Line read: " + line);
            const values = line.split(',');
            const newDoc = {};
            for (let i = 0; i < keys.length; i++) {
                const key = keys[i];
                const val = values[i];
                // strings containing purely numeric values should be converted
                newDoc[key] = isNaN(val) ? val : parseFloat(val);
            }
            console.log(newDoc);
            if (idx == 3) {
                collection.insertOne(newDoc); // TODO creating an error
            }
        }
        idx++;
    });
  } finally {
    // Close the database connection when finished or an error occurs
    await client.close();
  }
}

insertDocs().catch(console.error);

// https://nodejs.org/api/readline.html#readline_example_read_file_stream_line_by_line

