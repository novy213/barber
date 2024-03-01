const mqtt = require('mqtt');
const mysql = require('mysql');
const express = require('express');

const db = mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: 'admin',
    database: 'test'
});
db.connect((err) => {
    if (err) throw err;
    console.log('Connected to MySQL Database');
});

const client = mqtt.connect('mqtt://jakubsolarek.pl:1883');

client.on('connect', () => {
    console.log('Connected to MQTT Broker');
    client.subscribe('testTopic', (err) => {
        if (err) throw err;
    });
});

client.on('message', (topic, message) => {
    console.log(`Received message: ${message.toString()} on topic: ${topic}`);

    // Zapisz wiadomość do bazy danych MySQL
    const sql = 'INSERT INTO messages (topic, message) VALUES (?, ?)';
    db.query(sql, [topic, message.toString()], (err, result) => {
        if (err) throw err;
        console.log('Message inserted into database');
    });
});

const app = express();
const port = 3000;

app.listen(port, () => {
    console.log(`Server running on http://localhost:${port}`);
});