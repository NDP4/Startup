# Format Response API User

## GET /api/users (Admin Only)

### Response Sukses (200)

```json
{
    "success": true,
    "message": "Data users berhasil diambil",
    "data": [
        {
            "id": 1,
            "name": "Admin User",
            "email": "admin@example.com",
            "phone": "08123456789",
            "address": "Jakarta",
            "role": "admin"
        },
        {
            "id": 2,
            "name": "Regular User",
            "email": "user@example.com",
            "phone": "08987654321",
            "address": "Bandung",
            "role": "customer"
        }
    ],
    "total": 2
}
```

### Response Error - Unauthorized (403)

```json
{
    "success": false,
    "message": "Unauthorized"
}
```

## GET /api/users/{id}

### Response Sukses (200)

```json
{
    "success": true,
    "message": "Detail user berhasil diambil",
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "phone": "08123456789",
        "address": "Jakarta",
        "role": "customer"
    }
}
```

### Response Error - Akses Ditolak (403)

```json
{
    "success": false,
    "message": "Anda tidak memiliki akses untuk melihat data user lain"
}
```

### Response Error - Tidak Ditemukan (404)

```json
{
    "success": false,
    "message": "User dengan ID 999 tidak ditemukan"
}
```

## POST /api/user/logout

### Response Sukses (200)

```json
{
    "success": true,
    "message": "Logout berhasil"
}
```

### Response Error (500)

```json
{
    "success": false,
    "message": "Gagal melakukan logout",
    "error": "Detail error message"
}
```
