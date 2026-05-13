# Integrasi Payment Methods & Cash Flow - Dokumentasi Lengkap

## Ringkasan Eksekutif

Modul **Payment Methods** berfungsi sebagai master data yang mengontrol:
1. **Metode pembayaran** yang tersedia di sistem
2. **Tipe pembayaran** (Cash vs Non-Cash) yang menentukan logika cash flow
3. **Logo/Ikon** untuk identifikasi visual di POS dan laporan

Sistem membedakan:
- **Cash**: Uang tunai → otomatis masuk ke cash flow
- **Non-Cash** (QRIS, Transfer, Kartu): Perlu verifikasi manual → baru masuk cash flow

---

## 1. Struktur Database

### Tabel: `payment_methods`

```sql
CREATE TABLE payment_methods (
    id BIGINT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    logo VARCHAR(255) NULLABLE,
    description TEXT NULLABLE,
    is_active BOOLEAN DEFAULT true,
    is_cash BOOLEAN DEFAULT true COMMENT 'true = Cash, false = Non-Cash',
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    deleted_at TIMESTAMP (soft delete)
);
```

### Kolom Penting:
- **`name`**: Nama unik metode (Tunai, QRIS, Transfer Bank, dll)
- **`logo`**: Path file logo di storage (untuk POS & struk)
- **`is_cash`**: Penentu alur cash flow
  - `true` = Tunai langsung masuk kas
  - `false` = Perlu verifikasi sebelum masuk kas
- **`is_active`**: Toggle aktif/nonaktif untuk transaksi baru
- **`deleted_at`**: Soft delete untuk audit trail

---

## 2. Model Relationships

### PaymentMethod Model
```php
class PaymentMethod extends Model {
    // Relasi ke Transaction
    public function transactions(): HasMany {
        return $this->hasMany(Transaction::class);
    }
    
    // Helper method
    public function isCashPayment(): bool {
        return $this->is_cash === true;
    }
    
    public function getLogoUrlAttribute() {
        return $this->logo ? asset('storage/' . $this->logo) : null;
    }
}
```

### Transaction Model
```php
class Transaction extends Model {
    // Relasi ke PaymentMethod
    public function paymentMethod(): BelongsTo {
        return $this->belongsTo(PaymentMethod::class);
    }
    
    // Relasi ke TransactionItem
    public function transactionItems(): HasMany {
        return $this->hasMany(TransactionItem::class);
    }
}
```

---

## 3. Controller & Views

### PaymentMethodController
- **Endpoints**:
  - `GET /payment-method` - List methods (with soft delete filter)
  - `POST /payment-method` - Create method (validation + logo upload)
  - `PUT /payment-method/{id}` - Update method
  - `DELETE /payment-method/{id}` - Soft delete
  - `POST /payment-method/{id}/restore` - Restore deleted

- **Features**:
  - Logo upload & storage management
  - Toggle is_cash & is_active
  - Support soft delete & restore
  - Pagination & listing

### Views
- `index.blade.php` - List dengan logo preview, tipe pembayaran badge, status
- `create.blade.php` - Form dengan radio toggle Cash/Non-Cash
- `edit.blade.php` - Edit dengan logo preview side-by-side form

---

## 4. Seeding Default Data

### Default Payment Methods
```php
[
    ['name' => 'Tunai', 'is_cash' => true, 'is_active' => true],
    ['name' => 'QRIS', 'is_cash' => false, 'is_active' => true],
    ['name' => 'Transfer Bank', 'is_cash' => false, 'is_active' => true],
    ['name' => 'Kartu Kredit', 'is_cash' => false, 'is_active' => true],
    ['name' => 'Debit Card', 'is_cash' => false, 'is_active' => true],
    ['name' => 'E-Wallet', 'is_cash' => false, 'is_active' => true],
]
```

---

## 5. Integrasi dengan Transaksi

### Di TransaksiController
```php
public function index() {
    $transactions = Transaction::with(['paymentMethod', 'transactionItems.product'])
        ->latest('transaction_date')
        ->paginate(20);
}

// Setiap transaksi HARUS memiliki payment_method_id yang valid
```

### Di View Transaksi
- **List View**: Tampilkan logo metode pembayaran + badge Cash/Non-Cash
- **Detail View**: 
  - Logo + Nama metode
  - Badge tipe (Cash vs Non-Cash)
  - Info box: Untuk Cash → "otomatis ke kas", Untuk Non-Cash → "perlu verifikasi"

---

## 6. Business Logic: Cash vs Non-Cash

### Transaksi dengan is_cash = TRUE (Cash)
```
User bayar tunai → Transaksi dicatat "completed" 
→ OTOMATIS masuk ke CashFlow (incoming cash)
→ Laporan kas mencatat uang masuk
→ Benar untuk: Tunai, Debit tunai, dll
```

### Transaksi dengan is_cash = FALSE (Non-Cash)
```
User bayar QRIS/Transfer → Transaksi dicatat "pending"
→ ADMIN/KASIR verifikasi bukti pembayaran (screenshot, ref bank, dll)
→ Setelah verifikasi: status "completed" + masuk CashFlow
→ Laporan kas mencatat uang masuk (setelah verifikasi)
→ Benar untuk: QRIS, Transfer Bank, Kartu Kredit, E-wallet
```

---

## 7. CashFlow Integration (Future Implementation)

### Event: Ketika Transaksi Completed
```php
// Pseudo-code
if ($transaction->paymentMethod->is_cash === true) {
    // Cash payment: langsung ke kas
    CashFlow::create([
        'reference_type' => 'transaction',
        'reference_id' => $transaction->id,
        'type' => 'in',
        'amount' => $transaction->total_amount,
        'payment_method_id' => $transaction->payment_method_id,
        'description' => "Transaksi Cash #{$transaction->invoice_no}",
        'status' => 'verified',
    ]);
} else {
    // Non-cash payment: pending verification
    CashFlow::create([
        'reference_type' => 'transaction',
        'reference_id' => $transaction->id,
        'type' => 'in',
        'amount' => $transaction->total_amount,
        'payment_method_id' => $transaction->payment_method_id,
        'description' => "Transaksi {$transaction->paymentMethod->name} #{$transaction->invoice_no} - Pending Verifikasi",
        'status' => 'pending',
    ]);
}
```

---

## 8. Reports Integration

### Report Penjualan
```php
// Query transaksi by payment method type
$cashTransactions = Transaction::whereHas('paymentMethod', fn($q) => $q->where('is_cash', true))
    ->whereBetween('transaction_date', [$from, $to])
    ->sum('total_amount');

$nonCashTransactions = Transaction::whereHas('paymentMethod', fn($q) => $q->where('is_cash', false))
    ->whereBetween('transaction_date', [$from, $to])
    ->sum('total_amount');
```

### Dashboard Metrics
- Total transaksi per metode pembayaran
- Grafik komposisi Cash vs Non-Cash
- Pending verification count (untuk Non-Cash)
- Metode pembayaran paling populer

### Laporan Kas
```php
// Hanya Cash + verified Non-Cash
$cashFlow = CashFlow::where('status', 'verified')
    ->where('type', 'in')
    ->with('paymentMethod')
    ->get();
```

---

## 9. Storage & File Management

### Logo Upload
- **Directory**: `storage/app/public/payment-methods/`
- **Formats**: JPEG, PNG, GIF, WEBP
- **Max Size**: 2MB
- **Display Size**: 40x40px di list, 80x80px di detail

### File Handling
```php
// Upload
$path = $request->file('logo')->store('payment-methods', 'public');
$payment_method->logo = $path;

// Delete old before update
if ($payment_method->logo && Storage::disk('public')->exists($payment_method->logo)) {
    Storage::disk('public')->delete($payment_method->logo);
}

// Display
<img src="{{ asset('storage/' . $method->logo) }}" />
```

---

## 10. Security & Validation

### Validation Rules
```php
'name' => 'required|string|max:255|unique:payment_methods,name',
'description' => 'nullable|string',
'is_cash' => 'boolean',
'is_active' => 'boolean',
'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
```

### Authorization
- Hanya admin/manager yang bisa manage payment methods
- Tidak bisa menghapus metode yang masih digunakan transaksi aktif (future: soft delete + warning)
- Restore hanya dari admin panel

---

## 11. Audit Trail

### Soft Delete Strategy
```php
// List dengan filter dihapus
PaymentMethod::withTrashed()->latest()->paginate(15);

// Tampilkan status "Dihapus" di tabel
@if($method->trashed())
    <span class="badge-gray">Dihapus</span>
@endif

// Tombol restore untuk deleted items
@if($method->trashed())
    <form action="{{ route('payment-method.restore', $method->id) }}" method="POST">
        @csrf
        <button>Pulihkan</button>
    </form>
@endif
```

---

## 12. Checklist Implementasi

### ✅ Done
- [x] Migration: add `logo` & `is_cash` columns
- [x] Model: PaymentMethod dengan methods
- [x] Controller: PaymentMethodController CRUD
- [x] Views: index, create, edit
- [x] Routes: resource routes + restore
- [x] Seeding: 6 default payment methods
- [x] Sidebar: Menu item "Metode Pembayaran"
- [x] Transaksi: Update list & detail dengan logo + badge

### ⏳ To Do (Next Phase)
- [ ] CashFlow integration: Auto-create cash flow untuk Cash methods
- [ ] Verification system: Admin verify Non-Cash sebelum masuk kas
- [ ] Reports: Payment method analytics
- [ ] Dashboard: Payment method metrics
- [ ] POS: Tampilkan metode pembayaran dengan logo saat checkout
- [ ] Validation: Prevent delete metode yang masih aktif di transaksi

---

## 13. Testing Checklist

```bash
# Test Routes
- GET /payment-method → List semua metode
- GET /payment-method/create → Form create
- POST /payment-method → Simpan metode baru
- GET /payment-method/{id}/edit → Form edit
- PUT /payment-method/{id} → Update metode
- DELETE /payment-method/{id} → Soft delete
- POST /payment-method/{id}/restore → Restore

# Test Features
- Logo upload & storage
- is_cash toggle behavior
- is_active status
- Soft delete + restore
- Transaksi tampilkan logo & badge
- Non-Cash info box di detail transaksi
```

---

## 14. References

- **Database**: migrations/2026_04_20_000000_add_logo_and_is_cash_to_payment_methods_table.php
- **Model**: app/Models/PaymentMethod.php
- **Controller**: app/Http/Controllers/PaymentMethodController.php
- **Views**: resources/views/pages/payment-method/
- **Routes**: routes/web.php (payment-method resource)
- **Seeder**: database/seeders/PaymentMethodSeeder.php

