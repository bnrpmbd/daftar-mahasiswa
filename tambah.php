<?php
// File untuk menyimpan data
$dataFile = 'data/mahasiswa.json';

// Fungsi untuk membaca data dari file
function bacaData($file) {
    if (file_exists($file)) {
        $json = file_get_contents($file);
        $data = json_decode($json, true);
        return $data ? $data : [];
    }
    return [];
}

// Fungsi untuk menyimpan data ke file
function simpanData($file, $data) {
    // Buat direktori jika belum ada
    $dir = dirname($file);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    
    $json = json_encode($data, JSON_PRETTY_PRINT);
    return file_put_contents($file, $json);
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

// Variabel untuk menyimpan data yang baru diinput
$data_input = null;

// Proses form submission
if ($_POST) {
    $nama = trim($_POST['nama']);
    $nim = trim($_POST['nim']);
    $mk = trim($_POST['mk']);
    $mk_baru = trim($_POST['mk_baru'] ?? '');
    $nilai = (int)$_POST['nilai'];
    
    // Jika memilih "lainnya", gunakan input mata kuliah baru
    if ($mk === 'lainnya' && !empty($mk_baru)) {
        $mk = $mk_baru;
    }
    
    // Validasi input
    if (!empty($nama) && !empty($nim) && !empty($mk) && $nilai >= 0 && $nilai <= 100) {
        // Data baru
        $data_input = [
            "nama" => $nama,
            "nim" => $nim,
            "mk" => $mk,
            "nilai" => $nilai
        ];
        
        // Baca data yang sudah ada
        $mahasiswa = bacaData($dataFile);
        
        // Tambahkan data baru ke akhir array
        array_push($mahasiswa, $data_input);
        
        // Simpan ke file
        if (simpanData($dataFile, $mahasiswa)) {
            $success = "Data berhasil ditambahkan dan tersimpan permanen!";
        } else {
            $error = "Gagal menyimpan data!";
        }
    } else {
        $error = "Harap isi semua field dengan benar!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Mahasiswa</title>
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
            <h1>➕ Tambah Data Mahasiswa</h1>
            <p class="subtitle">Input data mahasiswa baru beserta nilai mata kuliah</p>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="nama">Nama:</label>
                <input type="text" id="nama" name="nama" placeholder="Banar Pambudi" required>
            </div>
            
            <div class="form-group">
                <label for="nim">NIM:</label>
                <input type="text" id="nim" name="nim" placeholder="21060123140160" required>
            </div>
            
            <div class="form-group">
                <label for="mk">Mata Kuliah:</label>
                <select id="mk" name="mk" required onchange="toggleMataKuliahLain()">
                    <option value="">Pilih Mata Kuliah</option>
                    <option value="Pengembangan Web">Pengembangan Web</option>
                    <option value="Komputasi Cerdas">Komputasi Cerdas</option>
                    <option value="Basis Data">Basis Data</option>
                    <option value="Rekayasa Perangkat Lunak">Rekayasa Perangkat Lunak</option>
                    <option value="lainnya">➕ Tambah Mata Kuliah Baru</option>
                </select>
                
                <div id="mk-lainnya" class="mk-input-baru" style="display: none;">
                    <label for="mk_baru">Nama Mata Kuliah Baru:</label>
                    <input type="text" id="mk_baru" name="mk_baru" placeholder="Masukkan nama mata kuliah baru">
                </div>
            </div>
            
            <div class="form-group">
                <label for="nilai">Nilai (0-100):</label>
                <input type="number" id="nilai" name="nilai" min="0" max="100" placeholder="0-100" required>
                <div id="nilai-huruf" class="nilai-preview"></div>
            </div>
            
            <div class="form-group">
                <button type="submit">Tambah Data</button>
            <a href="index.php" class="btn-cancel">Batal</a>
        </form>
        
        <?php if ($data_input): ?>
        <div class="result-section">
            <h2>Data yang Baru Ditambahkan:</h2>
            <table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>NIM</th>
                        <th>Mata Kuliah</th>
                        <th>Nilai Angka</th>
                        <th>Nilai Huruf</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo htmlspecialchars($data_input['nama']); ?></td>
                        <td><?php echo htmlspecialchars($data_input['nim']); ?></td>
                        <td><?php echo htmlspecialchars($data_input['mk']); ?></td>
                        <td><?php echo $data_input['nilai']; ?></td>
                        <td><strong><?php echo konversiNilai($data_input['nilai']); ?></strong></td>
                    </tr>
                </tbody>
            </table>
            <div class="action-buttons">
                <a href="index.php" class="btn-primary">Lihat Semua Data</a>
                <a href="tambah.php" class="btn-secondary">Tambah Data Lagi</a>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script>
        // Fungsi untuk toggle input mata kuliah baru
        function toggleMataKuliahLain() {
            const selectMK = document.getElementById('mk');
            const inputMKLain = document.getElementById('mk-lainnya');
            const inputMKBaru = document.getElementById('mk_baru');
            
            if (selectMK.value === 'lainnya') {
                inputMKLain.style.display = 'block';
                inputMKBaru.required = true;
                inputMKBaru.focus();
            } else {
                inputMKLain.style.display = 'none';
                inputMKBaru.required = false;
                inputMKBaru.value = '';
            }
        }
        
        // Preview nilai huruf secara real-time
        document.getElementById('nilai').addEventListener('input', function() {
            const nilai = parseInt(this.value);
            const previewDiv = document.getElementById('nilai-huruf');
            
            if (nilai >= 0 && nilai <= 100) {
                let huruf = '';
                if (nilai >= 85) huruf = 'A';
                else if (nilai >= 70) huruf = 'B';
                else if (nilai >= 60) huruf = 'C';
                else if (nilai >= 50) huruf = 'D';
                else huruf = 'E';
                
                previewDiv.innerHTML = `<strong>Nilai Huruf: ${huruf}</strong>`;
                previewDiv.className = `nilai-preview grade-${huruf}`;
            } else {
                previewDiv.innerHTML = '';
                previewDiv.className = 'nilai-preview';
            }
        });
        
        // Validasi form sebelum submit
        document.querySelector('form').addEventListener('submit', function(e) {
            const selectMK = document.getElementById('mk');
            const inputMKBaru = document.getElementById('mk_baru');
            
            if (selectMK.value === 'lainnya' && inputMKBaru.value.trim() === '') {
                e.preventDefault();
                alert('Harap masukkan nama mata kuliah baru!');
                inputMKBaru.focus();
                return false;
            }
            
            if (selectMK.value === '') {
                e.preventDefault();
                alert('Harap pilih mata kuliah!');
                selectMK.focus();
                return false;
            }
        });
    </script>
</body>
</html>
