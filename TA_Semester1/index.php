<?php
include 'RAB_Dinding.php'; 

$error = null;
$hasilPerhitungan = null;
$panjang = $tinggi = $jenis = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $panjang = $_POST['panjang'] ?? '';
    $tinggi = $_POST['tinggi'] ?? '';
    $jenis = $_POST['jenis_dinding'] ?? '';

    if (empty($panjang) || empty($tinggi) || empty($jenis) || !is_numeric($panjang) || !is_numeric($tinggi) || $panjang <= 0 || $tinggi <= 0) {
        $error = "Mohon masukkan nilai Panjang dan Tinggi yang valid (> 0) dan pilih Jenis Dinding.";
    } else {
        try {
            $rabDinding = new RAB_Dinding(floatval($panjang), floatval($tinggi), $jenis);
            
            $kebutuhan = $rabDinding->hitungKebutuhanMaterial();
            $rab = $rabDinding->hitungRAB($kebutuhan);
            $luas = $rabDinding->getLuasDinding();

            $hasilPerhitungan = [
                'input' => ['luas' => $luas, 'jenis' => $jenis],
                'kebutuhan' => $kebutuhan,
                'rab' => $rab,
            ];

        } catch (Exception $e) {
            $error = "Terjadi kesalahan: " . $e->getMessage();
        }
    }
}

$jenisPilihan = [
    'Bata_1PC:4PS' => 'Bata Merah 1PC:4PS (Standar Kuat)', 
    'Bata_1PC:6PS' => 'Bata Merah 1PC:6PS (Standar Umum)'
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalkulator RAB Dinding - Proyek Praktikum</title>
    <link rel="stylesheet" href="styles.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        
        <header class="navbar">
            <div class="logo">
                Kalkulator Material  <strong>RAB</strong>
            </div>
            <div class="menu">
                <a href="#"><i class="fas fa-search"></i> Search</a>
                <a href="#"><i class="fas fa-language"></i> Language</a>
                <a href="#"><i class="fas fa-question-circle"></i> Help</a>
            </div>
        </header>
        
        <section class="hero-section">
            <h1>Menghitung Biaya Kebutuhan Konstruksi Dinding Instan?</h1>
            <p>Website ini digunakan untuk menghitung kebutuhan material dan biaya RAB<p>
        </section>

        <main class="main-content">
            <div class="input-section">
                <h2>Hitung Kebutuhan Material Biaya RAB</h2>

                <?php if ($error): ?>
                    <div class="error"><?= $error ?></div>
                <?php endif; ?>

                <form method="post" action="index.php">
                    <div class="form-group">
                        <label for="panjang">Panjang Dinding (meter)</label>
                        <input type="number" id="panjang" name="panjang" step="0.01" required value="<?= htmlspecialchars($panjang) ?>">
                    </div>
                    <div class="form-group">
                        <label for="tinggi">Tinggi Dinding (meter)</label>
                        <input type="number" id="tinggi" name="tinggi" step="0.01" required value="<?= htmlspecialchars($tinggi) ?>">
                    </div>
                    <div class="form-group">
                        <label for="jenis_dinding">Pilih Jenis Dinding & Adukan</label>
                        <select id="jenis_dinding" name="jenis_dinding" required>
                            <option value="">-- Pilih Jenis --</option>
                            <?php foreach ($jenisPilihan as $value => $label): ?>
                                <option value="<?= $value ?>" <?= ($jenis === $value) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($label) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit"><i class="fas fa-calculator"></i> HITUNG RAB DINDING</button>
                </form>
            </div>

            <?php if ($hasilPerhitungan): ?>
                <section class="result-section">
                    <h2>Hasil Perhitungan Rencana Anggaran Biaya</h2>
                    
                    <p><strong>Luas Total Pekerjaan:</strong> <?= number_format($hasilPerhitungan['input']['luas'], 2, ',', '.') ?> m²</p>
                    <p><strong>Jenis Adukan:</strong> <?= htmlspecialchars($jenisPilihan[$hasilPerhitungan['input']['jenis']]) ?></p>

                    <h3>Kebutuhan Material & Upah</h3>
                    <table>
                        <thead>
                            <tr><th>Uraian</th><th>Kebutuhan</th><th>Satuan</th></tr>
                        </thead>
                        <tbody>
                            <tr><td>Bata Merah</td><td><?= number_format($hasilPerhitungan['kebutuhan']['bata'], 0, ',', '.') ?></td><td>Buah</td></tr>
                            <tr><td>Semen (Total)</td><td><?= number_format(ceil($hasilPerhitungan['kebutuhan']['semen']), 0, ',', '.') ?></td><td>Zak</td></tr>
                            <tr><td>Pasir (Total)</td><td><?= number_format($hasilPerhitungan['kebutuhan']['pasir'], 3, ',', '.') ?></td><td>m³</td></tr>
                            <tr><td>Upah Tukang</td><td><?= number_format($hasilPerhitungan['kebutuhan']['tukang'], 2, ',', '.') ?></td><td>OH (Orang Hari)</td></tr>
                        </tbody>
                    </table>

                    <h3>Total Anggaran Biaya (RAB)</h3>
                    <table>
                        <thead>
                            <tr><th>Jenis Biaya</th><th>Anggaran</th></tr>
                        </thead>
                        <tbody>
                            <tr><td>Biaya Material</td><td>Rp. <?= number_format($hasilPerhitungan['rab']['biaya_bata'] + $hasilPerhitungan['rab']['biaya_semen'] + $hasilPerhitungan['rab']['biaya_pasir'], 0, ',', '.') ?></td></tr>
                            <tr><td>Biaya Upah</td><td>Rp. <?= number_format($hasilPerhitungan['rab']['biaya_tukang'], 0, ',', '.') ?></td></tr>
                            <tr class="total-row">
                                <td>TOTAL KESELURUHAN</td>
                                <td>Rp. <?= number_format($hasilPerhitungan['rab']['total_rab'], 0, ',', '.') ?></td>
                            </tr>
                        </tbody>
                    </table>
                </section>
            <?php endif; ?>
        </main>
    </div>
</body>

</html>
