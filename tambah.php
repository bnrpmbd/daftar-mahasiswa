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
        
        // Cek apakah data dengan NIM dan mata kuliah yang sama sudah ada
        $data_found = false;
        $update_index = -1;
        
        foreach ($mahasiswa as $index => $mhs) {
            if ($mhs['nim'] == $nim && $mhs['mk'] == $mk) {
                $data_found = true;
                $update_index = $index;
                break;
            }
        }
        
        if ($data_found) {
            // Update data yang sudah ada
            $mahasiswa[$update_index] = $data_input;
            $action_message = "Data mahasiswa dengan NIM $nim untuk mata kuliah '$mk' berhasil diperbarui!";
        } else {
            // Tambahkan data baru ke akhir array
            array_push($mahasiswa, $data_input);
            $action_message = "Data mahasiswa baru berhasil ditambahkan!";
        }
        
        // Simpan ke file
        if (simpanData($dataFile, $mahasiswa)) {
            $success = $action_message;
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
                <input type="text" id="nama" name="nama" placeholder="Nama Lengkap" required>
            </div>
            
            <div class="form-group">
                <label for="nim">NIM:</label>
                <input type="text" id="nim" name="nim" placeholder="Maksimal 16 Digit" required>
            </div>
            
            <div class="form-group">
                <label for="mk">Mata Kuliah:</label>
                <select id="mk" name="mk" required onchange="toggleMataKuliahLain()">
                    <option value="">Pilih Mata Kuliah</option>
                    <?php
                    // Ambil mata kuliah yang sudah ada dari data
                    $existing_data = bacaData($dataFile);
                    $mata_kuliah_existing = [];
                    
                    // Mata kuliah default
                    $mata_kuliah_default = [
                        "Pengembangan Web",
                        "Komputasi Cerdas", 
                        "Basis Data",
                        "Rekayasa Perangkat Lunak"
                    ];
                    
                    // Gabungkan dengan mata kuliah dari data yang sudah ada
                    if (!empty($existing_data)) {
                        $mata_kuliah_from_data = array_unique(array_column($existing_data, 'mk'));
                        $mata_kuliah_existing = array_unique(array_merge($mata_kuliah_default, $mata_kuliah_from_data));
                    } else {
                        $mata_kuliah_existing = $mata_kuliah_default;
                    }
                    
                    sort($mata_kuliah_existing);
                    
                    foreach ($mata_kuliah_existing as $mk) {
                        echo "<option value=\"" . htmlspecialchars($mk) . "\">" . htmlspecialchars($mk) . "</option>";
                    }
                    ?>
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
            <h2>
                <?php if (isset($data_found) && $data_found): ?>
                    🔄 Data yang Diperbarui:
                <?php else: ?>
                    ✅ Data yang Baru Ditambahkan:
                <?php endif; ?>
            </h2>
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
                    <tr class="<?php echo isset($data_found) && $data_found ? 'data-updated' : 'data-new'; ?>">
                        <td><?php echo htmlspecialchars($data_input['nama']); ?></td>
                        <td><?php echo htmlspecialchars($data_input['nim']); ?></td>
                        <td><?php echo htmlspecialchars($data_input['mk']); ?></td>
                        <td><?php echo $data_input['nilai']; ?></td>
                        <td><strong><?php echo konversiNilai($data_input['nilai']); ?></strong></td>
                    </tr>
                </tbody>
            </table>
            
            <?php if (isset($data_found) && $data_found): ?>
                <div class="update-info">
                    <p><strong>ℹ️ Info:</strong> Data dengan NIM yang sama untuk mata kuliah ini sudah ada sebelumnya dan telah diperbarui dengan data baru.</p>
                </div>
            <?php endif; ?>
            
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
        
        // Fungsi untuk mengecek duplikasi data
        function checkDuplicateData() {
            const nim = document.getElementById('nim').value.trim();
            const mk = document.getElementById('mk').value;
            const mkBaru = document.getElementById('mk_baru').value.trim();
            
            const finalMK = mk === 'lainnya' ? mkBaru : mk;
            
            if (nim && finalMK) {
                // Tampilkan peringatan jika kemungkinan data akan diupdate
                const warningDiv = document.getElementById('duplicate-warning');
                if (warningDiv) {
                    warningDiv.remove();
                }
                
                // Buat peringatan baru
                const warning = document.createElement('div');
                warning.id = 'duplicate-warning';
                warning.className = 'duplicate-warning';
                warning.innerHTML = `
                    <p><strong>⚠️ Perhatian:</strong> Jika data dengan NIM <strong>${nim}</strong> untuk mata kuliah <strong>${finalMK}</strong> sudah ada, maka data lama akan diganti dengan data baru.</p>
                `;
                
                // Sisipkan sebelum tombol submit
                const submitButton = document.querySelector('button[type="submit"]');
                submitButton.parentNode.insertBefore(warning, submitButton);
            }
        }
        
        // Event listeners untuk mengecek duplikasi
        document.getElementById('nim').addEventListener('blur', checkDuplicateData);
        document.getElementById('mk').addEventListener('change', checkDuplicateData);
        document.getElementById('mk_baru').addEventListener('blur', checkDuplicateData);
    </script>
</body>
</html>
