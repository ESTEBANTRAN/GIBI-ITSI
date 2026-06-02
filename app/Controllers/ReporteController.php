<?php

namespace App\Controllers;

/**
 * @deprecated Usar AdminBienestarController::reportes()
 * @see AdminBienestarController
 */
class ReporteController extends BaseController
{
    public function index()
    {
        log_message('debug', 'ReporteController::index() llamado - deprecated');
        if (session('rol_id') == ROLE_ADMIN_BIENESTAR) {
            return view('AdminBienestar/reportes');
        }
        return redirect()->to('/login');
    }
}