INSERT INTO membership_tiers (id, tier_code, tier_name, max_physical_loans, loan_duration_days, max_renewals, daily_fine_amount, max_fine_cap, can_access_restricted_thesis)
VALUES
(1, 'REGULER', 'Anggota Reguler Mahasiswa', 3, 7, 1, 1000.00, 50000.00, 0),
(2, 'DOSEN_PENELITI', 'Dosen & Tenaga Pendidik', 10, 30, 3, 500.00, 100000.00, 1),
(3, 'PASCASARJANA', 'Mahasiswa Pascasarjana', 7, 14, 2, 1000.00, 75000.00, 1),
(4, 'UMUM_LUAR', 'Anggota Umum Luar Institusi', 2, 7, 1, 2000.00, 50000.00, 0)
ON DUPLICATE KEY UPDATE tier_name = VALUES(tier_name);

INSERT INTO users (id, member_number, email, password_hash, full_name, phone_number, role, tier_id, account_status, theme_preference, membership_expires_at)
VALUES
(1, 'ADM-001', 'admin@elibrary.local', '$2y$12$5kJv65K1aUaOj8vuUH4voObf5nIv38H3rhGBeBVCfldVqpdsONx3K', 'Administrator Utama', '081234567890', 'SUPER_ADMIN', 2, 'ACTIVE', 'LIGHT', '2030-12-31'),
(2, 'LIB-001', 'pustakawan@elibrary.local', '$2y$12$5kJv65K1aUaOj8vuUH4voObf5nIv38H3rhGBeBVCfldVqpdsONx3K', 'Siti Rahmawati, S.IIP.', '081234567891', 'LIBRARIAN', 2, 'ACTIVE', 'LIGHT', '2030-12-31'),
(3, 'CAT-001', 'kataloger@elibrary.local', '$2y$12$5kJv65K1aUaOj8vuUH4voObf5nIv38H3rhGBeBVCfldVqpdsONx3K', 'Budi Santoso, A.Md.', '081234567892', 'CATALOGER', 2, 'ACTIVE', 'LIGHT', '2030-12-31'),
(4, 'M-2024-0001', 'anggota@elibrary.local', '$2y$12$5kJv65K1aUaOj8vuUH4voObf5nIv38H3rhGBeBVCfldVqpdsONx3K', 'Sarah Anggita', '081234567893', 'MEMBER', 1, 'ACTIVE', 'LIGHT', '2027-12-31')
ON DUPLICATE KEY UPDATE full_name = VALUES(full_name);

INSERT INTO bibliographic_items (id, slug, isbn_13, issn, ddc_code, call_number_base, title, subtitle, authors_text, publisher_name, publication_year, edition_statement, language_code, physical_pages, synopsis, subject_tags, media_type, total_physical_copies, available_physical_copies, rating_avg, rating_count)
VALUES
(1, 'pengantar-rekayasa-perangkat-lunak', '978-0078022128', NULL, '005.1', '005.1 PRE p', 'Pengantar Rekayasa Perangkat Lunak', 'Pendekatan Praktisi', 'Roger S. Pressman, Bruce R. Maxim', 'McGraw-Hill Education', 2024, 'Edisi ke-9', 'id-ID', 944, 'Buku acuan menyeluruh mengenai metodologi rekayasa perangkat lunak modern, clean architecture, desain sistem berorientasi objek, pengujian perangkat lunak, dan manajemen proyek berbasis agile.', 'Rekayasa Perangkat Lunak, Clean Code, Agile, Arsitektur Sistem', 'HYBRID', 4, 4, 4.85, 28),
(2, 'arsitektur-komputer-modern', '978-0128119051', NULL, '004.22', '004.22 PAT a', 'Arsitektur Komputer Modern & Desain Sistem', 'Pendekatan Kuantitatif', 'David A. Patterson, John L. Hennessy', 'Morgan Kaufmann', 2023, 'Edisi ke-6', 'id-ID', 856, 'Kajian komprehensif struktur internal komputasi, hierarki memori, prosesor paralel, serta optimasi instruksi mesin untuk kinerja tinggi.', 'Komputer, Arsitektur, Mikroprosesor, Kinerja Sistem', 'PHYSICAL_ONLY', 3, 0, 4.90, 19),
(3, 'dasar-dasar-sains-data-terapan', '978-1491910399', NULL, '005.7', '005.7 WIC d', 'Dasar-Dasar Sains Data Terapan', 'Eksplorasi, Analisis & Visualisasi', 'Hadley Wickham, Garrett Grolemund', 'O Reilly Media', 2024, 'Edisi ke-2', 'id-ID', 522, 'Panduan praktis pengolahan data tabular, transformasi informasi, pemodelan statistik deskriptif, dan visualisasi grafik ilmiah.', 'Sains Data, Analisis Data, Statistik, Visualisasi', 'HYBRID', 3, 2, 4.75, 14),
(4, 'jaringan-komputer-dan-protokol-internet', '978-0133594140', NULL, '004.6', '004.6 TAN j', 'Jaringan Komputer dan Protokol Internet', 'Prinsip Arsitektur Jaringan Global', 'Andrew S. Tanenbaum, David J. Wetherall', 'Pearson Education', 2022, 'Edisi ke-5', 'id-ID', 960, 'Studi mendalam lapisan protokol jaringan dari fisik hingga aplikasi, keamanan enkripsi data, routing paket, dan komunikasi nirkabel.', 'Jaringan Komputer, Internet, Protokol, Keamanan Jaringan', 'PHYSICAL_ONLY', 2, 2, 4.80, 22),
(5, 'struktur-data-dan-algoritma-komputasi', '978-0262033848', NULL, '005.13', '005.13 COR s', 'Struktur Data dan Algoritma Komputasi', 'Analisis Kompleksitas & Desain Efisien', 'Thomas H. Cormen, Charles E. Leiserson, Ronald L. Rivest, Clifford Stein', 'MIT Press', 2023, 'Edisi ke-3', 'id-ID', 1292, 'Rujukan fundamental analisis kompleksitas algoritma pencarian, pengurutan graf, pemrograman dinamis, dan optimasi struktur data memori.', 'Algoritma, Struktur Data, Kompleksitas Komputasi, Graf', 'HYBRID', 4, 3, 4.95, 35),
(6, 'filosofi-teras', '978-602-412-518-9', NULL, '100', '100 MAN f', 'Filosofi Teras', 'Filsafat Yunani-Kuno untuk Mental Tangguh Masa Kini', 'Henry Manampiring', 'Penerbit Buku Kompas', 2019, 'Cetakan Ke-14', 'id-ID', 320, 'Lebih dari 2.000 tahun lalu, sebuah mazhab filsafat Yunani-Romawi kuno lahir untuk membantu manusia menemukan ketenteraman batin di tengah dunia yang gaduh. Mazhab itu adalah Stoisisme (atau Filosofi Stoa). Di Indonesia, ajaran abadi ini diterjemahkan secara membumi oleh Henry Manampiring menjadi Filosofi Teras.', 'Pengembangan Diri & Filsafat, Stoisisme, Psikologi, Self Improvement', 'HYBRID', 4, 3, 4.90, 2410),
(7, 'atomic-habits', '978-602-06-3317-6', NULL, '158.1', '158.1 CLE a', 'Atomic Habits', 'Perubahan Kecil yang Memberikan Hasil Luar Biasa', 'James Clear', 'Gramedia Pustaka Utama', 2021, 'Edisi Terjemahan', 'id-ID', 352, 'Atomic Habits membedah cara kerja kebiasaan manusia dan bagaimana perubahan kecil 1 persen setiap hari dapat memberikan dampak transformatif dalam jangka panjang.', 'Produktivitas, Pengembangan Diri, Kebiasaan, Psikologi Terapan', 'HYBRID', 5, 4, 4.80, 3800),
(8, 'bumi-manusia', '978-979-97312-3-4', NULL, '899.221', '899.221 TOE b', 'Bumi Manusia', 'Tetralogi Buru Buku Pertama', 'Pramoedya Ananta Toer', 'Lentera Dipantara', 2020, 'Cetakan Ke-34', 'id-ID', 535, 'Kisah Minke, seorang pribumi cerdas di masa kolonial Hindia Belanda, yang memperjuangkan keadilan kemanusiaan dan cinta di tengah cengkeraman feodalisme dan kolonialisme.', 'Sastra Klasik, Sejarah Indonesia, Fiksi Sejarah, Roman', 'HYBRID', 3, 2, 5.00, 5400),
(9, 'laut-bercerita', '978-602-424-694-5', NULL, '899.221', '899.221 CHU l', 'Laut Bercerita', 'Sebuah Novel', 'Leila S. Chudori', 'Kepustakaan Populer Gramedia', 2021, 'Cetakan Ke-8', 'id-ID', 380, 'Menuturkan kisah aktivis mahasiswa yang hilang diculik pada masa pra-reformasi dan kepedihan keluarga korban yang terus menanti kepulangan orang tercinta.', 'Fiksi Sastra, Sejarah Kontemporer, Novel Indonesia, Kemanusiaan', 'HYBRID', 4, 3, 4.90, 1900),
(10, 'kecerdasan-emosional', '978-602-03-2415-9', NULL, '152.4', '152.4 GOL k', 'Kecerdasan Emosional', 'Mengapa EI Lebih Penting daripada IQ', 'Daniel Goleman', 'Gramedia Pustaka Utama', 2022, 'Edisi Revisi', 'id-ID', 512, 'Buku fenomenal yang merombak pemahaman kita tentang kecerdasan dan kesuksesan, menyoroti peran sentral empati, kendali diri, dan kesadaran diri.', 'Psikologi, Pengembangan Diri, Kecerdasan Emosional', 'HYBRID', 3, 3, 4.80, 1250),
(11, 'sapiens', '978-602-444-244-6', NULL, '909', '909 HAR s', 'Sapiens: Riwayat Singkat Umat Manusia', 'Dari Zaman Batu Hingga Abad Ke-21', 'Yuval Noah Harari', 'Kepustakaan Populer Gramedia', 2020, 'Edisi Bahasa Indonesia', 'id-ID', 512, 'Menjelajahi bagaimana spesies Homo sapiens yang tidak signifikan dapat menjadi penguasa planet bumi melalui revolusi kognitif, agrikultur, dan saintifik.', 'Sejarah & Peradaban, Antropologi, Evolusi, Sains Populer', 'HYBRID', 5, 4, 4.90, 1400),
(12, 'gadis-kretek', '978-979-22-8141-5', NULL, '899.221', '899.221 KUM g', 'Gadis Kretek', 'Sebuah Roman Perjalanan', 'Ratih Kumala', 'Gramedia Pustaka Utama', 2022, 'Edisi Sampul Baru', 'id-ID', 274, 'Kisah romansa dan intrik keluarga dalam industri kretek tradisional Jawa yang berjalin dengan sejarah perkembangan kretek di Indonesia.', 'Fiksi Sejarah, Budaya Nusantara, Roman Indonesia', 'HYBRID', 3, 2, 4.80, 980),
(13, 'the-psychology-of-money', '978-623-6083-20-4', NULL, '332.024', '332.024 HOU p', 'The Psychology of Money', 'Pelajaran Abadi tentang Kekayaan, Ketamakan, dan Kebahagiaan', 'Morgan Housel', 'Penerbit Baca', 2021, 'Edisi Indonesia', 'id-ID', 262, 'Mengulas bagaimana perilaku dan psikologi manusia jauh lebih menentukan kesuksesan finansial daripada rumus matematika atau kecerdasan IQ semata.', 'Keuangan & Bisnis, Investasi, Mindset Finansial', 'HYBRID', 4, 3, 4.90, 2100),
(14, 'cantik-itu-luka', '978-602-03-1258-3', NULL, '899.221', '899.221 KUR c', 'Cantik Itu Luka', 'Sebuah Roman Epik', 'Eka Kurniawan', 'Gramedia Pustaka Utama', 2021, 'Edisi Khusus', 'id-ID', 492, 'Mahakarya realisme magis Indonesia yang menceritakan kehidupan Dewi Ayu dan anak-anak perempuannya di kota Halimunda dengan latar sejarah kelam bangsa.', 'Realisme Magis, Sastra Kontemporer, Fiksi Sejarah', 'HYBRID', 3, 2, 4.70, 1100),
(15, 'sebuah-seni-untuk-bersikap-bodo-amat', '978-602-05-1229-7', NULL, '158.1', '158.1 MAN s', 'Sebuah Seni untuk Bersikap Bodo Amat', 'Pendekatan yang Waras Demi Menjalani Hidup yang Baik', 'Mark Manson', 'Grasindo', 2021, 'Cetakan Ke-20', 'id-ID', 256, 'Buku pengembangan diri dengan pendekatan lugas tanpa pemanis buatan, mengajak pembaca untuk berani memilih hal-hal penting yang pantas dipedulikan.', 'Self-Improvement, Motivasi, Filsafat Praktis', 'HYBRID', 4, 3, 4.60, 3100)
ON DUPLICATE KEY UPDATE title = VALUES(title);

INSERT INTO book_copies (id, biblio_id, inventory_barcode, rfid_epc_hex, shelf_building, shelf_floor, shelf_aisle, condition_status, availability_status, acquisition_date, unit_replacement_cost)
VALUES
(1, 1, 'BC-2024-00101', 'E28011606000020475304B01', 'Gedung Utama', 2, 'Rak 2A-01', 'EXCELLENT', 'AVAILABLE', '2024-01-15', 350000.00),
(2, 1, 'BC-2024-00102', 'E28011606000020475304B02', 'Gedung Utama', 2, 'Rak 2A-01', 'EXCELLENT', 'AVAILABLE', '2024-01-15', 350000.00),
(3, 1, 'BC-2024-00103', 'E28011606000020475304B03', 'Gedung Utama', 2, 'Rak 2A-01', 'GOOD', 'AVAILABLE', '2024-01-15', 350000.00),
(4, 1, 'BC-2024-00104', 'E28011606000020475304B04', 'Gedung Utama', 2, 'Rak 2A-01', 'GOOD', 'AVAILABLE', '2024-01-15', 350000.00),
(5, 2, 'BC-2023-00201', 'E28011606000020475304C01', 'Gedung Utama', 2, 'Rak 2B-04', 'GOOD', 'BORROWED', '2023-06-10', 420000.00),
(6, 2, 'BC-2023-00202', 'E28011606000020475304C02', 'Gedung Utama', 2, 'Rak 2B-04', 'GOOD', 'BORROWED', '2023-06-10', 420000.00),
(7, 2, 'BC-2023-00203', 'E28011606000020475304C03', 'Gedung Utama', 2, 'Rak 2B-04', 'WORN', 'BORROWED', '2023-06-10', 420000.00),
(8, 3, 'BC-2024-00301', 'E28011606000020475304D01', 'Gedung Utama', 3, 'Rak 3A-02', 'EXCELLENT', 'AVAILABLE', '2024-02-20', 290000.00),
(9, 3, 'BC-2024-00302', 'E28011606000020475304D02', 'Gedung Utama', 3, 'Rak 3A-02', 'GOOD', 'AVAILABLE', '2024-02-20', 290000.00),
(10, 3, 'BC-2024-00303', 'E28011606000020475304D03', 'Gedung Utama', 3, 'Rak 3A-02', 'GOOD', 'BORROWED', '2024-02-20', 290000.00),
(11, 4, 'BC-2022-00401', 'E28011606000020475304E01', 'Gedung Utama', 2, 'Rak 2C-01', 'GOOD', 'AVAILABLE', '2022-11-05', 380000.00),
(12, 4, 'BC-2022-00402', 'E28011606000020475304E02', 'Gedung Utama', 2, 'Rak 2C-01', 'GOOD', 'AVAILABLE', '2022-11-05', 380000.00),
(13, 5, 'BC-2023-00501', 'E28011606000020475304F01', 'Gedung Utama', 3, 'Rak 3B-05', 'EXCELLENT', 'AVAILABLE', '2023-08-14', 450000.00),
(14, 5, 'BC-2023-00502', 'E28011606000020475304F02', 'Gedung Utama', 3, 'Rak 3B-05', 'EXCELLENT', 'AVAILABLE', '2023-08-14', 450000.00),
(15, 5, 'BC-2023-00503', 'E28011606000020475304F03', 'Gedung Utama', 3, 'Rak 3B-05', 'GOOD', 'AVAILABLE', '2023-08-14', 450000.00),
(16, 5, 'BC-2023-00504', 'E28011606000020475304F04', 'Gedung Utama', 3, 'Rak 3B-05', 'GOOD', 'BORROWED', '2023-08-14', 450000.00),
(17, 6, 'BC-2024-00601', 'E28011606000020475304A01', 'Gedung Utama', 1, 'Rak 1A-01', 'EXCELLENT', 'AVAILABLE', '2024-01-10', 125000.00),
(18, 6, 'BC-2024-00602', 'E28011606000020475304A02', 'Gedung Utama', 1, 'Rak 1A-01', 'GOOD', 'AVAILABLE', '2024-01-10', 125000.00),
(19, 7, 'BC-2024-00701', 'E28011606000020475304A03', 'Gedung Utama', 1, 'Rak 1B-02', 'EXCELLENT', 'AVAILABLE', '2024-01-12', 145000.00),
(20, 8, 'BC-2024-00801', 'E28011606000020475304A04', 'Gedung Utama', 1, 'Rak 1C-03', 'EXCELLENT', 'AVAILABLE', '2024-02-01', 175000.00),
(21, 9, 'BC-2024-00901', 'E28011606000020475304A05', 'Gedung Utama', 1, 'Rak 1D-01', 'EXCELLENT', 'AVAILABLE', '2024-02-15', 135000.00),
(22, 10, 'BC-2024-01001', 'E28011606000020475304A06', 'Gedung Utama', 1, 'Rak 1E-02', 'GOOD', 'AVAILABLE', '2024-02-20', 160000.00),
(23, 11, 'BC-2024-01101', 'E28011606000020475304A07', 'Gedung Utama', 1, 'Rak 1F-01', 'EXCELLENT', 'AVAILABLE', '2024-03-01', 185000.00),
(24, 12, 'BC-2024-01201', 'E28011606000020475304A08', 'Gedung Utama', 1, 'Rak 1G-02', 'EXCELLENT', 'AVAILABLE', '2024-03-05', 95000.00),
(25, 13, 'BC-2024-01301', 'E28011606000020475304A09', 'Gedung Utama', 1, 'Rak 1H-01', 'EXCELLENT', 'AVAILABLE', '2024-03-10', 115000.00),
(26, 14, 'BC-2024-01401', 'E28011606000020475304A10', 'Gedung Utama', 1, 'Rak 1I-03', 'EXCELLENT', 'AVAILABLE', '2024-03-12', 150000.00)
ON DUPLICATE KEY UPDATE availability_status = VALUES(availability_status);

INSERT INTO digital_assets (id, biblio_id, storage_filename_hash, file_format, file_size_bytes, sha256_checksum, access_level, max_concurrent_readers, uploaded_by)
VALUES
(1, 1, '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 'PDF', 14850200, '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 'MEMBER_STREAM_ONLY', 0, 1),
(2, 3, '4b227777d4dd1fc61c6f884f48641d02b4d121d3fd328cb08b5531fcacdabf8a', 'PDF', 8420100, '4b227777d4dd1fc61c6f884f48641d02b4d121d3fd328cb08b5531fcacdabf8a', 'MEMBER_STREAM_ONLY', 0, 1),
(3, 5, 'ef2d127de37b942baad06145e54b0c619a1f22327b2ebbcfbec78f5564afe39d', 'PDF', 22100450, 'ef2d127de37b942baad06145e54b0c619a1f22327b2ebbcfbec78f5564afe39d', 'MEMBER_STREAM_ONLY', 0, 1),
(4, 6, '6a884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d9', 'PDF', 12400000, '6a884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d9', 'MEMBER_STREAM_ONLY', 0, 1),
(5, 7, '7b227777d4dd1fc61c6f884f48641d02b4d121d3fd328cb08b5531fcacdabf8b', 'PDF', 16500000, '7b227777d4dd1fc61c6f884f48641d02b4d121d3fd328cb08b5531fcacdabf8b', 'MEMBER_STREAM_ONLY', 0, 1),
(6, 8, '8c2d127de37b942baad06145e54b0c619a1f22327b2ebbcfbec78f5564afe39e', 'PDF', 18200000, '8c2d127de37b942baad06145e54b0c619a1f22327b2ebbcfbec78f5564afe39e', 'MEMBER_STREAM_ONLY', 0, 1),
(7, 9, '9d2d127de37b942baad06145e54b0c619a1f22327b2ebbcfbec78f5564afe39f', 'PDF', 14100000, '9d2d127de37b942baad06145e54b0c619a1f22327b2ebbcfbec78f5564afe39f', 'MEMBER_STREAM_ONLY', 0, 1),
(8, 10, 'ae2d127de37b942baad06145e54b0c619a1f22327b2ebbcfbec78f5564afe390', 'PDF', 13200000, 'ae2d127de37b942baad06145e54b0c619a1f22327b2ebbcfbec78f5564afe390', 'MEMBER_STREAM_ONLY', 0, 1),
(9, 11, 'be2d127de37b942baad06145e54b0c619a1f22327b2ebbcfbec78f5564afe391', 'PDF', 19400000, 'be2d127de37b942baad06145e54b0c619a1f22327b2ebbcfbec78f5564afe391', 'MEMBER_STREAM_ONLY', 0, 1),
(10, 12, 'ce2d127de37b942baad06145e54b0c619a1f22327b2ebbcfbec78f5564afe392', 'PDF', 9800000, 'ce2d127de37b942baad06145e54b0c619a1f22327b2ebbcfbec78f5564afe392', 'MEMBER_STREAM_ONLY', 0, 1),
(11, 13, 'de2d127de37b942baad06145e54b0c619a1f22327b2ebbcfbec78f5564afe393', 'PDF', 11200000, 'de2d127de37b942baad06145e54b0c619a1f22327b2ebbcfbec78f5564afe393', 'MEMBER_STREAM_ONLY', 0, 1),
(12, 14, 'ee2d127de37b942baad06145e54b0c619a1f22327b2ebbcfbec78f5564afe394', 'PDF', 17800000, 'ee2d127de37b942baad06145e54b0c619a1f22327b2ebbcfbec78f5564afe394', 'MEMBER_STREAM_ONLY', 0, 1),
(13, 15, 'fe2d127de37b942baad06145e54b0c619a1f22327b2ebbcfbec78f5564afe395', 'PDF', 10500000, 'fe2d127de37b942baad06145e54b0c619a1f22327b2ebbcfbec78f5564afe395', 'MEMBER_STREAM_ONLY', 0, 1)
ON DUPLICATE KEY UPDATE file_size_bytes = VALUES(file_size_bytes);

INSERT INTO reading_progress (id, user_id, digital_asset_id, last_page_or_cfi, completion_percent, total_reading_seconds, bookmarks_json)
VALUES
(1, 4, 4, '178', 64.00, 5400, '["bab-3", "dikotomi-kendali"]'),
(2, 4, 7, '122', 32.00, 3600, '["bab-2", "bagian-biru"]')
ON DUPLICATE KEY UPDATE completion_percent = VALUES(completion_percent);

INSERT INTO user_favorite_books (id, user_id, biblio_id, reading_status, personal_note)
VALUES
(1, 4, 9, 'READING', 'Rekomendasi dari teman klub buku'),
(2, 4, 7, 'UNREAD', 'Perlu re-read bab habit stacking'),
(3, 4, 6, 'READING', 'Buku pegangan ketenangan hidup'),
(4, 4, 11, 'UNREAD', 'Target baca bulan depan'),
(5, 4, 12, 'COMPLETED', 'Fiksi sejarah aroma cengkeh'),
(6, 4, 13, 'COMPLETED', 'Bagus untuk belajar mindset finansial')
ON DUPLICATE KEY UPDATE reading_status = VALUES(reading_status);

INSERT INTO loan_transactions (id, transaction_code, idempotency_key, user_id, book_copy_id, issued_by_staff_id, returned_to_staff_id, borrowed_at, due_date, returned_at, loan_status, remarks)
VALUES
(1, 'TRX-2024-001', 'IDEMP-TRX-2024-001', 4, 19, 2, 2, '2024-10-03 09:00:00', '2024-10-12', '2024-10-12 14:00:00', 'RETURNED', 'Pengembalian tepat waktu kondisi prima'),
(2, 'TRX-2024-002', 'IDEMP-TRX-2024-002', 4, 25, 2, 2, '2024-09-22 10:30:00', '2024-09-28', '2024-09-28 11:15:00', 'RETURNED', 'Pengembalian tepat waktu kondisi prima'),
(3, 'TRX-2024-003', 'IDEMP-TRX-2024-003', 4, 20, 2, 2, '2024-08-31 14:00:00', '2024-09-14', '2024-09-14 16:20:00', 'RETURNED', 'Pengembalian tepat waktu kondisi prima'),
(4, 'TRX-2024-004', 'IDEMP-TRX-2024-004', 4, 26, 2, 2, '2024-08-11 11:00:00', '2024-08-22', '2024-08-22 15:10:00', 'RETURNED', 'Pengembalian tepat waktu kondisi prima')
ON DUPLICATE KEY UPDATE loan_status = VALUES(loan_status);

INSERT INTO library_holidays (holiday_date, description)
VALUES
('2026-01-01', 'Tahun Baru Masehi'),
('2026-03-20', 'Hari Libur Nasional Hari Raya Nyepi'),
('2026-05-01', 'Hari Buruh Internasional'),
('2026-08-17', 'Hari Kemerdekaan Republik Indonesia'),
('2026-12-25', 'Hari Raya Natal')
ON DUPLICATE KEY UPDATE description = VALUES(description);

INSERT INTO room_bookings (id, booking_code, user_id, room_name, room_type, booking_date, start_time, end_time, purpose, booking_status)
VALUES
(1, 'RB-20260401-001', 4, 'Ruang Diskusi Alpha', 'GROUP_DISCUSSION', '2026-04-01', '09:00:00', '11:00:00', 'Diskusi Tugas Akhir Arsitektur Perangkat Lunak', 'CONFIRMED'),
(2, 'RB-20260401-002', 4, 'Bilik Pod Senyap B-03', 'INDIVIDUAL_POD', '2026-04-02', '13:00:00', '15:00:00', 'Kajian Mandiri Jurnal Ilmiah', 'CONFIRMED')
ON DUPLICATE KEY UPDATE purpose = VALUES(purpose);
