# API Documentation

## Base URL
```
http://localhost:8000/api/v1
```

## Authentication

The API uses Laravel Sanctum for token-based authentication. Include the token in the Authorization header:

```
Authorization: Bearer {token}
```

### Register
```http
POST /api/v1/auth/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Response:**
```json
{
  "success": true,
  "message": "User registered successfully",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      ...
    },
    "token": "1|xxxxxxxxxxxxxxxxxxxx",
    "token_type": "Bearer"
  }
}
```

### Login
```http
POST /api/v1/auth/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password123"
}
```

### Get Authenticated User
```http
GET /api/v1/auth/user
Authorization: Bearer {token}
```

### Logout
```http
POST /api/v1/auth/logout
Authorization: Bearer {token}
```

### Create API Token
```http
POST /api/v1/auth/tokens
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "My App Token",
  "abilities": ["*"]
}
```

### List Tokens
```http
GET /api/v1/auth/tokens
Authorization: Bearer {token}
```

### Revoke Token
```http
DELETE /api/v1/auth/tokens/{id}
Authorization: Bearer {token}
```

### Refresh Tokens (Revoke All & Create New)
```http
POST /api/v1/auth/refresh
Authorization: Bearer {token}
```

### Forgot Password
```http
POST /api/v1/auth/forgot-password
Content-Type: application/json

{
  "email": "john@example.com"
}
```

### Reset Password
```http
POST /api/v1/auth/reset-password
Content-Type: application/json

{
  "token": "reset_token_from_email",
  "email": "john@example.com",
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}
```

---

## Profile

### Get Profile
```http
GET /api/v1/profile
Authorization: Bearer {token}
```

### Update Profile
```http
PUT /api/v1/profile
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "John Updated",
  "email": "john.updated@example.com"
}
```

### Update Password
```http
POST /api/v1/profile/password
Authorization: Bearer {token}
Content-Type: application/json

{
  "current_password": "oldpassword",
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}
```

### Delete Account
```http
DELETE /api/v1/profile
Authorization: Bearer {token}
Content-Type: application/json

{
  "password": "currentpassword"
}
```

---

## Teams

### List Teams
```http
GET /api/v1/teams
Authorization: Bearer {token}
```

### Create Team
```http
POST /api/v1/teams
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "My Team",
  "description": "Team description",
  "website": "https://example.com"
}
```

### Get Team
```http
GET /api/v1/teams/{id}
Authorization: Bearer {token}
```

### Update Team
```http
PUT /api/v1/teams/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Updated Team Name",
  "description": "Updated description"
}
```

### Delete Team
```http
DELETE /api/v1/teams/{id}
Authorization: Bearer {token}
```

### Get Team Members
```http
GET /api/v1/teams/{id}/members
Authorization: Bearer {token}
```

### Add Team Member
```http
POST /api/v1/teams/{id}/members
Authorization: Bearer {token}
Content-Type: application/json

{
  "user_id": 2,
  "role": "member"
}
```

**Roles:** `owner`, `admin`, `member`

### Remove Team Member
```http
DELETE /api/v1/teams/{id}/members/{user_id}
Authorization: Bearer {token}
```

---

## Plans

### List Plans (Public)
```http
GET /api/v1/plans
```

### Get Plan Details (Public)
```http
GET /api/v1/plans/{id}
```

### Get Current Plan
```http
GET /api/v1/plans/current
Authorization: Bearer {token}
```

---

## Billing

### Get Billing Information
```http
GET /api/v1/billing
Authorization: Bearer {token}
```

### Get Current Subscription
```http
GET /api/v1/billing/subscription
Authorization: Bearer {token}
```

### Subscribe to Plan
```http
POST /api/v1/billing/subscribe
Authorization: Bearer {token}
Content-Type: application/json

{
  "plan_code": "basic_monthly",
  "provider": "manual"
}
```

**Providers:** `manual`, `stripe`

### Cancel Subscription
```http
POST /api/v1/billing/cancel
Authorization: Bearer {token}
```

### Get Invoices
```http
GET /api/v1/billing/invoices
Authorization: Bearer {token}
```

---

## Dashboard

### Get Dashboard Overview
```http
GET /api/v1/dashboard
Authorization: Bearer {token}
```

### Get Dashboard Statistics
```http
GET /api/v1/dashboard/stats
Authorization: Bearer {token}
```

---

## Rate Limiting

- **Public endpoints:** 60 requests per minute
- **Authentication endpoints:** 10 requests per minute
- **Protected endpoints:** 120 requests per minute

Rate limit headers are included in responses:
```
X-RateLimit-Limit: 120
X-RateLimit-Remaining: 119
```

---

## Error Responses

All errors follow this format:

```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    "field": ["Error message"]
  }
}
```

### Common Status Codes

- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `429` - Too Many Requests
- `500` - Server Error

---

## Testing with cURL

### Register & Login
```bash
# Register
curl -X POST http://localhost:8000/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -d '{"name":"John Doe","email":"john@example.com","password":"password123","password_confirmation":"password123"}'

# Login
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"john@example.com","password":"password123"}'

# Use token
curl -X GET http://localhost:8000/api/v1/auth/user \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

---

## API Resources

All responses use Laravel API Resources for consistent data formatting:

- `UserResource` - User data
- `TeamResource` - Team data
- `PlanResource` - Plan data
- `BillingResource` - Billing/Subscription data

---

## Notes

- All timestamps are in ISO 8601 format
- Amounts are stored in minor units (cents) and converted in responses
- Pagination is included where applicable
- All endpoints require proper authentication headers (except public endpoints)

