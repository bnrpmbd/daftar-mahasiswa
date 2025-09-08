<?php
// File untuk menyimpan data
$dataFile = 'data/mahasiswa.json';

// Data Mahasiswa default
$mahasiswa_default = [
    ["nama" => "Andi", "nim" => "21060125150101", "mk" => "Pengembangan Web", "nilai" => 88],
    ["nama" => "Budi", "nim" => "21060125150102", "mk" => "Pengembangan Web", "nilai" => 85],
    ["nama" => "Citra", "nim" => "21060125150103", "mk" => "Komputasi Cerdas", "nilai" => 90],
    ["nama" => "Dewi", "nim" => "21060125150104", "mk" => "Basis Data", "nilai" => 72],
    ["nama" => "Eka", "nim" => "21060125150105", "mk" => "Komputasi Cerdas", "nilai" => 87],
    ["nama" => "Fajar", "nim" => "21060125150106", "mk" => "Basis Data", "nilai" => 50],
    ["nama" => "Gina", "nim" => "21060125150107", "mk" => "Pengembangan Web", "nilai" => 91],
    ["nama" => "Hadi", "nim" => "21060125150108", "mk" => "Komputasi Cerdas", "nilai" => 64],
    ["nama" => "Intan", "nim" => "21060125150109", "mk" => "Rekayasa Perangkat Lunak", "nilai" => 93],
    ["nama" => "Joko", "nim" => "21060125150110", "mk" => "Rekayasa Perangkat Lunak", "nilai" => 37],
];

// Buat direktori data jika belum ada
if (!is_dir('data')) {
    mkdir('data', 0777, true);
}

// Fungsi untuk membaca data dari file
function bacaData($file, $default) {
    if (file_exists($file)) {
        $json = file_get_contents($file);
        $data = json_decode($json, true);
        return $data ? $data : $default;
    }
    return $default;
}

// Fungsi untuk menyimpan data ke file
function simpanData($file, $data) {
    $json = json_encode($data, JSON_PRETTY_PRINT);
    return file_put_contents($file, $json);
}

// Baca data dari file atau gunakan default
$mahasiswa = bacaData($dataFile, $mahasiswa_default);

// Jika file belum ada, simpan data default
if (!file_exists($dataFile)) {
    simpanData($dataFile, $mahasiswa);
}

// Fungsi untuk konversi nilai angka ke huruf
function konversiNilai($angka) {
    if ($angka >= 85) {
        return "A";
    } elseif ($angka >= 70) {
        return "B";
    } elseif ($angka >= 60) {
        return "C";
    } elseif ($angka >= 50) {
        return "D";
    } else {
        return "E";
    }
}
?>

<!-- Fungsi Foreach Untuk Looping Data dan Menampilkan Tabel -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mahasiswa</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav>
        <ul>
            <li><a href="index.php">🏠 Beranda</a></li>
            <li><a href="tambah.php">➕ Tambah Mahasiswa</a></li>
        </ul>
    </nav>

    <div class="container">
        <div class="header-section">
            <h1>📚 Aplikasi Daftar Mahasiswa & Nilai</h1>
            <p class="subtitle">Sistem Manajemen Data Mahasiswa dengan PHP Murni</p>
        </div>
        
        <div class="info-section">
            <div class="stats">
                <div class="stat-item">
                    <span class="stat-number"><?php echo count($mahasiswa); ?></span>
                    <span class="stat-label">Total Mahasiswa</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?php 
                        $lulus = 0;
                        foreach($mahasiswa as $mhs) {
                            if($mhs['nilai'] >= 60) $lulus++;
                        }
                        echo $lulus;
                    ?></span>
                    <span class="stat-label">Lulus (≥60)</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?php echo count($mahasiswa) - $lulus; ?></span>
                    <span class="stat-label">Tidak Lulus (<60)</span>
                </div>
            </div>
        </div>
        
        <!-- Filter dan Search -->
        <div class="filter-section">
            <form method="GET" action="">
                <div class="filter-group">
                    <label for="filter_mk">Filter Mata Kuliah:</label>
                    <select id="filter_mk" name="filter_mk" onchange="this.form.submit()">
                        <option value="">Semua Mata Kuliah</option>
                        <?php
                        // Ambil semua mata kuliah unik dari data
                        $mata_kuliah_list = array_unique(array_column($mahasiswa, 'mk'));
                        sort($mata_kuliah_list);
                        
                        foreach ($mata_kuliah_list as $mk) {
                            $selected = (isset($_GET['filter_mk']) && $_GET['filter_mk'] == $mk) ? 'selected' : '';
                            echo "<option value=\"" . htmlspecialchars($mk) . "\" $selected>" . htmlspecialchars($mk) . "</option>";
                        }
                        ?>
                    </select>
                </div>
            </form>
        </div>
        
        <?php
        // Filter data berdasarkan mata kuliah
        $mahasiswa_filtered = $mahasiswa;
        if (isset($_GET['filter_mk']) && !empty($_GET['filter_mk'])) {
            $mahasiswa_filtered = array_filter($mahasiswa, function($mhs) {
                return $mhs['mk'] == $_GET['filter_mk'];
            });
        }
        ?>
        
        <!-- Tampilan Tabel -->
        <div class="table-section">
            <h2>📋 Daftar Mahasiswa & Nilai 
                <?php if (isset($_GET['filter_mk']) && !empty($_GET['filter_mk'])): ?>
                    - <?php echo htmlspecialchars($_GET['filter_mk']); ?>
                <?php endif; ?>
            </h2>
            
            <?php if (count($mahasiswa_filtered) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Mahasiswa</th>
                        <th>NIM</th>
                        <th>Mata Kuliah</th>
                        <th>Nilai Angka</th>
                        <th>Nilai Huruf</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Fungsi Foreach -->
                    <?php $no = 1; ?>
                    <?php foreach ($mahasiswa_filtered as $mhs): ?>
                    <tr class="<?php echo ($mhs['nilai'] >= 60) ? 'lulus' : 'tidak-lulus'; ?>">
                        <td><?php echo $no++; ?></td>
                        <td><?php echo htmlspecialchars($mhs['nama']); ?></td>
                        <td><?php echo htmlspecialchars($mhs['nim']); ?></td>
                        <td><?php echo htmlspecialchars($mhs['mk']); ?></td>
                        <td><?php echo $mhs['nilai']; ?></td>
                        <td class="grade-<?php echo konversiNilai($mhs['nilai']); ?>">
                            <strong><?php echo konversiNilai($mhs['nilai']); ?></strong>
                        </td>
                        <td>
                            <?php if ($mhs['nilai'] >= 60): ?>
                                <span class="status-lulus">✅ Lulus</span>
                            <?php else: ?>
                                <span class="status-tidak-lulus">❌ Tidak Lulus</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
                <div class="no-data">
                    <p>📝 Tidak ada data mahasiswa untuk mata kuliah yang dipilih.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>