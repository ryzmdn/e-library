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
(4, 'M-2024-0001', 'anggota@elibrary.local', '$2y$12$5kJv65K1aUaOj8vuUH4voObf5nIv38H3rhGBeBVCfldVqpdsONx3K', 'Ahmad Fauzi', '081234567893', 'MEMBER', 1, 'ACTIVE', 'LIGHT', '2027-12-31')
ON DUPLICATE KEY UPDATE full_name = VALUES(full_name);

INSERT INTO bibliographic_items (id, slug, isbn_13, issn, ddc_code, call_number_base, title, subtitle, authors_text, publisher_name, publication_year, edition_statement, language_code, physical_pages, synopsis, subject_tags, media_type, total_physical_copies, available_physical_copies, rating_avg, rating_count)
VALUES
(1, 'pengantar-rekayasa-perangkat-lunak', '978-0078022128', NULL, '005.1', '005.1 PRE p', 'Pengantar Rekayasa Perangkat Lunak', 'Pendekatan Praktisi', 'Roger S. Pressman, Bruce R. Maxim', 'McGraw-Hill Education', 2024, 'Edisi ke-9', 'id-ID', 944, 'Buku acuan menyeluruh mengenai metodologi rekayasa perangkat lunak modern, clean architecture, desain sistem berorientasi objek, pengujian perangkat lunak, dan manajemen proyek berbasis agile.', 'Rekayasa Perangkat Lunak, Clean Code, Agile, Arsitektur Sistem', 'HYBRID', 4, 4, 4.85, 28),
(2, 'arsitektur-komputer-modern', '978-0128119051', NULL, '004.22', '004.22 PAT a', 'Arsitektur Komputer Modern & Desain Sistem', 'Pendekatan Kuantitatif', 'David A. Patterson, John L. Hennessy', 'Morgan Kaufmann', 2023, 'Edisi ke-6', 'id-ID', 856, 'Kajian komprehensif struktur internal komputasi, hierarki memori, prosesor paralel, serta optimasi instruksi mesin untuk kinerja tinggi.', 'Komputer, Arsitektur, Mikroprosesor, Kinerja Sistem', 'PHYSICAL_ONLY', 3, 0, 4.90, 19),
(3, 'dasar-dasar-sains-data-terapan', '978-1491910399', NULL, '005.7', '005.7 WIC d', 'Dasar-Dasar Sains Data Terapan', 'Eksplorasi, Analisis & Visualisasi', 'Hadley Wickham, Garrett Grolemund', 'O Reilly Media', 2024, 'Edisi ke-2', 'id-ID', 522, 'Panduan praktis pengolahan data tabular, transformasi informasi, pemodelan statistik deskriptif, dan visualisasi grafik ilmiah.', 'Sains Data, Analisis Data, Statistik, Visualisasi', 'HYBRID', 3, 2, 4.75, 14),
(4, 'jaringan-komputer-dan-protokol-internet', '978-0133594140', NULL, '004.6', '004.6 TAN j', 'Jaringan Komputer dan Protokol Internet', 'Prinsip Arsitektur Jaringan Global', 'Andrew S. Tanenbaum, David J. Wetherall', 'Pearson Education', 2022, 'Edisi ke-5', 'id-ID', 960, 'Studi mendalam lapisan protokol jaringan dari fisik hingga aplikasi, keamanan enkripsi data, routing paket, dan komunikasi nirkabel.', 'Jaringan Komputer, Internet, Protokol, Keamanan Jaringan', 'PHYSICAL_ONLY', 2, 2, 4.80, 22),
(5, 'struktur-data-dan-algoritma-komputasi', '978-0262033848', NULL, '005.13', '005.13 COR s', 'Struktur Data dan Algoritma Komputasi', 'Analisis Kompleksitas & Desain Efisien', 'Thomas H. Cormen, Charles E. Leiserson, Ronald L. Rivest, Clifford Stein', 'MIT Press', 2023, 'Edisi ke-3', 'id-ID', 1292, 'Rujukan fundamental analisis kompleksitas algoritma pencarian, pengurutan graf, pemrograman dinamis, dan optimasi struktur data memori.', 'Algoritma, Struktur Data, Kompleksitas Komputasi, Graf', 'HYBRID', 4, 3, 4.95, 35)
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
(16, 5, 'BC-2023-00504', 'E28011606000020475304F04', 'Gedung Utama', 3, 'Rak 3B-05', 'GOOD', 'BORROWED', '2023-08-14', 450000.00)
ON DUPLICATE KEY UPDATE availability_status = VALUES(availability_status);

INSERT INTO digital_assets (id, biblio_id, storage_filename_hash, file_format, file_size_bytes, sha256_checksum, access_level, max_concurrent_readers, uploaded_by)
VALUES
(1, 1, '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 'PDF', 14850200, '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 'MEMBER_STREAM_ONLY', 0, 1),
(2, 3, '4b227777d4dd1fc61c6f884f48641d02b4d121d3fd328cb08b5531fcacdabf8a', 'PDF', 8420100, '4b227777d4dd1fc61c6f884f48641d02b4d121d3fd328cb08b5531fcacdabf8a', 'MEMBER_STREAM_ONLY', 0, 1),
(3, 5, 'ef2d127de37b942baad06145e54b0c619a1f22327b2ebbcfbec78f5564afe39d', 'PDF', 22100450, 'ef2d127de37b942baad06145e54b0c619a1f22327b2ebbcfbec78f5564afe39d', 'MEMBER_STREAM_ONLY', 0, 1)
ON DUPLICATE KEY UPDATE file_size_bytes = VALUES(file_size_bytes);

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
