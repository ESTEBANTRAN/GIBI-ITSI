<?php

namespace App\Controllers;

/**
 * @deprecated Usar AdminBienestarController::becas() o EstudianteController::becas()
 * @see AdminBienestarController
 * @see EstudianteController
 */
class BecaController extends BaseController
{
    public function index()
    {
        log_message('debug', 'BecaController::index() llamado - deprecated');
        if (session('rol_id') == ROLE_ESTUDIANTE) {
            return redirect()->to('estudiante/becas');
        }
        return redirect()->to('/login');
    }

    public function adminIndex()
    {
        log_message('debug', 'BecaController::adminIndex() llamado - deprecated');
        if (session('rol_id') == ROLE_ADMIN_BIENESTAR) {
            return view('AdminBienestar/becas');
        }
        return redirect()->to('/login');
    }
}