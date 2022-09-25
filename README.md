# Installation

```bash
git clone https://github.com/ebelicheva/otp.git otp
cd otp/docker

# edit exposed ports in .env if you need custom ones
docker-compose up --build
```

# API

API only interface is available on localhost, port 80.

For more details see the postman collection in docs/OTP.postman_collection.json.

### Registration

```
POST /api/v1/register

Headers:
    Content-type: application/json
Body:
{
    "email": "test@example.com",
    "password": "very-strong-password",
    "phone": "+(089) 363 0 481"
}
Response 200:
{
    "token": "some-secret-token-string-to-use-for-authentication"
}
```

### Check code

```
POST /api/v1/verify-code
Headers:
Content-type: application/json
Authorization: Bearer {{token}}
Body:
{
    "code": "371080"
}
```

### Issue new code

```
POST /api/v1/refresh-code
Headers:
Content-type: application/json
Authorization: Bearer {{token}}
```