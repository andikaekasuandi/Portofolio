<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class KaryawanRekeningSeeder extends Seeder
{
    /**
     * Panjang nomor rekening tiap bank tidak seragam di dunia nyata,
     * dummy ini sekadar mendekati format umum masing-masing bank.
     */
    private array $bankDigitLength = [
        'BCA'     => 10,
        'Mandiri' => 13,
        'BNI'     => 10,
        'BRI'     => 15,
        'BSI'     => 10,
    ];

    public function run()
    {
        $karyawan = $this->db->table('karyawan')
            ->select('id_karyawan')
            ->groupStart()
                ->where('nomor_rekening', null)
                ->orWhere('nomor_rekening', '')
            ->groupEnd()
            ->get()
            ->getResultArray();

        $banks = array_keys($this->bankDigitLength);

        foreach ($karyawan as $row) {
            $bank   = $banks[array_rand($banks)];
            $length = $this->bankDigitLength[$bank];
            $nomor  = (string) random_int(1, 9);
            for ($i = 1; $i < $length; $i++) {
                $nomor .= (string) random_int(0, 9);
            }

            $this->db->table('karyawan')
                ->where('id_karyawan', $row['id_karyawan'])
                ->update([
                    'nama_bank'      => $bank,
                    'nomor_rekening' => $nomor,
                ]);
        }
    }
}
