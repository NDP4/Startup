# Format Response API Pemesanan Bus

## GET /api/bookings (Index)

### Response Sukses - Ada Data (200)

```json
{
    "success": true,
    "message": "Data pemesanan berhasil diambil",
    "data": [
        {
            "id": 1,
            "customer_id": 1,
            "bus_id": 1,
            "booking_date": "2024-02-01",
            "return_date": "2024-02-02",
            "total_seats": 2,
            "seat_type": "standard",
            "pickup_location": "Jakarta",
            "destination": "Bandung",
            "status": "pending",
            "payment_status": "pending",
            "total_amount": "500000.00",
            "special_requests": null,
            "created_at": "2024-01-20T12:00:00.000000Z",
            "updated_at": "2024-01-20T12:00:00.000000Z",
            "customer": {
                "id": 1,
                "name": "John Doe",
                "email": "john@example.com"
            },
            "bus": {
                "id": 1,
                "name": "Bus A",
                "number_plate": "B 1234 XY"
            }
        },
        {
            "id": 3,
            "customer_id": 2,
            "bus_id": 2,
            "booking_date": "2024-02-05",
            "return_date": "2024-02-06",
            "total_seats": 4,
            "seat_type": "legrest",
            "pickup_location": "Surabaya",
            "destination": "Malang",
            "status": "confirmed",
            "payment_status": "paid",
            "total_amount": "750000.00",
            "special_requests": "Tolong sediakan air mineral",
            "created_at": "2024-01-21T14:30:00.000000Z",
            "updated_at": "2024-01-21T14:30:00.000000Z",
            "customer": {
                "id": 2,
                "name": "Jane Doe",
                "email": "jane@example.com"
            },
            "bus": {
                "id": 2,
                "name": "Bus B",
                "number_plate": "B 5678 XY"
            }
        }
    ],
    "total": 2
}
```

### Response Sukses - Data Kosong (200)

```json
{
    "success": true,
    "message": "Data pemesanan berhasil diambil",
    "data": [],
    "total": 0
}
```

## GET /api/bookings/{id} (Show)

### Response Sukses - Data Ditemukan (200)

```json
{
    "success": true,
    "message": "Data pemesanan ditemukan",
    "data": {
        "id": 1,
        "customer_id": 1,
        "bus_id": 1,
        "booking_date": "2024-02-01",
        "return_date": "2024-02-02",
        "total_seats": 2,
        "seat_type": "standard",
        "pickup_location": "Jakarta",
        "destination": "Bandung",
        "status": "pending",
        "payment_status": "pending",
        "total_amount": "500000.00",
        "special_requests": null,
        "created_at": "2024-01-20T12:00:00.000000Z",
        "updated_at": "2024-01-20T12:00:00.000000Z",
        "customer": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com"
        },
        "bus": {
            "id": 1,
            "name": "Bus A",
            "number_plate": "B 1234 XY"
        }
    }
}
```

### Response Error - Data Tidak Ditemukan (404)

```json
{
    "success": false,
    "message": "Data pemesanan dengan ID 2 tidak ditemukan"
}
```

### Response Error Server (500)

```json
{
    "success": false,
    "message": "Gagal mengambil data pemesanan",
    "error": "Detail error message"
}
```

## POST /api/bookings

### Response Sukses (201)

```json
{
    "success": true,
    "message": "Pemesanan berhasil dibuat",
    "data": {
        "id": 1,
        "customer_id": 1,
        "bus_id": 1,
        "booking_date": "2024-02-01",
        "return_date": "2024-02-02",
        "total_seats": 2,
        "seat_type": "standard",
        "pickup_location": "Jakarta",
        "destination": "Bandung",
        "status": "pending",
        "payment_status": "pending",
        "total_amount": "500000.00"
    }
}
```

### Response Error Validasi (422)

```json
{
    "success": false,
    "message": "Validasi gagal",
    "errors": {
        "bus_id": ["Bus harus dipilih"],
        "booking_date": ["Tanggal pemesanan harus diisi"],
        "return_date": ["Tanggal kembali harus setelah tanggal pemesanan"],
        "total_seats": ["Jumlah kursi minimal 1"]
    }
}
```

## PUT /api/bookings/{id}

### Response Sukses (200)

```json
{
    "success": true,
    "message": "Pemesanan berhasil diperbarui",
    "data": {
        "id": 1,
        "status": "cancelled",
        "special_requests": "Permintaan khusus yang diperbarui"
    }
}
```

### Response Error - Tidak Dapat Diubah (403)

```json
{
    "success": false,
    "message": "Tidak dapat mengubah pemesanan yang sudah dikonfirmasi"
}
```

## DELETE /api/bookings/{id}

### Response Sukses (200)

```json
{
    "success": true,
    "message": "Pemesanan berhasil dibatalkan"
}
```

### Response Error - Tidak Dapat Dihapus (403)

```json
{
    "success": false,
    "message": "Tidak dapat menghapus pemesanan yang sudah dikonfirmasi"
}
```

### Response Error - Tidak Ditemukan (404)

```json
{
    "success": false,
    "message": "Pemesanan tidak ditemukan"
}
```

### Response Error - Akses Ditolak (403)

```json
{
    "success": false,
    "message": "Anda tidak memiliki akses ke pemesanan ini"
}
```

## Response Error Token (401)

### Token Tidak Valid atau Tidak Ada

```json
{
    "success": false,
    "message": "Unauthorized",
    "error": "Token tidak valid atau telah kadaluarsa"
}
```

### Token Kadaluarsa

```json
{
    "success": false,
    "message": "Unauthorized",
    "error": "Token telah kadaluarsa"
}
```
