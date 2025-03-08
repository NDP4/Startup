# Format Response API Konfigurasi Kursi

## GET /api/seat-configurations

### Response Sukses - Ada Data (200)

```json
{
    "success": true,
    "message": "Data konfigurasi kursi berhasil diambil",
    "data": [
        {
            "id": 1,
            "bus_id": 1,
            "seat_type": "standard",
            "number_of_seats": 40,
            "price_per_seat": "50000.00",
            "created_at": "2024-01-20T12:00:00.000000Z",
            "updated_at": "2024-01-20T12:00:00.000000Z",
            "bus": {
                "id": 1,
                "name": "Bus A",
                "number_plate": "B 1234 XY"
            }
        },
        {
            "id": 2,
            "bus_id": 1,
            "seat_type": "legrest",
            "number_of_seats": 20,
            "price_per_seat": "75000.00",
            "created_at": "2024-01-20T12:00:00.000000Z",
            "updated_at": "2024-01-20T12:00:00.000000Z",
            "bus": {
                "id": 1,
                "name": "Bus A",
                "number_plate": "B 1234 XY"
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
    "message": "Data konfigurasi kursi berhasil diambil",
    "data": [],
    "total": 0
}
```

## POST /api/seat-configurations

### Response Sukses (201)

```json
{
    "success": true,
    "message": "Konfigurasi kursi berhasil ditambahkan",
    "data": {
        "id": 1,
        "bus_id": 1,
        "seat_type": "standard",
        "number_of_seats": 40,
        "price_per_seat": "50000.00",
        "created_at": "2024-01-20T12:00:00.000000Z",
        "updated_at": "2024-01-20T12:00:00.000000Z"
    }
}
```

### Response Error - Validasi (422)

```json
{
    "success": false,
    "message": "Validasi gagal",
    "errors": {
        "bus_id": ["Bus harus dipilih"],
        "seat_type": ["Tipe kursi tidak valid"],
        "number_of_seats": ["Jumlah kursi minimal 1"],
        "price_per_seat": ["Harga per kursi harus diisi"]
    }
}
```

### Response Error - Akses Ditolak (403)

```json
{
    "success": false,
    "message": "Anda tidak memiliki akses untuk menambah konfigurasi kursi"
}
```

## GET /api/seat-configurations/{id}

### Response Sukses (200)

```json
{
    "success": true,
    "message": "Data konfigurasi kursi ditemukan",
    "data": {
        "id": 1,
        "bus_id": 1,
        "seat_type": "standard",
        "number_of_seats": 40,
        "price_per_seat": "50000.00",
        "created_at": "2024-01-20T12:00:00.000000Z",
        "updated_at": "2024-01-20T12:00:00.000000Z",
        "bus": {
            "id": 1,
            "name": "Bus A",
            "number_plate": "B 1234 XY"
        }
    }
}
```

### Response Error - Tidak Ditemukan (404)

```json
{
    "success": false,
    "message": "Konfigurasi kursi tidak ditemukan"
}
```

## PUT /api/seat-configurations/{id}

### Response Sukses (200)

```json
{
    "success": true,
    "message": "Konfigurasi kursi berhasil diperbarui",
    "data": {
        "id": 1,
        "bus_id": 1,
        "seat_type": "legrest",
        "number_of_seats": 45,
        "price_per_seat": "75000.00",
        "updated_at": "2024-01-20T12:30:00.000000Z"
    }
}
```

### Response Error - Validasi (422)

```json
{
    "success": false,
    "message": "Validasi gagal",
    "errors": {
        "seat_type": ["Tipe kursi harus standard atau legrest"],
        "number_of_seats": ["Jumlah kursi harus berupa angka"],
        "price_per_seat": ["Harga per kursi tidak boleh negatif"]
    }
}
```

## DELETE /api/seat-configurations/{id}

### Response Sukses (200)

```json
{
    "success": true,
    "message": "Konfigurasi kursi berhasil dihapus"
}
```

### Response Error - Akses Ditolak (403)

```json
{
    "success": false,
    "message": "Anda tidak memiliki akses untuk menghapus konfigurasi kursi"
}
```

### Response Error Server (500)

```json
{
    "success": false,
    "message": "Terjadi kesalahan saat memproses permintaan",
    "error": "Detail pesan error"
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
