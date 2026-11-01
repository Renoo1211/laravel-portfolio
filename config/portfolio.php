<?php

/*
 * Konten portofolio. Teks yang berbeda per bahasa ditulis sebagai ['id' => ..., 'en' => ...].
 * Teks antarmuka (judul section, tombol, dll.) ada di lang/{id,en}/portfolio.php.
 */

return [

    'contact_email' => env('PORTFOLIO_CONTACT_EMAIL', 'renoo151122@gmail.com'),

    'locales' => ['id', 'en'],

    'socials' => [
        ['icon' => 'fab fa-linkedin', 'label' => 'LinkedIn', 'url' => 'https://www.linkedin.com/in/renosi/'],
        ['icon' => 'fab fa-instagram', 'label' => 'Instagram', 'url' => 'https://www.instagram.com/screzonss'],
        ['icon' => 'fab fa-github', 'label' => 'GitHub', 'url' => 'https://github.com/Renoo1211'],
    ],

    'stats' => [
        ['target' => 19, 'suffix' => 'K+', 'label' => 'stat_stores'],
        ['target' => 4, 'suffix' => '+', 'label' => 'stat_years'],
        ['target' => 5, 'suffix' => '', 'label' => 'stat_projects'],
    ],

    // img: nama file di public/img, atau URL penuh. fallback: emoji bila gambar gagal dimuat.
    'tools' => [
        'row1' => [
            ['name' => 'Microsoft Excel', 'img' => 'excel.png', 'fallback' => '📊'],
            ['name' => 'Microsoft Word', 'img' => 'office.png', 'fallback' => '📝'],
            ['name' => 'PowerPoint', 'img' => 'point.png', 'fallback' => '📊'],
            ['name' => 'Google Sheets', 'img' => 'https://upload.wikimedia.org/wikipedia/commons/a/ae/Google_Sheets_2020_Logo.svg', 'fallback' => '🟩'],
            ['name' => 'KNIME', 'img' => 'knime.jpeg', 'fallback' => '⚙️'],
        ],
        'row2' => [
            ['name' => 'SQL', 'img' => 'sql.jpg', 'fallback' => '🗄️'],
            ['name' => 'Python', 'img' => 'https://upload.wikimedia.org/wikipedia/commons/c/c3/Python-logo-notext.svg', 'fallback' => '🐍'],
            ['name' => 'Power BI', 'img' => 'https://upload.wikimedia.org/wikipedia/commons/c/cf/New_Power_BI_Logo.svg', 'fallback' => '📊'],
            ['name' => 'Tableau', 'img' => 'tableau.jpg', 'fallback' => '📈'],
        ],
    ],

    'experience' => [
        [
            'role' => ['id' => 'Staff Administrasi - Operasional Pembayaran Digital', 'en' => 'Staff Administrator - Digital Payment Operations'],
            'company' => 'PT. Indomarco Prismatama ( Indomaret Group )',
            'period' => ['id' => '2022 — Sekarang', 'en' => '2022 — Present'],
            'desc' => [
                'id' => 'Menangani ratusan komplain operasional harian yang masuk dari toko melalui sistem complaint online, memastikan penyelesaian kasus cepat dan pencatatan yang akurat. Mengolah data transaksi mentah menjadi laporan terstruktur menggunakan VLOOKUP, SUMIF, dan Pivot Table untuk mendukung monitoring operasional harian. Mendiagnosis dan menyelesaikan kendala mesin EDC agar perangkat pembayaran tetap dapat digunakan di seluruh toko Indomaret.',
                'en' => 'Handled hundreds of daily operational complaints submitted by stores through an online complaint system, ensuring fast resolution and accurate case tracking. Transformed raw transaction data into structured reports using VLOOKUP, SUMIF, and Pivot Tables to support daily operational monitoring. Diagnosed and resolved EDC machine issues to keep payment devices functional across Indomaret stores.',
            ],
            'tags' => ['Excel', 'VLOOKUP', 'Pivot Table', 'Data Reporting', 'Troubleshooting', 'Case Management'],
        ],
        [
            'role' => ['id' => 'Mahasiswa Sistem Informasi', 'en' => 'Information Systems Student'],
            'company' => 'Universitas Pamulang Serang',
            'period' => ['id' => '2024 — Sekarang', 'en' => '2024 — Present'],
            'desc' => [
                'id' => 'Mempelajari fondasi sistem informasi, basis data, dan analitik data secara akademis. Aktif mengerjakan proyek data pipeline, dashboard BI, dan eksplorasi machine learning sebagai bagian dari pengembangan diri.',
                'en' => 'Studying information systems foundations, databases, and data analytics academically. Actively working on data pipeline projects, BI dashboards, and machine learning exploration as part of self-development.',
            ],
            'tags' => ['Python', 'SQL', 'KNIME', 'Power BI'],
        ],
        [
            'role' => ['id' => 'Data Analyst (Belajar Mandiri)', 'en' => 'Data Analyst (Self-Learning)'],
            'company' => 'Independent',
            'period' => ['id' => '2025 — Sekarang', 'en' => '2025 — Present'],
            'desc' => [
                'id' => 'Secara mandiri mendalami ekosistem data analytics melalui kursus, proyek pribadi, dan sertifikasi. Berfokus pada ETL pipeline, visualisasi data, dan Business Intelligence untuk mendukung pengambilan keputusan berbasis data.',
                'en' => 'Independently exploring the data analytics ecosystem through courses, personal projects, and certifications. Focused on ETL pipelines, data visualization, and Business Intelligence to support data-driven decision making.',
            ],
            'tags' => ['Tableau', 'Power BI', 'Pandas', 'ETL'],
        ],
    ],

    // tags: [label, kelas warna]. done: true = Selesai, false = Dalam Proses.
    'projects' => [
        [
            'icon' => 'fa-solid fa-table-list',
            'tags' => [['Excel', 'green'], ['Automation', 'blue']],
            'name' => 'Automated Admin Report',
            'cats' => ['Excel'],
            'desc' => [
                'id' => 'Dashboard otomatis untuk laporan administrasi harian menggunakan Excel dengan Pivot Table dan macro — memangkas waktu pelaporan dari 2 jam menjadi 15 menit.',
                'en' => 'Automated dashboard for daily admin reports using Excel with Pivot Tables and macros — cutting reporting time from 2 hours to 15 minutes.',
            ],
            'done' => true,
            'stack' => 'Excel · VBA',
        ],
        [
            'icon' => 'fa-solid fa-chart-line',
            'tags' => [['Google Sheets', 'teal'], ['BI', 'amber']],
            'name' => 'Sales Tracker Dashboard',
            'cats' => ['BI'],
            'desc' => [
                'id' => 'Dashboard penjualan real-time di Google Sheets dengan visualisasi tren, filter dinamis, dan ringkasan KPI — terhubung ke Google Forms sebagai input data otomatis.',
                'en' => 'Real-time sales dashboard in Google Sheets with trend visualization, dynamic filters, and KPI summaries — connected to Google Forms as automatic data input.',
            ],
            'done' => true,
            'stack' => 'Sheets · Apps Script',
        ],
        [
            'icon' => 'fa-solid fa-gears',
            'tags' => [['KNIME', 'purple'], ['ETL', 'blue']],
            'name' => 'Data Cleaning Pipeline',
            'cats' => ['ETL'],
            'desc' => [
                'id' => 'Pipeline ETL dengan KNIME untuk membersihkan dan mentransformasi dataset mentah dari berbagai sumber sebelum dianalisis — mengurangi missing value hingga 90%.',
                'en' => 'ETL pipeline with KNIME to clean and transform raw datasets from multiple sources before analysis — reducing missing values by up to 90%.',
            ],
            'done' => true,
            'stack' => 'KNIME · ETL',
        ],
        [
            'icon' => 'fa-brands fa-python',
            'tags' => [['Python', 'green'], ['SQL', 'blue']],
            'name' => 'Student Data Analysis',
            'cats' => ['Python'],
            'desc' => [
                'id' => 'Analisis data akademik mahasiswa menggunakan Python (Pandas, Matplotlib) dan SQL — mengidentifikasi pola performa dan faktor yang mempengaruhi IPK.',
                'en' => 'Academic data analysis using Python (Pandas, Matplotlib) and SQL — identifying performance patterns and factors affecting GPA.',
            ],
            'done' => false,
            'stack' => 'Python · SQL',
        ],
        [
            'icon' => 'fa-solid fa-chart-pie',
            'tags' => [['BI', 'amber'], ['Dashboard', 'teal']],
            'cats' => ['BI', 'Python'],
            'name' => ['id' => 'BI Prototype — Organisasi', 'en' => 'BI Prototype — Organization'],
            'desc' => [
                'id' => 'Prototipe Business Intelligence untuk memetakan performa divisi organisasi kampus — trend analysis, perbandingan periode, dan insight actionable berbasis data.',
                'en' => 'Business Intelligence prototype to map campus organization division performance — trend analysis, period comparisons, and actionable data-driven insights.',
            ],
            'done' => false,
            'stack' => 'Sheets · Python',
        ],
    ],

    'certificates' => [
        [
            'img' => 'reno1.jpg',
            'name' => ['id' => 'Webinar: Introduction & Implementasi IPv6 Addressing', 'en' => 'Webinar: Introduction & Implementation of IPv6 Addressing'],
            'issuer' => 'ID-Networkers',
            'year' => '2025',
        ],
        [
            'img' => 'reno2.jpg',
            'name' => ['id' => 'Webinar: How to Determine Email Phishing', 'en' => 'Webinar: How to Determine Email Phishing'],
            'issuer' => 'ID-Networkers',
            'year' => '2025',
        ],
        [
            'img' => 'reno3.png',
            'name' => 'Intro to Data Analytics',
            'issuer' => 'RevoU Mini Course',
            'year' => '2026',
        ],
        [
            'img' => 'reno4.png',
            'name' => 'Bootcamp Data Analyst with Excel (Batch 25)',
            'issuer' => 'DQLab',
            'year' => '2026',
        ],
        [
            'img' => 'reno5.png',
            'name' => ['id' => 'SQL 101: Belajar SQL dari Nol', 'en' => 'SQL 101: Learn SQL from Scratch'],
            'issuer' => 'Ngulik Data',
            'year' => '2026',
        ],
    ],
];
