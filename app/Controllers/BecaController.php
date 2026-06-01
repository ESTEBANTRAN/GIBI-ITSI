<?php

namespace App\Controllers;

class BecaController extends BaseController
{
    public function index()
    {
        if (session('rol_id') == ROLE_ESTUDIANTE) {
            return redirect()->to('estudiante/becas');
        }
        return redirect()->to('/login');
    }

    public function adminIndex()
    {
        if (session('rol_id') == ROLE_ADMIN_BIENESTAR) {
            return view('AdminBienestar/becas');
        }
        return redirect()->to('/login');
    }
}