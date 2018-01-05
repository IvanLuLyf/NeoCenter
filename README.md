#TwimiCenter Neo

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