# Barber
Barber app made in yii2 framework for barbers

# api
## Api url
```
/barber/basic/web
```
## 1.1 Login
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
## 1.2 Logout
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
## 1.3 Register
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
  "login": "admin",
  "password": "admin",
  "name": "jan",
  "last_name: "kowalski"
}
```
### Response: 
```
{
  "error":false,
  "message": null
}
```
## 2.1 Get results list
```
GET /result
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
[
  {
    "id": 1,
    "user_id": 1,
    "game_id": 1,
    "correct_precentage": 26,
    "all_time": 307,
    "avg_reaction_time_correct": 4.2,
    "avg_reaction_time_incorrect": 9.7,
    "best_streak": 7
  },
  {
    "id": 2,
    "user_id": 1,
    "game_id": 2,
    "correct_precentage": 80,
    "all_time": 269,
    "avg_reaction_time_correct": 5.5,
    "avg_reaction_time_incorrect": 9.2,
    "best_streak": 4
  },
  {...},
]
```
## 2.2 Get ranking list
```
GET /ranking
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
[
  {
    "id": 4,
    "user_id": 6,
    "avg_all": 9.3,
    "fastest_correct_score": 1.5
  },
  {
    "id": 3,
    "user_id": 6,
    "avg_all": 8.4,
    "fastest_correct_score": 1.8
  },
  {...},
]
```
