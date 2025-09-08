<?php
// Fungsi untuk konversi nilai angka ke huruf
function konversiNilai($angka) {
    if ($angka >= 85 && $angka <= 100) {
        return "A";
    } elseif ($angka >= 70 && $angka < 85) {
        return "B";
    } elseif ($angka >= 60 && $angka < 70) {
        return "C";
    } elseif ($angka >= 50 && $angka < 60) {
        return "D";
    } else {
        return "E";
    }
}

// Cek apakah data dikirim melalui POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = htmlspecialchars($_POST['nama']);
    $nim = htmlspecialchars($_POST['nim']);
    $mk = htmlspecialchars($_POST['mk']);
    $nilai = (int)$_POST['nilai'];
    $nilaiHuruf = konversiNilai($nilai);
} else {
    // Redirect ke halaman tambah jika tidak ada data POST
    header('Location: tambah.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Tersimpan</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav>
        <ul>
            <li><a href="index.php">Data Mahasiswa</a></li>
            <li><a href="tambah.php">Tambah Data</a></li>
        </ul>
    </nav>

    <div class="container">
        <h1>Data Berhasil Disimpan</h1>
        
        <div class="success-message">
            <p>Data mahasiswa telah berhasil disimpan!</p>
        </div>
        
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
                    <td><?php echo $nama; ?></td>
                    <td><?php echo $nim; ?></td>
                    <td><?php echo $mk; ?></td>
                    <td><?php echo $nilai; ?></td>
                    <td><?php echo $nilaiHuruf; ?></td>
                </tr>
            </tbody>
        </table>
        
        <div class="actions">
            <a href="tambah.php" class="btn">Tambah Data Lagi</a>
            <a href="index.php" class="btn">Lihat Semua Data</a>
        </div>
    </div>
</body>
</html>
