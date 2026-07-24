# Sistem Penilaian (Scoring System) — CTF Undiksha

## Daftar Isi

1. [Gambaran Umum](#gambaran-umum)
2. [Alur Pengiriman Flag](#alur-pengiriman-flag)
3. [Skor Step-Down](#skor-step-down)
4. [Penalti Jawaban Salah](#penalti-jawaban-salah)
5. [Sistem Peringkat (Ranking)](#sistem-peringkat)
6. [Pengaturan Per Event](#pengaturan-per-event)
7. [Contoh Perhitungan](#contoh-perhitungan)
8. [Proteksi Keamanan](#proteksi-keamanan)

---

## Gambaran Umum

Sistem penilaian CTF Undiksha menggunakan **Step-Down Scoring** — yaitu tim pertama yang menyelesaikan tantangan mendapatkan **skor penuh**, sedangkan tim ke-2 dan seterusnya mendapatkan skor yang dikurangi sebesar persentase tertentu. Sistem ini memberikan keuntungan bagi tim yang berhasil menyelesaikan tantangan lebih awal (*first blood*).

### Komponen Utama

| Komponen | Deskripsi |
|----------|-----------|
| **Skor Dasar** (`base_score`) | Poin maksimal yang bisa diperoleh untuk sebuah tantangan |
| **Pengurangan Solver ke-2+** (`degradation_rate`) | Persentase pengurangan skor untuk solver ke-2 dan seterusnya (default: 10%) |
| **Persentase Penalti** (`penalty_deduction`) | Persentase skor dasar yang dikurangi per jawaban salah (default: 5%) |

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
          ┌───────────────┐  ┌──────────────────────┐
          │ Cek: Tim sudah│  │ Hitung penalti       │
          │ solve?        │  │ = -(base_score × 5%) │
          └──────┬────────┘  └────────┬─────────────┘
                 │                    │
          ┌──────┴──────┐             │
          │Belum        │Sudah        │
          ▼             ▼             │
   ┌──────────┐  ┌───────────┐       │
   │Hitung    │  │ Tolak:    │       │
   │skor      │  │ "Sudah    │       │
   │step-down │  │  solved"  │       │
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

## Skor Step-Down

### Formula

```
Solver ke-1 (First Blood):
  Skor = base_score

Solver ke-2 dan seterusnya:
  Skor = floor(base_score × (1 - degradation_rate))
```

Dimana:
- `base_score` = Skor dasar yang ditetapkan untuk tantangan
- `degradation_rate` = Persentase pengurangan skor (dikonfigurasi per event, default: 0.10 / 10%)

### Keuntungan First Blood

Tim pertama yang menyelesaikan tantangan mendapatkan **skor penuh** (`base_score`). Semua solver berikutnya mendapat skor yang sama, yaitu `base_score × 90%` (dengan default rate 10%).

**Tidak ada penurunan lebih lanjut** setelah solver ke-2. Solver ke-3, ke-4, ke-100, dst. semua mendapatkan skor yang sama dengan solver ke-2.

---

## Penalti Jawaban Salah

Setiap kali peserta mengirim flag yang **salah**, tim akan mendapat **pengurangan poin** berdasarkan persentase dari skor dasar tantangan:

```
Penalti = floor(base_score × penalty_rate)
```

- Jika `penalty_rate = 0.05` (5%), dan `base_score = 500`, maka penalti = `floor(500 × 0.05)` = **-25 poin**
- Jika `penalty_rate = 0`, maka tidak ada penalti
- Penalti dihitung untuk **setiap** submit flag yang salah (bukan per tantangan)
- Penalti bersifat **proporsional** — tantangan dengan skor lebih tinggi memiliki penalti lebih besar

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
| `degradation_rate` | Persentase pengurangan skor untuk solver ke-2+ | `0.10` (10%) | 0.00 - 1.00 |
| `penalty_deduction` | Persentase skor dasar yang dikurangi per jawaban salah | `0.05` (5%) | 0.00 - 1.00 |
| `max_team_size` | Jumlah maksimal anggota per tim | `3` | 1 - 100 |
| `show_solver_count` | Tampilkan jumlah solver ke peserta | `true` | true / false |

### Cara Mengatur

1. Buka panel admin → **Acara**
2. Edit event yang diinginkan
3. Di bagian **Pengaturan Skor**, atur nilai yang diinginkan
4. Klik **Simpan**

> **Penting:** Pengaturan ini harus dikonfigurasi **sebelum** kompetisi dimulai. Mengubah pengaturan di tengah kompetisi hanya akan mempengaruhi submission yang akan datang, bukan yang sudah tercatat.

---

## Contoh Perhitungan

### Skenario

Sebuah tantangan dengan `base_score = 500` pada event dengan `degradation_rate = 0.10` (10%) dan `penalty_deduction = 0.05` (5%).

### Skor per Solver

| Solver Ke- | Formula | Skor yang Didapat |
|------------|---------|-------------------|
| 1 (First Blood) | `base_score = 500` | **500 poin** |
| 2 | `floor(500 × (1 - 0.10)) = floor(500 × 0.90)` | **450 poin** |
| 3 | Sama dengan solver ke-2 | **450 poin** |
| 4 | Sama dengan solver ke-2 | **450 poin** |
| 10 | Sama dengan solver ke-2 | **450 poin** |
| 100 | Sama dengan solver ke-2 | **450 poin** |

### Skenario Penalti

Tim A mencoba menyelesaikan tantangan di atas:

| Aksi | Poin | Skor Kumulatif |
|------|------|----------------|
| Submit salah 1 | `floor(500 × 0.05)` = -25 | -25 |
| Submit salah 2 | -25 | -50 |
| Submit salah 3 | -25 | -75 |
| Submit **benar** (solver ke-3) | +450 | +375 |

**Total skor Tim A untuk tantangan ini: 375 poin** (450 - 75)

### Perbandingan Antar Tantangan

| Tantangan | Base Score | Penalti per Salah (5%) | Skor 1st | Skor 2nd+ |
|-----------|-----------|----------------------|----------|-----------|
| Easy | 100 | -5 | 100 | 90 |
| Medium | 250 | -12 | 250 | 225 |
| Hard | 500 | -25 | 500 | 450 |

---

## Proteksi Keamanan

### Pencegahan Duplikasi (Race Condition)

Sistem menggunakan **database-level locking** (`SELECT ... FOR UPDATE`) di dalam transaksi untuk mencegah:

- Dua anggota tim yang mengirim flag benar secara bersamaan mendapat poin ganda
- Kondisi race condition pada perhitungan skor

### Throttling

Endpoint submission dibatasi **10 request per menit** per pengguna untuk mencegah brute-force.

### Validasi Waktu

- Flag tidak bisa dikirim **sebelum** waktu mulai event (`start_time`)
- Flag tidak bisa dikirim **setelah** waktu berakhir event (`end_time`)
- Tantangan hanya bisa diakses jika berstatus **aktif** (`is_active = true`)

### Trim Whitespace

Flag yang dikirim akan di-trim (spasi di awal/akhir dihapus) sebelum dibandingkan, untuk mencegah kesalahan akibat spasi yang tidak disengaja.
