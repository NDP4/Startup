# Format Response API Bus

## GET /api/buses (Index)

### Response Sukses - Ada Data (200)

```json
{
    "success": true,
    "message": "Data bus berhasil diambil",
    "data": [
        {
            "id": 1,
            "name": "Bus A",
            "number_plate": "B 1234 XY",
            "description": "Bus Luxury",
            "default_seat_capacity": 40,
            "status": "available",
            "pricing_type": "daily",
            "price_per_day": "1000000.00",
            "price_per_km": null,
            "created_at": "2024-01-20T12:00:00.000000Z",
            "updated_at": "2024-01-20T12:00:00.000000Z"
        }
    ],
    "total": 1
}
```

### Response Sukses - Data Kosong (200)

```json
{
    "success": true,
    "message": "Data bus berhasil diambil",
    "data": [],
    "total": 0
}
```

## POST /api/buses

### Response Sukses (201)

```json
{
    "success": true,
    "message": "Bus berhasil ditambahkan",
    "data": {
        "id": 1,
        "name": "Bus A",
        "number_plate": "B 1234 XY",
        "description": "Bus Luxury",
        "default_seat_capacity": 40,
        "status": "available",
        "pricing_type": "daily",
        "price_per_day": "1000000.00"
    }
}
```

### Response Error Validasi (422)

```json
{
    "success": false,
    "message": "Validasi gagal",
    "errors": {
        "name": ["Nama bus harus diisi"],
        "number_plate": ["Nomor plat sudah digunakan"],
        "default_seat_capacity": ["Kapasitas kursi harus berupa angka"]
    }
}
```

## GET /api/buses/{id}

### Response Sukses (200)

```json
{
    "success": true,
    "message": "Data bus ditemukan",
    "data": {
        "id": 1,
        "name": "Bus A",
        "number_plate": "B 1234 XY",
        "description": "Bus Luxury",
        "default_seat_capacity": 40,
        "status": "available",
        "pricing_type": "daily",
        "price_per_day": "1000000.00"
    }
}
```

### Response Error - Tidak Ditemukan (404)

```json
{
    "success": false,
    "message": "Bus dengan ID 2 tidak ditemukan"
}
```

## PUT /api/buses/{id}

### Response Sukses (200)

```json
{
    "success": true,
    "message": "Bus berhasil diperbarui",
    "data": {
        "id": 1,
        "name": "Bus A Update",
        "status": "maintenance"
    }
}
```

## DELETE /api/buses/{id}

### Response Sukses (200)

```json
{
    "success": true,
    "message": "Bus berhasil dihapus"
}
```

### Response Error Server (500)

```json
{
    "success": false,
    "message": "Gagal menghapus bus",
    "error": "Detail error message"
}
```
