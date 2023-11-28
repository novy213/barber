# Barber
Barber app made in yii2 framework for barbers

# api
## Api url
```
/barber/basic/web
```
# Login
```
POST /
```
### Params:
```
(null)
```
### Body:
```
{
  "phone": "123456789",
  "password": "test"
}
```
### Response: 
```
{
  "error": false,
  "message": null,
  "token": "50x9v0uqxvLsBctrX1brKOL1TRhw5oDt",
  "userId": 11
}
```
## Logout
```
DELETE /
```
### Params:
```
(null)
```
### Body:
```
(null)
```
### Response: 
```
{
  "error":false,
  "message": null
}
```
## Register
```
POST /register
```
### Params:
```
(null)
```
### Body:
```
{
  "name": "jan",
  "last_name: "kowalski"
  "phone": "123456789",
  "password": "admin",
}
```
### Response: 
```
{
  "error":false,
  "message": null
}
```
## Add visit
```
POST /addvisit
```
### Params:
```
(null)
```
### Body:
```
{
"date":"2023-11-21 15:30",
"barber_id":11,
"type_id":4,
"additions":[
{
"additional_id":1
},
{
"additional_id":2
}
]
}

or

{
"date":"2023-11-21 15:30",
"barber_id":11,
"type_id":4,
"additions":[]
}
```
### Response: 
```
{
  "error":false,
  "message": null
}
```
## Get visits for barber
```
POST /visits/{barber_id}
```
### Params:
```
(null)
```
### Body:
```
date - 2023-11-12
```
### Response: 
```
{
  "error": false,
  "message": null,
  "visit": [
    {
      "date": "2023-11-21 12:00",
      "status": 0,
      "date_end": null
    },
    {
      "date": "2023-11-21 12:15",
      "status": 0,
      "date_end": null
    },
    {
      "date": "2023-11-21 12:30",
      "status": 0,
      "date_end": null
    },
    {
      "date": "2023-11-21 12:45",
      "status": 1,
      "date_end": "2023-11-21 13:00"
    },
    {
      "date": "2023-11-21 13:00",
      "status": 1,
      "date_end": "2023-11-21 13:15"
    },
    {
      "date": "2023-11-21 13:15",
      "status": 1,
      "date_end": "2023-11-21 13:30"
    },
    {
      "date": "2023-11-21 13:30",
      "status": 1,
      "date_end": "2023-11-21 13:45"
    },
    {
      "date": "2023-11-21 13:45",
      "status": 1,
      "date_end": "2023-11-21 14:00"
    },
    {
      "date": "2023-11-21 14:00",
      "status": 1,
      "date_end": "2023-11-21 14:15"
    },
    {
      "date": "2023-11-21 14:15",
      "status": 1,
      "date_end": "2023-11-21 14:30"
    },
    {
      "date": "2023-11-21 14:30",
      "status": 0,
      "date_end": null
    },
    {...},
  ]
}
```
## Get user visits
```
GET /uservisits
```
### Params:
```
(null)
```
### Body:
```
(null)
```
### Response: 
```
{
  "error": false,
  "message": null,
  "visit": [
    {
      "id": 8,
      "date": "2023-11-21 12:45",
      "barber_name": "adam",
      "barber_last_name": "noowa",
      "img_url": "",
      "label": "combo_golarkaFarb",
      "additional_info": null,
      "user_id": 11,
      "notified": 0,
      "price": 70,
      "time": 105
    },
    {
      "id": 15,
      "date": "2023-11-21 10:00",
      "barber_name": "tets2",
      "barber_last_name": "test2",
      "img_url": "http://localhost/admin/barber_img/test.png",
      "label": "combo_golarka",
      "additional_info": null,
      "user_id": 11,
      "notified": 0,
      "price": 70,
      "time": 75
    }
  ]
}
```
## Change user information data
```
PUT /changeuserdata
```
### Params:
```
(null)
```
### Body:
```
name - not required
last_name - not required
phone - not required
moze byc albo to albo to albo wszystko.
```
### Response: 
```
{
  "error": false,
  "message": null
}
```
