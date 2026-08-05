<?php

namespace App\Models;

use CodeIgniter\Model;

class ResetPasswordRequestModel extends Model
{
    protected $table            = 'reset_password_request';
    protected $primaryKey       = 'id_request';
    protected $allowedFields    = [
        'id_user', 'catatan', 'status', 'diproses_oleh', 'created_at', 'processed_at',
    ];
    protected $returnType       = 'array';
    protected $useTimestamps    = false;

    public function adaPending(int $idUser): bool
    {
        return $this->where('id_user', $idUser)->where('status', 'Pending')->countAllResults() > 0;
    }

    public function countPending(?string $role = null): int
    {
        $builder = $this->where('status', 'Pending');

        if ($role !== null) {
            $builder->join('users', 'users.id_user = reset_password_request.id_user')
                ->where('users.role', $role);
        }

        return $builder->countAllResults();
    }

    public function getPendingWithUser(?string $role = null)
    {
        $builder = $this->select('reset_password_request.*, users.username, users.role')
            ->join('users', 'users.id_user = reset_password_request.id_user')
            ->where('reset_password_request.status', 'Pending')
            ->orderBy('reset_password_request.created_at', 'ASC');

        if ($role !== null) {
            $builder->where('users.role', $role);
        }

        return $builder->findAll();
    }
}
