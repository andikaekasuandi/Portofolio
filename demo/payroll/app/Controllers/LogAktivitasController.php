<?php

namespace App\Controllers;

use App\Models\LogAktivitasModel;

class LogAktivitasController extends BaseController
{
    protected LogAktivitasModel $model;

    public function __construct()
    {
        $this->model = new LogAktivitasModel();
    }

    public function index()
    {
        return view('log_aktivitas/index', ['data' => $this->model->getTerbaru()]);
    }
}
