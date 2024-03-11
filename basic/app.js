const mqtt = require('mqtt');
const mysql = require('mysql');
const express = require('express');

const db = mysql.createConnection({
    host: '127.0.0.1',
    port: '3306',
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
    var response = JSON.stringify(message.toString());
    /*
    {
        "barber_id": 1,
        "sender": "barber",
        "user_id": 2,
        "message": "Hello!"
    }
     */
    const data = [null, response.barber_id, response.sender, response.user_id, 0, 0, new Date(), topic, response.message];
    if(response.sender=="barber"){
        data[4] = 1
    }
    else
    {
        data[5] = 1
    }
    const sql = 'INSERT INTO message (id, barber_id, sender, user_id, barber_readed, user_readed, date, topic, message) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)';
    db.query(sql, data, (err, result) => {
        if (err) throw err;
        console.log('Message inserted into database');
    });
});

const app = express();
const port = 3000;

app.listen(port, () => {
    console.log(`Server running on http://localhost:${port}`);
});