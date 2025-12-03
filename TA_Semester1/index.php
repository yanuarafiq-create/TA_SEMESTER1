<?php
// Include file logika
include 'RAB_Dinding.php'; 
// Asumsi variabel $hasilPerhitungan, $error, $panjang, $tinggi, $jenis, dan $jenisPilihan 
// sudah terdefinisi dari proses include dan eksekusi logika di RAB_Dinding.php
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalkulator Material dan RAB Konstruksi Dinding</title>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');
        
        /* Warna Background Lembut (Soft Blue/Greenish Hue) */
        body { 
            font-family: 'Roboto', sans-serif; 
            background:linear-gradient(150deg, #64a5e6ff 0%, #c5d6e9ff 100%);
            color: #37474f; /* Dark text for readability */
            margin: 0; 
            padding: 50px 20px; 
            display: flex;
            justify-content: center;
        }
        .container { 
            max-width: 700px; 
            width: 100%;
            background: #ffffff; 
            padding: 30px; 
            border-radius: 12px; 
            box-shadow: 0 8px 15px rgba(0,0,0,0.1); 
        }
        h1 { 
            color: #00bcd4; /* Cyan Accent */
            text-align: center; 
            margin-bottom: 25px; 
            font-weight: 700;
        }
        h2 {
            text-align: center
        }
        /* Background Formulir yang Sangat Lembut */
        .form-section {
            background-color: #f5feff; /* Very slight off-white */
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #b2ebf2; /* Light border matching theme */
            margin-bottom: 25px;
        }
        .form-group { 
            margin-bottom: 15px; 
        }
        label { 
            display: block; 
            margin-bottom: 5px; 
            font-weight: 500; 
            color: #455a64;
        }
        input[type="number"], select { 
            width: 100%; 
            padding: 10px; 
            border: 1px solid #cfd8dc; 
            border-radius: 6px; 
            box-sizing: border-box; 
            font-size: 16px; 
            transition: border-color 0.3s; 
        }
        input[type="number"]:focus, select:focus { 
            border-color: #00bcd4; 
            box-shadow: 0 0 0 3px rgba(0, 188, 212, 0.1);
            outline: none; 
        }
        button { 
            background-color: #0097a7; /* Darker Cyan */
            color: white; 
            padding: 12px 20px; 
            border: none; 
            border-radius: 6px; 
            cursor: pointer; 
            width: 100%; 
            font-size: 17px; 
            font-weight: 500;
            transition: background-color 0.3s; 
        }
        button:hover { 
            background-color: #00838f;
        }
        .error { 
            color: #d32f2f; /* Red */
            background-color: #ffcdd2; /* Light Red Pastel */
            border: 1px solid #ef9a9a; 
            padding: 10px; 
            border-radius: 6px; 
            margin-bottom: 20px; 
            text-align: center; 
        }
        /* Background Hasil yang Sedikit Lebih Gelap */
        .result-box { 
            margin-top: 25px; 
            padding: 25px; 
            background-color: #b2ebf2; /* Medium Cyan Pastel */
            border-radius: 8px;
        }
        .result-box h2 { 
            color: #00796b; /* Dark Teal */
            border-bottom: 2px solid #0097a7; 
            padding-bottom: 8px; 
            margin-bottom: 20px; 
            font-weight: 600;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px; 
        }
        th, td { 
            padding: 10px; 
            text-align: left; 
            border-bottom: 1px solid #80deea; /* Light blue separator */
        }
        th { 
            background-color: #00bcd4; /* Cyan Header */
            color: white; 
            font-weight: 500;
        }
        tr:nth-child(even) {
            background-color: #e0f7fa; /* Very Light Cyan for even rows */
        }
        .total-row td { 
            font-weight: 700; 
            background-color: #0097a7; /* Darker Cyan Total */
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1> Kalkulator Material Dan Rab pada Konstruksi Dinding</h1>
        
        <?php if (!empty($error)): ?>
            <div class="error">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <div class="form-section">
            <form method="post" action="index.php">
                <div class="form-group">
                    <label for="panjang">Panjang Dinding (m)</label>
                    <input type="number" id="panjang" name="panjang" step="0.01" required value="<?= $panjang ?? '' ?>">
                </div>
                <div class="form-group">
                    <label for="tinggi">Tinggi Dinding (m)</label>
                    <input type="number" id="tinggi" name="tinggi" step="0.01" required value="<?= $tinggi ?? '' ?>">
                </div>
                <div class="form-group">
                    <label for="jenis_dinding">Jenis Dinding</label>
                    <?php 
                    $jenisPilihan = ['Bata_1PC:4PS' => 'Bata Merah 1PC:4PS', 'Bata_1PC:6PS' => 'Bata Merah 1PC:6PS']; 
                    ?>
                    <select id="jenis_dinding" name="jenis_dinding" required>
                        <option value="">-- Pilih Jenis --</option>
                        <?php foreach ($jenisPilihan as $value => $label): ?>
                            <option value="<?= $value ?>" <?= (($jenis ?? '') == $value) ? 'selected' : '' ?>>
                                <?= $label ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit">HITUNG RAB</button>
            </form>
        </div>

        <?php if (!empty($hasilPerhitungan)): ?>
            <div class="result-box">
                <h2> Hasil Perhitungan</h2>

                <p><strong>Luas Area:</strong> <?= number_format($hasilPerhitungan['input']['luas'], 2) ?> m²</p>
                <p><strong>Jenis Dinding:</strong> <?= $jenisPilihan[$hasilPerhitungan['input']['jenis']] ?></p>
                
                <hr style="border: 0; border-top: 1px solid #80deea; margin: 15px 0;">

                <h3>Kebutuhan Material</h3>
                <table>
                    <thead>
                        <tr><th>Material</th><th>Kebutuhan</th><th>Satuan</th></tr>
                    </thead>
                    <tbody>
                        <tr><td>Bata Merah</td><td><?= number_format($hasilPerhitungan['kebutuhan']['bata'], 0, ',', '.') ?></td><td>Buah</td></tr>
                        <tr><td>Semen (Total)</td><td><?= number_format(ceil($hasilPerhitungan['kebutuhan']['semen']), 0, ',', '.') ?></td><td>Zak</td></tr>
                        <tr><td>Pasir (Total)</td><td><?= number_format($hasilPerhitungan['kebutuhan']['pasir'], 3, ',', '.') ?></td><td>m³</td></tr>
                        <tr><td>Upah Tukang</td><td><?= number_format($hasilPerhitungan['kebutuhan']['tukang'], 2, ',', '.') ?></td><td>OH</td></tr>
                    </tbody>
                </table>
                
                <hr style="border: 0; border-top: 1px solid #80deea; margin: 15px 0;">

                <h3>Rincian Anggaran Biaya (RAB)</h3>
                <table>
                    <thead>
                        <tr><th>Uraian</th><th>Biaya</th></tr>
                    </thead>
                    <tbody>
                        <tr><td>Biaya Material</td><td>Rp. <?= number_format($hasilPerhitungan['rab']['biaya_bata'] + $hasilPerhitungan['rab']['biaya_semen'] + $hasilPerhitungan['rab']['biaya_pasir'], 0, ',', '.') ?></td></tr>
                        <tr><td>Biaya Upah</td><td>Rp. <?= number_format($hasilPerhitungan['rab']['biaya_tukang'], 0, ',', '.') ?></td></tr>
                        <tr class="total-row">
                            <td>TOTAL RAB</td>
                            <td>Rp. <?= number_format($hasilPerhitungan['rab']['total_rab'], 0, ',', '.') ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>