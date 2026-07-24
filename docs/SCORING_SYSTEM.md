# Sistem Penilaian (Scoring System) — CTF Undiksha

## Daftar Isi

1. [Gambaran Umum](#gambaran-umum)
2. [Alur Pengiriman Flag](#alur-pengiriman-flag)
3. [Skor Dinamis (Dynamic Scoring)](#skor-dinamis)
4. [Penalti Jawaban Salah](#penalti-jawaban-salah)
5. [Sistem Peringkat (Ranking)](#sistem-peringkat)
6. [Pengaturan Per Event](#pengaturan-per-event)
7. [Contoh Perhitungan](#contoh-perhitungan)
8. [Proteksi Keamanan](#proteksi-keamanan)

---

## Gambaran Umum

Sistem penilaian CTF Undiksha menggunakan **Dynamic Scoring** — yaitu skor yang diperoleh peserta akan berkurang secara otomatis seiring bertambahnya jumlah tim yang berhasil menyelesaikan tantangan tersebut. Sistem ini memberikan keuntungan bagi tim yang berhasil menyelesaikan tantangan lebih awal (*first blood*).

### Komponen Utama

| Komponen | Deskripsi |
|----------|-----------|
| **Skor Dasar** (`base_score`) | Poin maksimal yang bisa diperoleh untuk sebuah tantangan |
| **Tingkat Degradasi** (`degradation_rate`) | Persentase penurunan skor per solver (0.00 - 1.00) |
| **Penalti Jawaban Salah** (`penalty_deduction`) | Poin yang dikurangi saat mengirim flag yang salah |
| **Skor Minimum** | 10% dari `base_score` (agar tantangan tidak pernah bernilai 0) |

---

## Alur Pengiriman Flag

Berikut adalah langkah-langkah yang terjadi saat peserta mengirim flag:

```
┌─────────────────────────────────────────────────────────┐
│                   Peserta Submit Flag                    │
└───────────────────────────┬─────────────────────────────┘
                            │
                            ▼
              ┌─────────────────────────┐
              │  Validasi:              │
              │  • Event aktif?         │
              │  • Waktu kompetisi?     │
              │  • Sudah punya tim?     │
              │  • Tantangan aktif?     │
              └───────────┬─────────────┘
                          │
                          ▼
              ┌─────────────────────────┐
              │  Database Lock          │
              │  (Mencegah duplikasi)   │
              └───────────┬─────────────┘
                          │
                          ▼
              ┌─────────────────────────┐
              │  Bandingkan Flag        │
              │  (trim whitespace)      │
              └───────┬─────────┬───────┘
                      │         │
                Benar │         │ Salah
                      ▼         ▼
          ┌───────────────┐  ┌──────────────────┐
          │ Cek: Tim sudah│  │ Hitung penalti   │
          │ solve?        │  │ = -penalty_ded.  │
          └──────┬────────┘  └────────┬─────────┘
                 │                    │
          ┌──────┴──────┐             │
          │Belum        │Sudah        │
          ▼             ▼             │
   ┌──────────┐  ┌───────────┐       │
   │Hitung    │  │ Tolak:    │       │
   │skor      │  │ "Sudah    │       │
   │dinamis   │  │  solved"  │       │
   └────┬─────┘  └───────────┘       │
        │                            │
        ▼                            ▼
   ┌─────────────────────────────────────┐
   │     Simpan Submission ke Database   │
   │     (user_id, team_id, flag,        │
   │      is_correct, points_awarded)    │
   └─────────────────────────────────────┘
```

---

## Skor Dinamis

### Formula

```
Skor = base_score × (1 - degradation_rate) ^ jumlah_tim_yang_sudah_solve
```

Dimana:
- `base_score` = Skor dasar yang ditetapkan untuk tantangan
- `degradation_rate` = Tingkat penurunan skor (dikonfigurasi per event)
- `jumlah_tim_yang_sudah_solve` = Berapa banyak tim yang sudah menyelesaikan tantangan ini **sebelum** tim saat ini

### Skor Minimum

Agar tantangan tidak pernah bernilai 0 poin, ada batas skor minimum:

```
Skor Minimum = ceil(base_score × 0.10)
```

Artinya, setiap tantangan akan selalu memberikan **minimal 10%** dari skor dasarnya, berapapun jumlah tim yang sudah menyelesaikannya.

### Keuntungan First Blood

Tim pertama yang menyelesaikan tantangan mendapatkan **skor penuh** (`base_score`), karena pada saat itu `jumlah_tim_yang_sudah_solve = 0`.

```
Skor first blood = base_score × (1 - degradation_rate)^0 = base_score × 1 = base_score
```

---

## Penalti Jawaban Salah

Setiap kali peserta mengirim flag yang **salah**, tim akan mendapat **pengurangan poin**:

```
Penalti = -1 × penalty_deduction
```

- Jika `penalty_deduction = 0`, maka tidak ada penalti (default)
- Jika `penalty_deduction = 5`, maka setiap jawaban salah mengurangi 5 poin dari total skor tim
- Penalti dihitung untuk **setiap** submit flag yang salah (bukan per tantangan)

> **Catatan:** Pengurangan penalti bisa membuat total skor tim menjadi **negatif** jika terlalu banyak mengirim jawaban salah.

---

## Dinamika dan Batasan Tim

Dalam kompetisi berbasis tim, pengiriman flag mewakili **seluruh tim**, bukan individu.

### 1. Representasi Tim
Setiap anggota tim yang berhasil mengirimkan flag yang benar akan memberikan poin untuk **keseluruhan tim**. Poin ini diakumulasikan ke dalam Total Skor tim.

### 2. Batasan Penyelesaian (Satu Kali per Tim)
Jika salah satu anggota tim telah berhasil menyelesaikan sebuah tantangan, maka:
- Tantangan tersebut dianggap **telah diselesaikan (solved)** oleh tim tersebut.
- Anggota tim lainnya **tidak bisa** mengirimkan flag untuk tantangan yang sama. Sistem akan menolak pengiriman dengan pesan "Tantangan ini sudah diselesaikan oleh tim Anda."
- Tim hanya mendapatkan poin satu kali untuk setiap tantangan.

### 3. Batasan Anggota (Max Team Size)
Setiap event memiliki batasan jumlah maksimal anggota per tim (diatur melalui `max_team_size`). Jika tim sudah mencapai batas ini, peserta lain tidak akan bisa bergabung dengan kode tim tersebut.

### 4. Limitasi Pengiriman (Rate Limiting)
Untuk mencegah eksploitasi dan serangan *brute-force* pada form pengiriman flag:
- Sistem membatasi pengiriman flag maksimal **10 kali per menit per pengguna**.
- Jika pengguna melampaui batas ini, mereka harus menunggu sebelum bisa mencoba mengirim flag lagi. (HTTP 429 Too Many Requests).

---

## Sistem Peringkat

### Kriteria Pemeringkatan

Peringkat tim ditentukan berdasarkan dua kriteria:

1. **Total Skor** (utama) — Tim dengan skor tertinggi mendapat peringkat lebih tinggi
2. **Waktu Solve Terakhir** (tiebreaker) — Jika dua tim memiliki skor yang sama, tim yang menyelesaikan tantangan terakhirnya **lebih awal** mendapat peringkat lebih tinggi

### Perhitungan Total Skor

```
Total Skor = Σ(semua points_awarded)
```

Total skor dihitung dari **semua** submission tim, termasuk:
- ✅ Poin dari jawaban benar (positif)
- ❌ Penalti dari jawaban salah (negatif)

---

## Pengaturan Per Event

Setiap event memiliki pengaturan scoring yang bisa dikonfigurasi oleh admin melalui panel admin:

| Pengaturan | Deskripsi | Default | Range |
|------------|-----------|---------|-------|
| `degradation_rate` | Tingkat penurunan skor | `0.05` (5%) | 0.00 - 1.00 |
| `penalty_deduction` | Pengurangan poin per jawaban salah | `0` | 0 - ∞ |
| `max_team_size` | Jumlah maksimal anggota per tim | `5` | 1 - 100 |

### Cara Mengatur

1. Buka panel admin → **Acara**
2. Edit event yang diinginkan
3. Di bagian **Pengaturan Skor**, atur nilai yang diinginkan
4. Klik **Simpan**

> **Penting:** Pengaturan ini harus dikonfigurasi **sebelum** kompetisi dimulai. Mengubah pengaturan di tengah kompetisi hanya akan mempengaruhi submission yang akan datang, bukan yang sudah tercatat.

---

## Contoh Perhitungan

### Skenario

Sebuah tantangan dengan `base_score = 500` pada event dengan `degradation_rate = 0.10` (10%) dan `penalty_deduction = 5`.

### Skor per Solver

| Solver Ke- | Formula | Skor yang Didapat |
|------------|---------|-------------------|
| 1 (First Blood) | `500 × (1 - 0.10)^0 = 500 × 1.00` | **500 poin** |
| 2 | `500 × (1 - 0.10)^1 = 500 × 0.90` | **450 poin** |
| 3 | `500 × (1 - 0.10)^2 = 500 × 0.81` | **405 poin** |
| 4 | `500 × (1 - 0.10)^3 = 500 × 0.729` | **365 poin** |
| 5 | `500 × (1 - 0.10)^4 = 500 × 0.6561` | **328 poin** |
| 10 | `500 × (1 - 0.10)^9 = 500 × 0.3874` | **194 poin** |
| 20 | `500 × (1 - 0.10)^19 = 500 × 0.1351` | **68 poin** |
| 44+ | Skor minimum = `ceil(500 × 0.10)` | **50 poin** |

### Skenario Penalti

Tim A mencoba menyelesaikan tantangan di atas:

| Aksi | Poin | Skor Kumulatif |
|------|------|----------------|
| Submit salah 1 | -5 | -5 |
| Submit salah 2 | -5 | -10 |
| Submit salah 3 | -5 | -15 |
| Submit **benar** (solver ke-3) | +405 | +390 |

**Total skor Tim A untuk tantangan ini: 390 poin** (405 - 15)

---

## Proteksi Keamanan

### Pencegahan Duplikasi (Race Condition)

Sistem menggunakan **database-level locking** (`SELECT ... FOR UPDATE`) di dalam transaksi untuk mencegah:

- Dua anggota tim yang mengirim flag benar secara bersamaan mendapat poin ganda
- Kondisi race condition pada perhitungan skor dinamis

### Throttling

Endpoint submission dibatasi **10 request per menit** per pengguna untuk mencegah brute-force.

### Validasi Waktu

- Flag tidak bisa dikirim **sebelum** waktu mulai event (`start_time`)
- Flag tidak bisa dikirim **setelah** waktu berakhir event (`end_time`)
- Tantangan hanya bisa diakses jika berstatus **aktif** (`is_active = true`)

### Trim Whitespace

Flag yang dikirim akan di-trim (spasi di awal/akhir dihapus) sebelum dibandingkan, untuk mencegah kesalahan akibat spasi yang tidak disengaja.
