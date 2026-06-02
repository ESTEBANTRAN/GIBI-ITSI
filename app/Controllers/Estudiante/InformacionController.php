<?php

namespace App\Controllers\Estudiante;

use App\Controllers\BaseController;
use App\Models\UsuarioModel;
use App\Security\InputSanitizerTrait;

class InformacionController extends BaseController
{
    use InputSanitizerTrait;

    protected $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
    }

    public function informacionServicios()
    {
        if (!session('id') || session('rol_id') != ROLE_ESTUDIANTE) {
            return redirect()->to('/login');
        }

        $data = [
            'estudiante' => $this->usuarioModel->find(session('id')),
            'titulo' => 'Servicios de Bienestar Institucional',
            'descripcion' => 'Conoce todos los servicios que ofrece la Unidad de Bienestar Institucional del ITSI'
        ];

        return view('estudiante/informacion/servicios', $data);
    }

    public function informacionPsicologia()
    {
        if (!session('id') || session('rol_id') != ROLE_ESTUDIANTE) {
            return redirect()->to('/login');
        }

        $data = [
            'estudiante' => $this->usuarioModel->find(session('id')),
            'titulo' => 'Apoyo Psicológico',
            'descripcion' => 'Servicios de atención psicológica y apoyo emocional'
        ];

        return view('estudiante/informacion/psicologia', $data);
    }

    public function informacionSalud()
    {
        if (!session('id') || session('rol_id') != ROLE_ESTUDIANTE) {
            return redirect()->to('/login');
        }

        $data = [
            'estudiante' => $this->usuarioModel->find(session('id')),
            'titulo' => 'Servicios de Salud',
            'descripcion' => 'Atención médica, prevención y promoción de la salud'
        ];

        return view('estudiante/informacion/salud', $data);
    }

    public function informacionTrabajoSocial()
    {
        if (!session('id') || session('rol_id') != ROLE_ESTUDIANTE) {
            return redirect()->to('/login');
        }

        $data = [
            'estudiante' => $this->usuarioModel->find(session('id')),
            'titulo' => 'Trabajo Social',
            'descripcion' => 'Apoyo socioeconómico y orientación social'
        ];

        return view('estudiante/informacion/trabajo_social', $data);
    }

    public function informacionOrientacionAcademica()
    {
        if (!session('id') || session('rol_id') != ROLE_ESTUDIANTE) {
            return redirect()->to('/login');
        }

        $data = [
            'estudiante' => $this->usuarioModel->find(session('id')),
            'titulo' => 'Orientación Académica',
            'descripcion' => 'Asesoramiento académico y apoyo para el desarrollo estudiantil'
        ];

        return view('estudiante/informacion/orientacion_academica', $data);
    }
}
