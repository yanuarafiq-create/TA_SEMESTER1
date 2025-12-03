<?php
// Mendefinisikan class untuk merepresentasikan Dinding dan perhitungannya (OOP)
class RAB_Dinding {
    // Variabel dan Tipe Data (Property)
    private float $panjang; // Panjang dinding (meter)
    private float $tinggi;  // Tinggi dinding (meter)
    private string $jenisDinding; // Jenis material dinding
    private array $koefisien; // Array koefisien SNI (Associative Array)
    private array $hargaSatuan; // Array harga satuan material/upah

    // Constructor
    public function __construct(float $panjang, float $tinggi, string $jenisDinding) {
        $this->panjang = $panjang;
        $this->tinggi = $tinggi;
        $this->jenisDinding = $jenisDinding;

        // Inisialisasi Koefisien SNI
        $this->koefisien = [
            'Bata_1PC:4PS' => [
                'bata' => 70,
                'semen' => 0.010,
                'pasir' => 0.040,
                'tukang' => 0.3, 
            ],
            'Bata_1PC:6PS' => [
                'bata' => 70,
                'semen' => 0.007,
                'pasir' => 0.050,
                'tukang' => 0.3,
            ],
            'Plesteran' => [
                'semen' => 0.062,
                'pasir' => 0.024,
                'tukang' => 0.5,
            ]
        ];

        // Harga Satuan (Variabel, Array)
        $this->hargaSatuan = [
            'bata' => 1000,      
            'semen' => 70000,  
            'pasir' => 250000,   
            'upah_tukang' => 150000, 
        ];
    }

    // Getter Method (Function method)
    public function getLuasDinding(): float {
        return $this->panjang * $this->tinggi;
    }

    // Function method utama untuk menghitung kebutuhan material
    public function hitungKebutuhanMaterial(): array {
        $luas = $this->getLuasDinding();
        $koef_dinding = $this->koefisien[$this->jenisDinding];
        $koef_plesteran = $this->koefisien['Plesteran'];

        // Perhitungan Dinding
        $kebutuhan = [
            'bata' => $luas * $koef_dinding['bata'],
            'semen_dinding' => $luas * $koef_dinding['semen'],
            'pasir_dinding' => $luas * $koef_dinding['pasir'],
            'tukang_dinding' => $luas * $koef_dinding['tukang'],
        ];

        // Perhitungan Plesteran dan Acian (dianggap 2 sisi: luas * 2)
        $luas_plesteran = $luas * 2;
        $kebutuhan['semen_plesteran'] = $luas_plesteran * $koef_plesteran['semen'];
        $kebutuhan['pasir_plesteran'] = $luas_plesteran * $koef_plesteran['pasir'];
        $kebutuhan['tukang_plesteran'] = $luas_plesteran * $koef_plesteran['tukang'];
        
        // Total kebutuhan
        $total_kebutuhan = [
            'luas' => $luas,
            'bata' => $kebutuhan['bata'],
            // Total Semen (dinding + plesteran)
            'semen' => $kebutuhan['semen_dinding'] + $kebutuhan['semen_plesteran'],
            // Total Pasir (dinding + plesteran)
            'pasir' => $kebutuhan['pasir_dinding'] + $kebutuhan['pasir_plesteran'],
            // Total Tukang (dinding + plesteran)
            'tukang' => $kebutuhan['tukang_dinding'] + $kebutuhan['tukang_plesteran'],
        ];

        return $total_kebutuhan;
    }

    // Function method untuk menghitung RAB
    public function hitungRAB(array $kebutuhan): array {
        // Perhitungan Biaya Material
        $biaya_bata = $kebutuhan['bata'] * $this->hargaSatuan['bata'];
        // Pembulatan ke atas untuk material (Contoh: Semen) (Pengkondisian/Function)
        $jumlah_semen_dibulatkan = ceil($kebutuhan['semen']); 
        $biaya_semen = $jumlah_semen_dibulatkan * $this->hargaSatuan['semen']; 
        $biaya_pasir = $kebutuhan['pasir'] * $this->hargaSatuan['pasir']; 
        
        // Perhitungan Biaya Upah (Tukang)
        $biaya_tukang = $kebutuhan['tukang'] * $this->hargaSatuan['upah_tukang'];
        
        // Hitung Total RAB
        $total_rab = $biaya_bata + $biaya_semen + $biaya_pasir + $biaya_tukang;

        // Array hasil RAB
        $hasil_rab = [
            'biaya_bata' => $biaya_bata,
            'biaya_semen' => $biaya_semen,
            'biaya_pasir' => $biaya_pasir,
            'biaya_tukang' => $biaya_tukang,
            'total_rab' => $total_rab,
        ];

        return $hasil_rab;
    }
}

$hasilPerhitungan = [];
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $panjang = floatval($_POST['panjang'] ?? 0);
    $tinggi = floatval($_POST['tinggi'] ?? 0);
    $jenis = $_POST['jenis_dinding'] ?? '';

    // Pengkondisian (Validasi Input)
    if ($panjang <= 0 || $tinggi <= 0 || empty($jenis)) {
        $error = "Mohon isi Panjang dan Tinggi dinding dengan angka yang valid dan pilih Jenis Dinding.";
    } else {
        try {
            // Instansiasi Objek (OOP)
            $rabDinding = new RAB_Dinding($panjang, $tinggi, $jenis);

            // Hitung Kebutuhan Material
            $kebutuhan = $rabDinding->hitungKebutuhanMaterial();

            // Hitung RAB
            $rab = $rabDinding->hitungRAB($kebutuhan);
            
            // Gabungkan semua hasil untuk dikirim ke tampilan
            $hasilPerhitungan = [
                'input' => [
                    'panjang' => $panjang,
                    'tinggi' => $tinggi,
                    'jenis' => $jenis,
                    'luas' => $kebutuhan['luas']
                ],
                'kebutuhan' => $kebutuhan,
                'rab' => $rab
            ];

        } catch (Throwable $e) {
            $error = "Terjadi Kesalahan: " . $e->getMessage();
        }
    }
}
?>