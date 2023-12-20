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
  "password": "test",
  "notification_token": "Expo bla bla bla"
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
## Delete visit
```
DELETE /deletevisit
```
### Params:
```
(null)
```
### Body:
```
visit_id - id of the visit
```
### Response: 
```
{
  "error": false,
  "message": null
}
```
## Ban user
```
POST /banuser/{phone}
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
  "message": null
}
```
## Unban user
```
POST /unbanuser/{phone}
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
  "message": null
}
```
## Dayoff
```
PUT /dayoff
```
### Params:
```
(null)
```
### Body:
```
date - albo 2023-10-10 albo 2023-10-10 10:30
```
### Response: 
```
{
  "error": false,
  "message": null
}
```
## Get user data informations
```
GET /userdata
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
  "name": "adam",
  "last_name": "adnd2",
  "phone": 48111111113,
  "notification": 60
}
```
## Get list of banned users
```
GET /bannedusers
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
  "users": [
    {
      "id": 10,
      "password": "$2y$10$8/O6f2G8IBRitCMJaJ3K0OY9VYJlz1BB9TMLmWluWYPwpiO1/FvTu",
      "name": "John",
      "last_name": "Doe",
      "phone": 48111111111,
      "admin": 0,
      "notification": 60,
      "verified": 0,
      "ban": 1,
      "access_token": null
    }
  ]
}
```
## Close account
```
DELETE /closeacc
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
}
```
## Get types and additional servieces
```
GET /gettypes
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
  "types": [
    {
      "id": 1,
      "label": "combo_razor",
      "time": 60,
      "price": 50
    },
    {
      "id": 2,
      "label": "combo_golarka",
      "time": 60,
      "price": 50
    },
    {...},
  ],
  "additional": [
    {
      "id": 1,
      "label": "razor",
      "price": 5,
      "time": 0
    },
    {
      "id": 2,
      "label": "coloring",
      "price": 15,
      "time": 15
    }
  ]
}
```
## Change type or additional service
```
PUT /changetype
```
### Params:
```
(null)
```
### Body:
```
type_id or additional_id - zalezy co chcesz zmienic
```
### Response: 
```
{
  "error": false,
  "message": null,
}
```
## Add type or additional service
```
POST /changetype
```
### Params:
```
(null)
```
### Body:
```
type_id or additional_id - zalezy co chcesz zmienic
label
price
time
```
### Response:
```
{
  "error": false,
  "message": null,
}
```
## Verificate account
```
PUT /verify
```
### Params:
```
(null)
```
### Body:
```
code - kod generujesz w funkcji sendsms(opisana ponizej)
```
### Response:
```
{
  "error": false,
  "message": null,
}
```
## Change password for account
```
PUT /changepassword
```
### Params:
```
(null)
```
### Body:
```
code - kod mozesz wygenerowac w funkcji send sms for password(opisana ponizej)
password - new password
```
### Response:
```
{
  "error": false,
  "message": null,
}
```
## Send sms
```
POST /sendsms
```
Ta funkcja jest wykorzystywana do weryfikacji konta
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
}
```
## Send sms for password
```
POST /smsforpassword
```
Ta funkcja jest wykorzystywana do zmiany hasla
### Params:
```
(null)
```
### Body:
```
phone
```
### Response:
```
{
  "error": false,
  "message": null,
}
```
## Change notification
```
PUT /changenotification
```
### Params:
```
(null)
```
### Body:
```
notification
```
### Response:
```
{
  "error": false,
  "message": null,
}
```
## Delete type
```
DELETE /deletetype
```
### Params:
```
(null)
```
### Body:
```
type_id
```
### Response:
```
{
  "error": false,
  "message": null,
}
```
## Day on
```
DELETE /dayon
```
### Params:
```
(null)
```
### Body:
```
date - albo 2023-11-12 albo 2023-11-12 10:30
```
### Response:
```
{
  "error": false,
  "message": null,
}
```
## Update visit
```
PUT /updatevisit
```
### Params:
```
(null)
```
### Body:
```
visit_id
additional_info
```
### Response:
```
{
  "error": false,
  "message": null,
}
```
## Get barbers
```
GET /getbarbers
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
  "barbers": [
    {
      "id": 1,
      "name": "jan",
      "last_name": "kowalski",
      "user_id": 1,
      "hour_start": "08:00",
      "hour_end": "16:00",
      "img_url": ""
    }
  ]
}
```