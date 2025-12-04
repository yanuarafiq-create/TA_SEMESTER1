<?php

class RAB_Dinding {
    private float $panjang;
    private float $tinggi;
    private string $jenisDinding;

    private array $koefisien = [
        'Bata_1PC:4PS' => [
            // Adukan Bata 1:4 
            'bata' => 70,       // buah
            'semen_dinding' => 0.010, // m³
            'pasir_dinding' => 0.043, // m³
            'tukang_dinding' => 0.3, // OH
            // Plesteran 1:4 (disimulasikan untuk 1 m² plesteran)
            'semen_plesteran' => 0.062, // m³
            'pasir_plesteran' => 0.02, // m³
            'tukang_plesteran' => 0.7, // OH
        ],
        'Bata_1PC:6PS' => [
            // Adukan Bata 1:6 
            'bata' => 70, 
            'semen_dinding' => 0.007,
            'pasir_dinding' => 0.045,
            'tukang_dinding' => 0.25,
            // Plesteran 1:6
            'semen_plesteran' => 0.045,
            'pasir_plesteran' => 0.02,
            'tukang_plesteran' => 0.6,
        ]
    ];

    private array $hargaSatuan = [
        'bata' => 1200,      // per buah
        'semen' => 60000,    // per zak (diasumsikan 50kg)
        'pasir' => 300000,   // per m³
        'tukang' => 150000,  // per OH (Orang Hari)
    ];

    public function __construct(float $panjang, float $tinggi, string $jenis) {
        $this->panjang = $panjang;
        $this->tinggi = $tinggi;
        $this->jenisDinding = $jenis;
        if (!array_key_exists($jenis, $this->koefisien)) {
            throw new Exception("Jenis dinding tidak valid.");
        }
    }

    public function getLuasDinding(): float {
        return $this->panjang * $this->tinggi;
    }

    public function hitungKebutuhanMaterial(): array {
        $luas = $this->getLuasDinding();
        $luas_plesteran = $luas * 2; // Diasumsikan plesteran 2 sisi
        $koef = $this->koefisien[$this->jenisDinding];

        $kebutuhan = [];

        $kebutuhan['bata'] = $luas * $koef['bata'];
        $kebutuhan['semen_dinding'] = $luas * $koef['semen_dinding'];
        $kebutuhan['pasir_dinding'] = $luas * $koef['pasir_dinding'];
        $kebutuhan['tukang_dinding'] = $luas * $koef['tukang_dinding'];

        $kebutuhan['semen_plesteran'] = $luas_plesteran * $koef['semen_plesteran'];
        $kebutuhan['pasir_plesteran'] = $luas_plesteran * $koef['pasir_plesteran'];
        $kebutuhan['tukang_plesteran'] = $luas_plesteran * $koef['tukang_plesteran'];
        
        $kebutuhan['semen'] = $kebutuhan['semen_dinding'] + $kebutuhan['semen_plesteran'];
        $kebutuhan['pasir'] = $kebutuhan['pasir_dinding'] + $kebutuhan['pasir_plesteran'];
        $kebutuhan['tukang'] = $kebutuhan['tukang_dinding'] + $kebutuhan['tukang_plesteran'];

        return $kebutuhan;

        
    public function hitungRAB(array $kebutuhan): array {
        $rab = [];

        $semen_dibulatkan = ceil($kebutuhan['semen']);
        
        $rab['biaya_bata'] = $kebutuhan['bata'] * $this->hargaSatuan['bata'];
        $rab['biaya_semen'] = $semen_dibulatkan * $this->hargaSatuan['semen'];
        $rab['biaya_pasir'] = $kebutuhan['pasir'] * $this->hargaSatuan['pasir'];
        
        $rab['biaya_tukang'] = $kebutuhan['tukang'] * $this->hargaSatuan['tukang'];

        $rab['total_rab'] = $rab['biaya_bata'] + $rab['biaya_semen'] + $rab['biaya_pasir'] + $rab['biaya_tukang'];

        return $rab;
    }
}

?>
