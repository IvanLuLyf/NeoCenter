#TwimiCenter Neo

## User Login
URL : `/api/user/login?username=[Your Username]&password=[Your Password]`

Response:

```
{
    "status":"ok",
    "ret":0,
    "id":"[Your User ID]",
    "email":"[Your Email]",
    "nickname":"[Your Nickname]",
    "token":[Token],
    "expire":[Expire Time]
}
```
    
------------

## User Register
URL : `/api/user/register?username=[Your Username]&password=[Your Password]&email=[Your Email]&nickname=[Your Nickname(Optional)]`

Response:

```
{
    "status":"ok",
    "ret":0,
    "id":"[Your User ID]",
    "email":"[Your Email]",
    "nickname":"[Your Nickname]",
    "token":[Token],
    "expire":[Expire Time]
}
```

------------