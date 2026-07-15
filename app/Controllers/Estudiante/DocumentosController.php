<?php

namespace App\Controllers\Estudiante;

use App\Controllers\BaseController;
use App\Models\UsuarioModel;
use App\Security\InputSanitizerTrait;

class DocumentosController extends BaseController
{
    use InputSanitizerTrait;

    protected $db;
    protected $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
        $this->db = \Config\Database::connect();
    }

    public function documentos()
    {
        if (!session('id') || session('rol_id') != ROLE_ESTUDIANTE) {
            return redirect()->to('/login');
        }

        $page = (int)($this->request->getGet('page') ?? 1);
        $perPage = 15;
        $offset = ($page - 1) * $perPage;

        // Count total
        $totalDocumentos = $this->db->table('documentos_solicitud_becas dsb')
            ->join('becas_documentos_requisitos bdr', 'bdr.id = dsb.documento_requerido_id')
            ->join('solicitudes_becas sb', 'sb.id = dsb.solicitud_beca_id')
            ->join('becas b', 'b.id = sb.beca_id')
            ->join('periodos_academicos p', 'p.id = sb.periodo_id')
            ->where('sb.estudiante_id', session('id'))
            ->where('sb.estado !=', 'Rechazada')
            ->where('dsb.ruta_archivo IS NOT NULL')
            ->where('dsb.ruta_archivo !=', '/temp/pendiente_subida.tmp')
            ->countAllResults();

        // Obtener documentos con paginación
        $documentos = $this->db->table('documentos_solicitud_becas dsb')
            ->select("dsb.*, bdr.nombre_documento, bdr.descripcion, sb.beca_id, b.nombre as nombre_beca, sb.periodo_id, p.nombre as periodo_nombre")
            ->join('becas_documentos_requisitos bdr', 'bdr.id = dsb.documento_requerido_id')
            ->join('solicitudes_becas sb', 'sb.id = dsb.solicitud_beca_id')
            ->join('becas b', 'b.id = sb.beca_id')
            ->join('periodos_academicos p', 'p.id = sb.periodo_id')
            ->where('sb.estudiante_id', session('id'))
            ->where('sb.estado !=', 'Rechazada')
            ->where('dsb.ruta_archivo IS NOT NULL')
            ->where('dsb.ruta_archivo !=', '/temp/pendiente_subida.tmp')
            ->orderBy('sb.periodo_id', 'DESC')
            ->orderBy('dsb.orden_revision', 'ASC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        // Normalizar clave tama±o_archivo a tamano_archivo
        foreach ($documentos as &$doc) {
            $keys = array_keys($doc);
            foreach ($keys as $key) {
                if (str_replace("\xC2\xB1", 'n', $key) === 'tamano_archivo' && $key !== 'tamano_archivo') {
                    $doc['tamano_archivo'] = $doc[$key];
                    break;
                }
            }
        }
        unset($doc);

        $totalPages = max(1, ceil($totalDocumentos / $perPage));

        $data = [
            'estudiante' => $this->usuarioModel->find(session('id')),
            'documentos' => $documentos,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'per_page' => $perPage,
            'total' => $totalDocumentos
        ];

        return view('estudiante/documentos', $data);
    }
}
