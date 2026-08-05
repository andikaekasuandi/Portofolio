<?php

namespace App\Controllers;

use App\Models\AbsensiModel;
use App\Models\KaryawanModel;
use App\Models\LogAktivitasModel;
use App\Models\PenggajianModel;
use App\Models\UserModel;

class PenggajianController extends BaseController
{
    protected PenggajianModel $model;

    public function __construct()
    {
        $this->model = new PenggajianModel();
    }

    public function index()
    {
        $bulan = (int) ($this->request->getGet('bulan') ?? date('n'));
        $tahun = (int) ($this->request->getGet('tahun') ?? date('Y'));
        $tipe  = session()->get('role') === 'Owner' ? $this->request->getGet('tipe') : null;

        return view('penggajian/index', [
            'data'          => $this->model->getFiltered($bulan, $tahun, $tipe),
            'bulan'         => $bulan,
            'tahun'         => $tahun,
            'tipe'          => $tipe,
            'totalKaryawan' => (new KaryawanModel())->countAllResults(),
            'sudahDigaji'   => $this->model->countKaryawanDigaji($bulan, $tahun),
        ]);
    }

    public function proses()
    {
        $adminKaryawanIds = (new UserModel())->getAdminKaryawanIds();
        $karyawanModel    = new KaryawanModel();

        $adminKaryawan = [];
        if (session()->get('role') === 'Owner') {
            foreach ($adminKaryawanIds as $idKaryawan) {
                $karyawan = $karyawanModel->getWithJabatan($idKaryawan);
                if ($karyawan) {
                    $adminKaryawan[] = $karyawan;
                }
            }
        }

        // Karyawan non-Admin, buat dropdown proses gaji manual (1 karyawan)
        $bulanIni = (int) date('n');
        $tahunIni = (int) date('Y');

        $daftarKaryawan = array_values(array_filter(
            $karyawanModel->getWithJabatan(),
            static fn (array $k): bool => ! in_array((int) $k['id_karyawan'], $adminKaryawanIds, true)
        ));

        foreach ($daftarKaryawan as &$k) {
            $k['eligible']       = $this->sudahMasukSebulanPenuh($k, $bulanIni, $tahunIni);
            $k['mulai_eligible'] = $this->tanggalMulaiEligible($k)->format('d/m/Y');
        }
        unset($k);

        return view('penggajian/proses', [
            'adminKaryawan'  => $adminKaryawan,
            'daftarKaryawan' => $daftarKaryawan,
        ]);
    }

    public function simpanProses()
    {
        $bulan = (int) $this->request->getPost('bulan');
        $tahun = (int) $this->request->getPost('tahun');

        $karyawanModel = new KaryawanModel();
        $absensiModel  = new AbsensiModel();

        $diprosesOleh     = session()->get('username');
        $diprosesOlehRole = session()->get('role');
        $adminKaryawanIds = (new UserModel())->getAdminKaryawanIds();

        $berhasil              = 0;
        $dilewatiSudahDiproses = 0;
        $dilewatiAdmin         = 0;
        $dilewatiBaruMasuk     = 0;

        foreach ($karyawanModel->getWithJabatan() as $karyawan) {
            $idKaryawan = (int) $karyawan['id_karyawan'];

            if (in_array($idKaryawan, $adminKaryawanIds, true)) {
                $dilewatiAdmin++;

                continue;
            }

            if ($this->model->sudahDiproses($idKaryawan, $bulan, $tahun)) {
                $dilewatiSudahDiproses++;

                continue;
            }

            if (! $this->sudahMasukSebulanPenuh($karyawan, $bulan, $tahun)) {
                $dilewatiBaruMasuk++;

                continue;
            }

            $rekap = $absensiModel->rekapBulanan($idKaryawan, $bulan, $tahun);

            $data = array_merge($this->hitungGaji($karyawan, $rekap), [
                'kode_slip'          => $this->model->generateKodeSlip($bulan, $tahun),
                'id_karyawan'        => $idKaryawan,
                'bulan'              => $bulan,
                'tahun'              => $tahun,
                'tanggal_gaji'       => date('Y-m-d'),
                'diproses_oleh'      => $diprosesOleh,
                'diproses_oleh_role' => $diprosesOlehRole,
            ]);

            if ($this->model->save($data)) {
                $berhasil++;
            }
        }

        $pesan = "Proses gaji selesai: {$berhasil} slip berhasil diproses";
        if ($dilewatiSudahDiproses > 0) {
            $pesan .= ", {$dilewatiSudahDiproses} dilewati (sudah diproses sebelumnya)";
        }

        if ($dilewatiBaruMasuk > 0) {
            $pesan .= ", {$dilewatiBaruMasuk} karyawan baru dilewati (belum genap 1 bulan masa kerja, pakai Proses Gaji Manual kalau mau tetap dibayar)";
        }

        if ($dilewatiAdmin > 0) {
            $pesan .= ", {$dilewatiAdmin} karyawan berstatus Admin dilewati (proses lewat menu Proses Gaji Karyawan Admin)";
        }

        (new LogAktivitasModel())->catat(
            'Proses Gaji Massal',
            'Penggajian',
            "Memproses gaji massal periode {$bulan}/{$tahun}: {$berhasil} berhasil diproses, {$dilewatiSudahDiproses} dilewati (sudah diproses), {$dilewatiBaruMasuk} dilewati (karyawan baru belum genap 1 bulan), {$dilewatiAdmin} karyawan Admin dilewati."
        );

        return redirect()->to('/penggajian')->with('success', $pesan . '.');
    }

    public function simpanProsesAdmin()
    {
        $bulan = (int) $this->request->getPost('bulan');
        $tahun = (int) $this->request->getPost('tahun');

        $karyawanModel = new KaryawanModel();
        $absensiModel  = new AbsensiModel();

        $diprosesOleh     = session()->get('username');
        $diprosesOlehRole = session()->get('role');

        $berhasil              = 0;
        $dilewatiSudahDiproses = 0;
        $dilewatiBaruMasuk     = 0;

        foreach ((new UserModel())->getAdminKaryawanIds() as $idKaryawan) {
            $karyawan = $karyawanModel->getWithJabatan($idKaryawan);
            if (! $karyawan) {
                continue;
            }

            if ($this->model->sudahDiproses($idKaryawan, $bulan, $tahun)) {
                $dilewatiSudahDiproses++;

                continue;
            }

            if (! $this->sudahMasukSebulanPenuh($karyawan, $bulan, $tahun)) {
                $dilewatiBaruMasuk++;

                continue;
            }

            $rekap = $absensiModel->rekapBulanan($idKaryawan, $bulan, $tahun);

            $data = array_merge($this->hitungGaji($karyawan, $rekap), [
                'kode_slip'          => $this->model->generateKodeSlip($bulan, $tahun),
                'id_karyawan'        => $idKaryawan,
                'bulan'              => $bulan,
                'tahun'              => $tahun,
                'tanggal_gaji'       => date('Y-m-d'),
                'diproses_oleh'      => $diprosesOleh,
                'diproses_oleh_role' => $diprosesOlehRole,
            ]);

            if ($this->model->save($data)) {
                $berhasil++;
            }
        }

        $pesan = "Proses gaji karyawan Admin selesai: {$berhasil} slip berhasil diproses";
        if ($dilewatiSudahDiproses > 0) {
            $pesan .= ", {$dilewatiSudahDiproses} dilewati (sudah diproses sebelumnya)";
        }

        if ($dilewatiBaruMasuk > 0) {
            $pesan .= ", {$dilewatiBaruMasuk} dilewati (karyawan baru belum genap 1 bulan masa kerja)";
        }

        (new LogAktivitasModel())->catat(
            'Proses Gaji Admin',
            'Penggajian',
            "Memproses gaji karyawan Admin periode {$bulan}/{$tahun}: {$berhasil} berhasil diproses, {$dilewatiSudahDiproses} dilewati (sudah diproses), {$dilewatiBaruMasuk} dilewati (karyawan baru belum genap 1 bulan)."
        );

        return redirect()->to('/penggajian')->with('success', $pesan . '.');
    }

    public function simpanProsesManual()
    {
        $idKaryawan = (int) $this->request->getPost('id_karyawan');
        $bulan      = (int) $this->request->getPost('bulan');
        $tahun      = (int) $this->request->getPost('tahun');

        $karyawanModel    = new KaryawanModel();
        $absensiModel     = new AbsensiModel();
        $adminKaryawanIds = (new UserModel())->getAdminKaryawanIds();

        if (in_array($idKaryawan, $adminKaryawanIds, true) && session()->get('role') !== 'Owner') {
            return redirect()->to('/penggajian/proses')->with('error', 'Karyawan ini berstatus Admin, hanya Owner yang bisa memproses gajinya.');
        }

        $karyawan = $karyawanModel->getWithJabatan($idKaryawan);
        if (! $karyawan) {
            return redirect()->to('/penggajian/proses')->with('error', 'Karyawan tidak ditemukan.');
        }

        if ($this->model->sudahDiproses($idKaryawan, $bulan, $tahun)) {
            return redirect()->to('/penggajian/proses')->with('error', "Gaji {$karyawan['nama']} untuk periode {$bulan}/{$tahun} sudah pernah diproses sebelumnya.");
        }

        if (! $this->sudahMasukSebulanPenuh($karyawan, $bulan, $tahun)) {
            $mulaiEligible = $this->tanggalMulaiEligible($karyawan)->format('d/m/Y');

            return redirect()->to('/penggajian/proses')->with('error', "{$karyawan['nama']} baru masuk kerja dan belum genap 1 bulan masa kerja untuk periode {$bulan}/{$tahun}. Baru bisa digaji mulai {$mulaiEligible}.");
        }

        $rekap = $absensiModel->rekapBulanan($idKaryawan, $bulan, $tahun);

        $data = array_merge($this->hitungGaji($karyawan, $rekap), [
            'kode_slip'          => $this->model->generateKodeSlip($bulan, $tahun),
            'id_karyawan'        => $idKaryawan,
            'bulan'              => $bulan,
            'tahun'              => $tahun,
            'tanggal_gaji'       => date('Y-m-d'),
            'diproses_oleh'      => session()->get('username'),
            'diproses_oleh_role' => session()->get('role'),
        ]);

        $this->model->save($data);

        (new LogAktivitasModel())->catat(
            'Proses Gaji Manual',
            'Penggajian',
            "Memproses gaji manual untuk {$karyawan['nama']} ({$karyawan['kode_karyawan']}) periode {$bulan}/{$tahun}."
        );

        return redirect()->to('/penggajian')->with('success', "Gaji {$karyawan['nama']} untuk periode {$bulan}/{$tahun} berhasil diproses.");
    }

    /**
     * Karyawan baru cuma layak digaji begitu sudah genap 1 bulan masa kerja dari tanggal_masuk-nya
     * (masuk 28 Juli -> baru layak mulai 28 Agustus). Berlaku di proses massal maupun proses manual.
     */
    private function sudahMasukSebulanPenuh(array $karyawan, int $bulan, int $tahun): bool
    {
        return $this->tanggalMulaiEligible($karyawan) <= $this->akhirPeriode($bulan, $tahun);
    }

    /**
     * Tanggal masuk + 1 bulan, di-clamp ke akhir bulan target kalau kelebihan
     * (masuk 31 Januari -> layak 28/29 Februari, bukan 3 Maret gara-gara overflow "+1 month" PHP).
     */
    private function tanggalMulaiEligible(array $karyawan): \DateTimeImmutable
    {
        $masuk = new \DateTimeImmutable($karyawan['tanggal_masuk']);

        $bulanTarget = (int) $masuk->format('n') + 1;
        $tahunTarget = (int) $masuk->format('Y');
        if ($bulanTarget > 12) {
            $bulanTarget = 1;
            $tahunTarget++;
        }

        $awalBulanTarget  = new \DateTimeImmutable(sprintf('%04d-%02d-01', $tahunTarget, $bulanTarget));
        $hari             = min((int) $masuk->format('j'), (int) $awalBulanTarget->format('t'));

        return $awalBulanTarget->modify(sprintf('+%d days', $hari - 1));
    }

    private function akhirPeriode(int $bulan, int $tahun): \DateTimeImmutable
    {
        return (new \DateTimeImmutable(sprintf('%04d-%02d-01', $tahun, $bulan)))->modify('last day of this month');
    }

    private function hitungGaji(array $karyawan, array $rekap): array
    {
        $gajiPokok   = (float) $karyawan['gaji_pokok'];
        $tunjangan   = (float) $karyawan['tunjangan'];
        $tarifLembur = (float) $karyawan['tarif_lembur'];

        $uangLembur = $rekap['total_lembur'] * $tarifLembur;

        // Potongan berbasis persentase dari gaji pokok
        $persenAlpha = 0.02; // 2% per hari Alpha
        $persenIzin  = 0.01; // 1% per hari Izin

        $potongan = $rekap['Alpha'] * ($gajiPokok * $persenAlpha) + $rekap['Izin'] * ($gajiPokok * $persenIzin);

        return [
            'gaji_pokok'  => $gajiPokok,
            'tunjangan'   => $tunjangan,
            'uang_lembur' => $uangLembur,
            'potongan'    => $potongan,
            'total_gaji'  => $gajiPokok + $tunjangan + $uangLembur - $potongan,
        ];
    }

    public function cetak($id)
    {
        $slip = $this->model->getWithKaryawan($id);
        if (! $slip) {
            return redirect()->to('/penggajian')->with('error', 'Slip gaji tidak ditemukan.');
        }

        return view('penggajian/slip', ['slip' => $slip]);
    }

    public function delete($id)
    {
        $slip = $this->model->getWithKaryawan($id);
        if (! $slip) {
            return redirect()->to('/laporan/penggajian')->with('error', 'Slip gaji tidak ditemukan.');
        }

        $this->model->delete($id);

        (new LogAktivitasModel())->catat(
            'Hapus',
            'Penggajian',
            "Menghapus slip gaji {$slip['kode_slip']} atas nama {$slip['nama']} periode {$slip['bulan']}/{$slip['tahun']}."
        );

        return redirect()->to('/laporan/penggajian')->with('success', 'Data penggajian berhasil dihapus.');
    }
}