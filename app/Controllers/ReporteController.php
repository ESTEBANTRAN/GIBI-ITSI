<?php

namespace App\Controllers;

class ReporteController extends BaseController
{
    public function index()
    {
        if (session('rol_id') == ROLE_ADMIN_BIENESTAR) {
            return view('AdminBienestar/reportes');
        }
        return redirect()->to('/login');
    }
}