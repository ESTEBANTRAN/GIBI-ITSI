<?php

namespace App\Controllers;

/**
 * @deprecated Usar AdminBienestarController o EstudianteController
 * @see AdminBienestarController
 * @see EstudianteController
 */
class FichaController extends BaseController
{
    public function index()
    {
        log_message('debug', 'FichaController::index() llamado - deprecated');
        if (session('rol_id') == ROLE_ESTUDIANTE) {
            return redirect()->to('estudiante/ficha-socioeconomica');
        }
        return redirect()->to('/login');
    }

    public function adminIndex()
    {
        log_message('debug', 'FichaController::adminIndex() llamado - deprecated');
        if (session('rol_id') == ROLE_ADMIN_BIENESTAR) {
            return view('AdminBienestar/fichas');
        }
        return redirect()->to('/login');
    }
}