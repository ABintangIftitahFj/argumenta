# Product Requirements Document (PRD)

## 1. Ringkasan Produk

**Nama kerja:** AI Debate Coach  
**Platform:** Progressive Web App (PWA), mobile-first  
**Tahap:** MVP → Public Beta → Full Debate Simulator  
**Bahasa antarmuka dan latihan:** Bahasa Indonesia dan English  
**Inspirasi pengalaman belajar:** Duolingo (gamification dan learning path), dengan identitas visual dan maskot orisinal.

AI Debate Coach adalah platform latihan debat kompetitif dan kemampuan berbicara yang memungkinkan pengguna mempelajari mosi, berlatih pidato pada posisi tertentu, mendapatkan evaluasi argumentasi dan bahasa, serta melacak perkembangan dari waktu ke waktu. Pada fase lanjutan, AI mengisi posisi pembicara yang kosong dalam simulasi debat sesuai format kompetisi.

## 2. Masalah dan Peluang

- Akses pelatih dan lawan debat yang kompeten tidak selalu tersedia.
- Pelajar membutuhkan feedback spesifik tentang penalaran, rebuttal, struktur, kosakata, grammar, dan kelancaran bicara.
- Latihan mandiri sering tidak memiliki pengukuran perkembangan yang konsisten.
- Format debat memiliki peran dan aturan berbeda; chatbot umum sering mencampuradukkannya.

**Value proposition:** Berlatih kapan saja sebagai pembicara format debat yang dipilih; memperoleh coaching berbasis bukti dari transkrip/audio, evaluasi yang bisa ditindaklanjuti, serta jalur latihan adaptif.

## 3. Sasaran Pengguna

| Persona | Kebutuhan |
|---|---|
| Pemula debat | Memahami mosi, dasar argumen, dan struktur pidato |
| Pelajar/mahasiswa | Latihan teratur BP/AP, peningkatan bahasa Inggris |
| Debater kompetitif | Analisis clash, rebuttal, weighing, kontribusi peran |
| Pembelajar bahasa Inggris | Grammar, vocabulary, filler words, kejelasan bicara |
| Pelatih/komunitas (masa depan) | Meninjau kemajuan dan memberikan umpan balik |

## 4. Tujuan dan Indikator Sukses

### Tujuan MVP
1. Pengguna menyelesaikan pidato mandiri 7–8 menit (atau durasi sesuai aturan).
2. Pengguna mendapatkan transkrip yang dapat diperiksa/dikoreksi dan evaluasi berdasarkan peran.
3. Pengguna memahami tiga kekuatan dan tiga area perbaikan utama.
4. Pengguna dapat mengulang latihan dan melihat tren kemampuan.
5. Penggunaan AI terkendali melalui kuota dan pencatatan biaya.

### KPI awal (target hipotesis; divalidasi saat beta)
- ≥70% sesi rekaman yang berhasil diunggah mencapai status evaluasi selesai.
- ≥60% pengguna beta yang menyelesaikan evaluasi pertama memulai latihan kedua dalam 14 hari.
- ≥80% pengguna uji menyatakan feedback dapat ditindaklanjuti.
- ≥95% job evaluasi selesai tanpa intervensi manual setelah retry yang wajar.
- 100% pemanggilan AI tercatat berdasarkan pengguna, fitur, model, dan penggunaan yang dilaporkan provider.
- Tidak ada akses lintas pengguna ke rekaman maupun laporan privat pada pengujian otorisasi.

## 5. Ruang Lingkup dan Tahapan

| Area | MVP / Release 1 | Release 2 | Release 3 |
|---|---|---|---|
| PWA & autentikasi | Ya | Penguatan | Penguatan |
| AI Coach untuk mosi | Ya | Penyempurnaan | Knowledge retrieval |
| Speech Practice | Ya (BP, AP) | Australs | MUN |
| Rekam audio & STT | Ya | Peningkatan ketepatan | Analisis audio lanjut |
| AI Judge & bahasa | Ya | Kalibrasi lebih luas | Pengukuran lanjut |
| Progress & gamification | Ya | Program adaptif | Fitur komunitas |
| Full Debate dengan AI | Tidak | BP/AP, kursi AI | Australs/MUN sesuai desain khusus |
| TTS / POI interaktif | Tidak | Terbatas | Lanjutan |
| Pembayaran | Paket dan kuota dasar | Optimasi | Organisasi |

**Tidak termasuk MVP:** multiplayer langsung, turnamen publik, video conference, penilaian pronunciation tingkat fonem, live simultaneous speech-to-speech, RAG skala besar, atau klaim sertifikasi/penjurian WUDC resmi.

## 6. Alur Pengguna Utama

1. **Onboarding:** pilih bahasa UI, tujuan belajar, tingkat kemampuan, format favorit; buat akun atau login.
2. **Beranda:** tampilkan jalur belajar, streak, XP, sesi terakhir, tombol latihan cepat.
3. **Pilih mode:** AI Coach atau Speech Practice (Full Debate pada rilis berikutnya).
4. **Setup latihan:** pilih format, mosi, posisi pembicara, bahasa pidato, tingkat kesulitan; lihat durasi/aturan otomatis.
5. **Persiapan:** pengguna melihat brief posisi dan (jika perlu) konteks pidato sebelumnya; pilihan bantuan AI ditandai sebagai coached practice.
6. **Pidato:** timer, meter input, kontrol mulai/akhiri, indikator koneksi dan penyimpanan.
7. **Pengunggahan:** audio diunggah langsung ke object storage privat; sesi masuk antrean evaluasi.
8. **Transkripsi:** hasil disajikan untuk diperiksa; bagian tidak yakin ditandai. Pengguna dapat melakukan koreksi sebelum evaluasi akhir bila alur mengizinkan.
9. **Evaluasi:** laporan debat dan bahasa, cuplikan transkrip bertimestamp, saran perbaikan dan latihan ulang.
10. **Progres:** hasil masuk riwayat, grafik, pelacakan kesalahan, XP/streak, rekomendasi tantangan selanjutnya.

## 7. Kebutuhan Fungsional

### FR-01 — Akun, Profil, dan PWA
- Registrasi/login/logout, reset kata sandi, verifikasi email sesuai kebijakan.
- Profil menyimpan bahasa pilihan, level, format favorit, dan paket langganan.
- PWA installable dengan manifest, ikon orisinal, service worker, dan offline fallback untuk shell/konten yang aman.
- Rekaman dan evaluasi AI **memerlukan koneksi internet**; jangan menjanjikan evaluasi offline.
- UI responsif 320px hingga desktop; bottom navigation untuk perangkat seluler.

### FR-02 — Learning Path dan Gamification
- Jalur latihan: Fundamentals → Argumentation → Rebuttal → Weighing → Role Mastery → Full Simulation.
- XP diberikan hanya untuk aksi yang terverifikasi selesai; cegah farming dari refresh/retry.
- Streak dihitung berdasarkan hari kalender di zona waktu profil pengguna; sediakan aturan pemulihan transparan jika ada.
- Badge, level, daily goal, dan feedback perayaan menggunakan desain orisinal, tidak meniru aset Duolingo.
- Gamification tidak memengaruhi objektivitas skor debat.

### FR-03 — Format, Mosi, dan Peran
- Format awal: **British Parliamentary (BP)** dan **Asian Parliamentary (AP)**.
- Pengguna dapat memilih mosi dari katalog atau memasukkan mosi sendiri.
- Setiap format memiliki konfigurasi terversi: tim, posisi, urutan, durasi, POI, reply speech, syarat peran, dan rubrik.
- Posisi yang ditawarkan hanya posisi sah dalam format tersebut.
- **BP:** OG/OO/CG/CO, delapan pembicara, tanpa reply speech.
- **AP:** dua tim × tiga pembicara, pidato utama dan reply sesuai rulebook kompetisi yang dipilih; reply tidak boleh diberikan pada peran yang tidak memenuhi syarat.
- **Australs (rencana):** gunakan rulebook spesifik yang diverifikasi, jangan berasumsi identik dengan AP.
- **MUN (rencana):** komite, negara, agenda, rules of procedure, dan mekanisme caucus/resolusi terpisah dari debat parlementer.
- Ketika latihan pada peran yang membutuhkan konteks sebelumnya (mis. Whip/Closing), sediakan brief/pidato lawan sebelumnya atau label latihan sebagai *isolated speech drill* dengan metrik yang sesuai.

### FR-04 — AI Debate Coach
- Percakapan teks Indonesia/Inggris mengenai mosi, argument construction, burden, stakeholder, rebuttal, weighing, dan strategi peran.
- Persona: pelatih debat kompetitif berpenalaran mendalam **terinspirasi standar WUDC**, tanpa mengaku sebagai juara atau juri manusia resmi.
- Bimbingan adaptif: beginner, intermediate, advanced, elite.
- Respons mengutamakan pertanyaan pembimbing dan latihan terarah, bukan selalu menuliskan pidato jadi.
- Pisahkan contoh hipotetis dari data terverifikasi; jangan mengarang sumber atau aturan.
- Riwayat pesan menggunakan ringkasan konteks dan jendela percakapan untuk mengontrol token.

### FR-05 — Speech Practice dan Perekaman
- Browser meminta izin mikrofon secara eksplisit.
- Timer disesuaikan dengan format/peran; batas maksimum file, durasi, dan MIME type divalidasi.
- Dukungan playback sebelum submit, retake sebelum konfirmasi, peringatan navigasi saat merekam, dan status upload.
- Pengunggahan **langsung dari browser ke object storage** melalui presigned URL; credential penyimpanan tidak pernah dikirim ke klien.
- Simpan metadata audio (path privat, durasi, ukuran, codec, status, timestamp) di MySQL.
- Fitur stop/pause mengikuti aturan mode: bila pause diizinkan, sesi ditandai bukan simulasi kompetisi ketat.

### FR-06 — Speech-to-Text
- Transkripsikan audio menggunakan provider yang mendukung input audio; verifikasi kompatibilitas model dan gateway sebelum rilis.
- Pertahankan ucapan asli termasuk filler dan kesalahan bahasa; jangan memperbaiki grammar di transkrip dasar.
- Simpan segmen/timestamp **hanya bila tersedia atau telah diperoleh melalui alignment yang tervalidasi**.
- Tandai bagian dengan confidence rendah/ucapan tidak jelas; sediakan koreksi transkrip manual.
- Simpan transkrip asli dan versi terkoreksi; rekam sumber yang digunakan untuk evaluasi.

### FR-07 — AI Debate Judge
- Gunakan agent dan system instructions terpisah dari Coach dan Opponent.
- Masukan: mosi, format, peran, transkrip, speech context, rubrik terversi, pilihan bahasa, dan metrik audio yang tersedia.
- Keluaran terstruktur: skor tiap dimensi, kutipan spesifik, alasan, tingkat keparahan, langkah koreksi, contoh perbaikan, rekomendasi latihan.
- Dimensi inti: argument construction, reasoning, rebuttal/clash, comparative weighing, organization, role fulfillment.
- Untuk dimensi tanpa bukti/konteks, tampilkan **Not Assessed**, bukan angka nol yang menyesatkan.
- Bedakan logical fallacy, unsupported claim, missing mechanism, dan factual claim requiring verification.
- Nilai kompetisi/kemenangan hanya diperkirakan pada simulasi penuh dengan konteks cukup; bukan skor resmi WUDC.
- AI Judge tidak menerima instruksi dari teks pidato sebagai perintah sistem (*prompt-injection resistance*).

### FR-08 — English/Indonesian Language Coach
- Nilai grammar, pilihan kosakata, ketepatan ekspresi, pengulangan, organisasi wacana, dan filler words dalam konteks.
- Berikan kutipan asli, versi usulan, alasan, dan lokasi segmen jika timestamp tersedia.
- Metrik kecepatan bicara dihitung dari jumlah kata dan waktu bicara; jeda/noise membutuhkan analisis audio khusus.
- Pisahkan **Debate Score** dan **Language Score** agar aksen/kesalahan grammar tidak menghukum logika secara tidak adil.
- Jangan menyebut pronunciation, articulation, atau background noise sebagai hasil pengukuran jika hanya memiliki teks; tampilkan “Not Assessed”.
- Jika audio bising/ASR tidak pasti, beri peringatan kualitas rekaman dan jangan mengklaim kesalahan pengguna tanpa bukti.

### FR-09 — Hasil, Replay, dan Perbaikan
- Laporan berisi skor, ringkasan, 3 kekuatan, 3 prioritas perbaikan, dan latihan berikutnya.
- Audio replay privat dengan transkrip sinkron bila timestamp dapat dipercaya.
- Pengguna dapat membuka contoh koreksi pada segmen tertentu dan mengulang latihan singkat.
- Setiap hasil mencatat versi model, prompt, rubrik, dan sumber transkrip untuk audit/komparabilitas.

### FR-10 — Progress Tracking
- Riwayat sesi, skor per kategori, total waktu latihan, frekuensi kesalahan, dan perubahan dari waktu ke waktu.
- Grafik memakai data tersimpan; membuka dashboard tidak memanggil LLM ulang.
- Perbandingan skor hanya dilakukan untuk rubrik/format/jenis latihan yang sebanding, atau diberi penanda perbedaan.
- Rekomendasi terpersonalisasi dapat dihasilkan secara periodik/selektif, lalu disimpan dan dipakai ulang.

### FR-11 — Plan, Kuota, dan Billing
- **Free:** model teks SumoPod gratis bila benar-benar tersedia; latihan suara percobaan dibatasi.
- **Student:** akses evaluasi lengkap dengan kuota latihan bulanan.
- **Pro:** kuota lebih besar, analisis lintas sesi, dan program latihan adaptif.
- Harga/kuota awal merupakan hipotesis: Free Rp0; Student Rp29.000/bulan; Pro Rp59.000/bulan. Finalisasi setelah pengukuran biaya nyata.
- Kuota ditentukan berdasarkan jumlah sesi, chat, durasi, serta batas biaya internal; jangan menampilkan token mentah sebagai satu-satunya metrik produk.
- Debit kredit secara atomik dengan mekanisme reserve → settle/refund; request idempotent untuk retry.
- Gateway provider/model mendukung fallback hanya dalam batas biaya global; saat gratis tidak tersedia, tampilkan pesan yang jelas atau fallback berbiaya terbatas.

### FR-12 — Full Debate Simulation (Release 2)
- Pengguna memilih format, mosi, bahasa, tingkat kesulitan, dan **satu posisi**; AI mengisi posisi kosong yang sah.
- Laravel Debate Orchestrator mengontrol posisi, urutan giliran, timer, POI, dan status pertandingan, bukan LLM bebas.
- AI Opponent menerima state perdebatan sebelumnya dan wajib menanggapi kontribusi pengguna secara relevan.
- TTS membacakan pidato AI jika diaktifkan; konsumsi TTS dibatasi terpisah.
- BP: delapan posisi; AP: enam pembicara utama dan reply yang memenuhi syarat.
- Penilaian pertandingan dipisahkan dari evaluasi individual pengguna.
- MUN akan menggunakan mesin simulasi prosedural khusus pada rilis lanjut.

## 8. Halaman dan Navigasi

| Halaman | Konten Utama |
|---|---|
| Welcome/Onboarding | Pilih tujuan, format, bahasa, level |
| Home | Learning path, streak, XP, CTA latihan |
| Learn | Modul, tantangan, badge |
| AI Coach | Chat, prompt contoh, riwayat |
| Practice Setup | Mode, mosi, format, posisi, bahasa |
| Preparation | Brief peran, catatan, aturan, timer |
| Speech Room | Timer, wave meter, kontrol rekam |
| Processing | Status upload, transcribe, evaluate |
| Evaluation | Skor, feedback, contoh perbaikan |
| Transcript Replay | Audio + anotasi timestamp |
| Progress | Grafik skill, histori, kelemahan |
| Subscription | Paket, kuota, pembayaran |
| Profile/Settings | Bahasa, privasi, preferensi, hapus data |
| Full Debate Room (R2) | Kursi, urutan, AI speeches, POI, recap |

**Navigasi PWA mobile:** Home · Learn · Practice · Progress · Profile; AI Coach muncul sebagai CTA utama/akses cepat. Desktop menggunakan sidebar responsif.

## 9. Prinsip Desain UX/UI

- **Duolingo-inspired, not copied:** perjalanan belajar gamified, feedback positif, progress visual, kartu membulat, animasi kecil, dan maskot **orisinal** (nama kerja: Kiko).
- Warna awal: teal green `#20B87A`, dengan cream, lime, orange, violet sebagai aksen; semua tetap harus lulus pengujian kontras.
- Mobile-first dengan target sentuh ≥44px, teks dapat diperbesar, status jelas, akses keyboard, dan dukungan reduced motion.
- Sesi 7–8 menit memiliki **focus mode**: UI sederhana, timer besar, status mikrofon terlihat, tanpa animasi berlebihan.
- Menampilkan feedback suportif tetapi spesifik, tidak menghukum aksen atau membuat klaim evaluasi yang tidak didukung data.
- English/Indonesian switch tersedia untuk UI dan sesi; keduanya dapat dipilih secara terpisah.

## 10. Arsitektur Teknis

| Lapisan | Pilihan |
|---|---|
| Backend + SSR halaman | Laravel 13 + Inertia.js (tanpa runtime SSR Node di production) |
| UI | Vue 3 + TypeScript + Tailwind CSS |
| Database | MySQL pada hosting cPanel |
| AI orchestration | Laravel AI SDK jika dukungan provider cocok; adapter HTTP custom bila perlu |
| Model | Gemini (premium), SumoPod (Free bila sesuai penawaran aktual) |
| Speech | MediaRecorder API; STT melalui provider yang telah diuji |
| Object storage | S3-compatible eksternal (SumoPod jika kompatibel, alternatif R2) |
| Background jobs | Laravel Database Queue; cron cPanel atau worker terpisah |
| PWA | Web manifest + service worker; cache aman, update terkontrol |
| Grafik | Chart.js / ApexCharts |
| Deploy | cPanel dengan document root ke Laravel `public/` |

### Pemrosesan sesi

```text
Vue MediaRecorder
   → Laravel buat sesi + presigned upload URL
   → Browser upload audio ke private object storage
   → Laravel validasi objek dan enqueue job
   → STT provider → simpan transcript
   → AI Judge → structured evaluation
   → MySQL menyimpan metrik dan laporan
   → Vue menampilkan progress/status dan hasil
```

### AI orchestration

```text
Authenticated user + plan
    → quota / budget guard
    → Coach / Judge / Opponent role resolver
    → format-specific rules + persona + level + language
    → provider adapter (Gemini / SumoPod)
    → usage logging + cost calculation
    → typed output validation + persistence
```

## 11. Model Data Minimum

- `users`, `profiles`
- `debate_formats`, `format_rule_versions`, `debate_roles`, `motions`
- `debate_sessions`, `debate_speeches`, `speech_recordings`
- `transcripts`, `transcript_segments`, `evaluations`, `evaluation_issues`
- `learning_modules`, `user_learning_progress`, `xp_transactions`, `streaks`
- `ai_usage_logs`, `ai_jobs`, `user_credits`, `credit_transactions`
- `plans`, `subscriptions`, `payments`

**Prinsip:** file audio disimpan sebagai objek privat; MySQL hanya menyimpan metadata/path, transkrip, skor, dan jejak proses.

## 12. Keamanan, Privasi, dan Kepatuhan

- Semua akses menggunakan HTTPS; API key hanya ada di backend.
- Object storage privat + URL bertanda tangan dengan TTL singkat.
- Validasi MIME/ukuran/durasi file, autentikasi, otorisasi resource, rate limit, dan CSRF.
- Pengguna harus menyetujui perekaman dan pemrosesan suara oleh penyedia AI; cantumkan kebijakan retensi.
- Retensi audio awal yang diusulkan: Free 7 hari, Student 30 hari, Pro 90 hari; bisa diubah dan dikomunikasikan secara transparan.
- Sediakan hapus akun/data serta ekspor riwayat yang memungkinkan.
- Jangan gunakan transkrip pengguna lain untuk melatih/menilai tanpa persetujuan yang sah.
- Log teknis menghindari penyimpanan token rahasia, signed URL aktif, atau teks sensitif secara berlebihan.
- Batasi akses admin dan rekam aktivitas administratif.

## 13. Reliability, Performance, dan Batasan Hosting

- Pekerjaan STT/penilaian asynchronous: `pending`, `transcribing`, `evaluating`, `completed`, `failed`.
- Retry dengan backoff dan idempotency; error yang dapat diperbaiki dipisahkan dari error final.
- Kinerja background worker pada cPanel harus diuji; bila cron/proses dibatasi, worker pindah ke layanan terpisah tanpa memigrasikan aplikasi web.
- Uji 8-minute upload pada jaringan seluler tidak stabil, termasuk resumable/retry jika storage mendukung.
- Hindari memperlakukan 1.000 pengguna terdaftar sebagai 1.000 concurrent users; load test sesuai pola penggunaan.
- Log biaya per job, metrik keberhasilan STT, tingkat kegagalan, durasi antrean, dan token tiap model.

## 14. Kriteria Penerimaan MVP

1. Pengguna baru berhasil mendaftar dan membuka PWA pada Android dan desktop.
2. Pengguna memilih BP atau AP, mosi, posisi valid, dan bahasa Indonesia/Inggris.
3. Pengguna merekam hingga 8 menit lalu unggah tanpa membocorkan credential storage.
4. Transkripsi mempertahankan filler dan kesalahan asli; koreksi pengguna tercatat terpisah.
5. AI Judge mengembalikan struktur JSON valid, mengutip bagian pidato nyata, dan membedakan *not assessed* dari skor rendah.
6. Evaluasi bahasa menampilkan saran grammar/vocabulary dan tidak mengklaim pronunciation/noise dari teks saja.
7. Dashboard menampilkan minimal tiga sesi dan perbandingan skor relevan tanpa panggilan AI setiap kunjungan.
8. Kuota Free/Student/Pro dan batas budget menolak penggunaan di luar jatah secara konsisten, termasuk concurrent requests.
9. Tidak ada pengguna yang dapat mengakses rekaman/laporan pengguna lain.
10. Job yang gagal menunjukkan status dan jalur retry/refund yang benar; tidak ada debit kredit ganda.
11. Alur utama bisa diselesaikan pada layar 320px tanpa horizontal overflow.
12. Pengujian beta membuktikan ketersediaan model yang dipilih dan kapasitas hosting/worker.

## 15. Rencana Pengiriman

| Minggu | Fokus | Hasil |
|---|---|---|
| 1–2 | PRD, ERD, UI dasar, auth, PWA shell | Foundation |
| 3–4 | AI Gateway/Coach, format & role engine | Coach + setup |
| 5–6 | Rekam, direct storage, STT & queue | Speech pipeline |
| 7–8 | Judge, bahasa, transcript feedback | Speech MVP |
| 9–10 | Progress, XP/streak, plans/credits | Learning & monetization |
| 11–12 | Testing, kalibrasi, deploy beta | Public beta |
| 13–18 | Debate orchestrator, AI seats, TTS, POI | Full Debate beta |

**Go/no-go public beta:** evaluasi stabil, sumber penilaian dapat ditelusuri, privasi memadai, biaya AI terkendali, dan queue pada hosting produksi terbukti berjalan.

## 16. Risiko dan Mitigasi

| Risiko | Mitigasi |
|---|---|
| Model gratis SumoPod tidak tersedia | Konfigurasi dinamis; fallback beranggaran atau nonaktif sementara |
| Biaya audio/output melebihi estimasi | Catat usage aktual, pembatasan sesi & model, spend cap |
| STT salah karena noise/aksen | Confidence flag, transkrip terkoreksi, jangan hukum tanpa bukti |
| AI Judge tidak konsisten | Rubrik terversi, structured output, gold set penilaian manusia |
| Aturan AP/Australs/MUN bervariasi | Rulebook per turnamen, jangan hardcode aturan universal |
| Shared hosting membatasi background processing | Queue tahan gagal, cron test, worker eksternal opsional |
| Banyak sesi AI simulasi mahal | Rilis bertahap; budget TTS dan full-debate terpisah |
| Gamification mendorong spam | XP berbasis sesi valid dan batas harian |

## 17. Dependensi dan Pertanyaan Terbuka

- Nama merek produk final, logo, dan desain maskot orisinal.
- Rulebook resmi dan versi yang diadopsi untuk setiap format, terutama AP/Australs/MUN.
- Kualitas serta ketersediaan model Gemini/SumoPod melalui gateway yang dipilih.
- Apakah penyimpanan SumoPod mendukung S3 API, CORS, dan presigned PUT/GET.
- Ketentuan data processing, retensi, dan kebutuhan pengguna di bawah umur.
- Provider payment gateway dan kebijakan refund/langganan.
- Validasi bobot rubrik dengan pelatih/juri manusia.
- Harga final paket setelah 30–50 sesi nyata diukur.

---

**Status dokumen:** Draft PRD v1.0 — 14 Oktober 2026  
**Prinsip produk:** Practice → Evidence-Based Feedback → Targeted Improvement → Measurable Progress.
