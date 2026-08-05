<?php

namespace App\Models;

use CodeIgniter\Model;

class LogAktivitasModel extends Model
{
    protected $table            = 'log_aktivitas';
    protected $primaryKey       = 'id_log';
    protected $allowedFields    = [
        'id_user', 'username', 'role', 'aksi', 'modul', 'keterangan', 'created_at',
    ];
    protected $returnType       = 'array';
    protected $useTimestamps    = false;

    /**
     * @param array{id_user?: int, username?: string, role?: string}|null $pelaku Dipakai untuk mencatat
     *        aksi yang dilakukan sebelum login (mis. request lupa password), karena session belum ada.
     */
    public function catat(string $aksi, string $modul, string $keterangan = '', ?array $pelaku = null): void
    {
        $this->insert([
            'id_user'    => $pelaku['id_user'] ?? session()->get('id_user'),
            'username'   => $pelaku['username'] ?? session()->get('username'),
            'role'       => $pelaku['role'] ?? session()->get('role'),
            'aksi'       => $aksi,
            'modul'      => $modul,
            'keterangan' => $keterangan,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function getTerbaru()
    {
        return $this->orderBy('created_at', 'DESC')->findAll();
    }
}
