<?php

namespace App\Controllers;

class FichaController extends BaseController
{
    public function index()
    {
        if (session('rol_id') == ROLE_ESTUDIANTE) {
            return redirect()->to('estudiante/ficha-socioeconomica');
        }
        return redirect()->to('/login');
    }

    public function adminIndex()
    {
        if (session('rol_id') == ROLE_ADMIN_BIENESTAR) {
            return view('AdminBienestar/fichas');
        }
        return redirect()->to('/login');
    }
}