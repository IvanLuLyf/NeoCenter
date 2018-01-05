#TwimiCenter Neo

## Return Code Reference

|  Code  | Description |
|------- |-------------|
|0       |ok           |
|1001    |password error|
|1002    |user not exists|
|1003    |username exists|
|1004    |empty arguments|
|1005    |invalid username|
|1006    |database error|
|1007    |wrong file|
|2001    |invalid appkey|
|2002    |permission denied|
|3001    |invalid tid|

## User Login
URL : `/api/user/login?username=[Your Username]&password=[Your Password]`

Response:

```
{
    "ret" : 0,
    "status" : "ok",
    "id" : "[Your User ID]",
    "username" : "[Your Username]",
    "email" : "[Your Email]",
    "token" : "[Access Token]",
    "nickname" : "[Your Nickname]",
    "expire" : "[Expire Time]"
}
```
    
------------

## User Register
URL : `/api/user/register?username=[Your Username]&password=[Your Password]&email=[Your Email]&nickname=[Your Nickname(Optional)]`

Response:

```
{
    "ret" : 0,
    "status" : "ok",
    "id" : "[Your User ID]",
    "username" : "[Your Username]",
    "email" : "[Your Email]",
    "token" : "[Access Token]",
    "nickname" : "[Your Nickname]",
    "expire" : "[Expire Time]"
}
```

------------