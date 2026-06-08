<?php

namespace App\Controllers;

use App\Models\SolicitudBecaDocumentoModel;
use App\Models\SolicitudBecaModel;
use App\Models\BecaModel;
use App\Models\FichaSocioeconomicaModel;
use App\Helpers\GoogleDriveHelper;
use App\Security\InputSanitizerTrait;

class DocumentoBecaController extends BaseController
{
    use InputSanitizerTrait;

    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Verificar elegibilidad para una beca específica
     */
    public function verificarElegibilidadBeca()
    {
        try {
            if (!session('id') || session('rol_id') != 1) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Acceso no autorizado'
                ]);
            }

            $estudianteId = session('id');
            $becaId = $this->getPostInt('beca_id');
            
            if (empty($becaId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'ID de beca es obligatorio'
                ]);
            }

            // Verificar ficha socioeconómica aprobada
            $fichaModel = new FichaSocioeconomicaModel();
            $fichaAprobada = $fichaModel->where('estudiante_id', $estudianteId)
                                       ->where('estado', 'Aprobada')
                                       ->first();

            if (!$fichaAprobada) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Debe tener una ficha socioeconómica aprobada',
                    'elegible' => false
                ]);
            }

            // Verificar si ya solicitó esta beca
            $solicitudModel = new SolicitudBecaModel();
            $solicitudExistente = $solicitudModel->where('estudiante_id', $estudianteId)
                                                ->where('beca_id', $becaId)
                                                ->whereIn('estado', ['Postulada', 'En Revisión', 'Aprobada'])
                                                ->first();

            if ($solicitudExistente) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Ya tiene una solicitud activa para esta beca',
                    'elegible' => false
                ]);
            }

            // Obtener información de la beca
            $becaModel = new BecaModel();
            $beca = $becaModel->find($becaId);

            if (!$beca || $beca['estado'] !== 'Activa') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'La beca no está disponible',
                    'elegible' => false
                ]);
            }

            // Verificar cupos disponibles
            if ($beca['cupos_disponibles'] && $beca['cupos_disponibles'] > 0) {
                $solicitudesAprobadas = $solicitudModel->where('beca_id', $becaId)
                                                      ->where('estado', 'Aprobada')
                                                      ->countAllResults();

                if ($solicitudesAprobadas >= $beca['cupos_disponibles']) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'La beca no tiene cupos disponibles',
                        'elegible' => false
                    ]);
                }
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Estudiante elegible para la beca',
                'elegible' => true,
                'beca' => $beca
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error al verificar elegibilidad: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error interno del servidor'
            ]);
        }
    }

    /**
     * Actualizar documentos de una solicitud de beca
     */
    public function actualizarDocumentosBeca()
    {
        try {
            if (!session('id') || session('rol_id') != 1) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Acceso no autorizado'
                ]);
            }

            $estudianteId = session('id');
            $solicitudId = $this->getPostInt('solicitud_id');
            $documentoRequisitoId = $this->getPostInt('documento_requisito_id');
            
            if (empty($solicitudId) || empty($documentoRequisitoId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Parámetros incompletos'
                ]);
            }

            // Verificar que la solicitud pertenece al estudiante
            $solicitudModel = new SolicitudBecaModel();
            $solicitud = $solicitudModel->where('id', $solicitudId)
                                       ->where('estudiante_id', $estudianteId)
                                       ->first();

            if (!$solicitud) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Solicitud no encontrada'
                ]);
            }

            // Verificar que la solicitud esté en estado válido para actualizar
            if (!in_array($solicitud['estado'], ['Postulada', 'En Revisión'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No se puede actualizar documentos en el estado actual'
                ]);
            }

            // Procesar archivo subido
            $archivo = $this->request->getFile('documento');
            
            if (!$archivo->isValid()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Archivo no válido'
                ]);
            }

            // Validar tipo de archivo
            $tiposPermitidos = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
            $extension = $archivo->getExtension();
            
            if (!in_array(strtolower($extension), $tiposPermitidos)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tipo de archivo no permitido'
                ]);
            }

            // Validar tamaño (máximo 10MB)
            if ($archivo->getSize() > 10 * 1024 * 1024) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'El archivo excede el tamaño máximo permitido (10MB)'
                ]);
            }

            // Generar nombre único para el archivo
            $nombreArchivo = 'doc_' . $solicitudId . '_' . $documentoRequisitoId . '_' . time() . '.' . $extension;
            $rutaDestino = 'uploads/becas/documentos/';
            
            // Crear directorio si no existe
            if (!is_dir($rutaDestino)) {
                mkdir($rutaDestino, 0755, true);
            }

            // Mover archivo
            $archivo->move($rutaDestino, $nombreArchivo);
            $rutaCompleta = $rutaDestino . $nombreArchivo;

            // Guardar información en la base de datos
            $documentoModel = new SolicitudBecaDocumentoModel();
            
            // Verificar si ya existe un documento para este requisito
            $documentoExistente = $documentoModel->where('solicitud_beca_id', $solicitudId)
                                                ->where('documento_requisito_id', $documentoRequisitoId)
                                                ->first();

            $datosDocumento = [
                'solicitud_beca_id' => $solicitudId,
                'documento_requisito_id' => $documentoRequisitoId,
                'nombre_archivo' => $archivo->getClientName(),
                'ruta_archivo' => $rutaCompleta,
                'tipo_archivo' => $archivo->getMimeType(),
                'tamano_archivo' => $archivo->getSize(),
                'estado' => 'Pendiente',
                'fecha_subida' => date('Y-m-d H:i:s')
            ];

            if ($documentoExistente) {
                // Actualizar documento existente
                $documentoModel->update($documentoExistente['id'], $datosDocumento);
                $mensaje = 'Documento actualizado exitosamente';
            } else {
                // Crear nuevo documento
                $documentoModel->insert($datosDocumento);
                $mensaje = 'Documento subido exitosamente';
            }

            // Actualizar porcentaje de avance de la solicitud
            $this->actualizarPorcentajeAvanceSolicitud($solicitudId);

            return $this->response->setJSON([
                'success' => true,
                'message' => $mensaje
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error al actualizar documentos: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error interno del servidor'
            ]);
        }
    }

    /**
     * Descargar documento de beca
     */
    public function descargarDocumentoBeca($id)
    {
        try {
            if (!session('id') || session('rol_id') != 1) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Acceso no autorizado'
                ]);
            }

            $estudianteId = session('id');
            
            $documentoModel = new SolicitudBecaDocumentoModel();
            $documento = $documentoModel->select('documentos_solicitud_becas.*, solicitudes_becas.estudiante_id')
                                       ->join('solicitudes_becas', 'solicitudes_becas.id = documentos_solicitud_becas.solicitud_beca_id')
                                       ->where('documentos_solicitud_becas.id', $id)
                                       ->where('solicitudes_becas.estudiante_id', $estudianteId)
                                       ->first();

            if (!$documento) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Documento no encontrado'
                ]);
            }

            $rutaArchivo = FCPATH . $documento['ruta_archivo'];
            
            if (!file_exists($rutaArchivo)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Archivo no encontrado en el servidor'
                ]);
            }

            return $this->response->download($rutaArchivo, null)->setFileName($documento['nombre_archivo']);

        } catch (\Exception $e) {
            log_message('error', 'Error al descargar documento: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error interno del servidor'
            ]);
        }
    }

    /**
     * Mostrar y gestionar documentos de una solicitud de beca
     */
    public function documentosBeca($solicitudId)
    {
        try {
            $estudianteId = session('id');
            $solicitud = $this->db->table('solicitudes_becas sb')
                ->select('sb.*, b.nombre as beca_nombre, p.nombre as periodo_nombre')
                ->join('becas b', 'b.id = sb.beca_id')
                ->join('periodos_academicos p', 'p.id = sb.periodo_id')
                ->where('sb.id', $solicitudId)
                ->where('sb.estudiante_id', $estudianteId)
                ->get()
                ->getRowArray();

            if (!$solicitud) {
                return redirect()->to('estudiante/becas')->with('error', 'Solicitud no encontrada');
            }

            $beca = (new BecaModel())->find($solicitud['beca_id']);
            if (!$beca) {
                return redirect()->to('estudiante/becas')->with('error', 'Beca no encontrada');
            }

            // Obtener documentos de la solicitud
            $documentos = $this->db->table('documentos_solicitud_becas dsb')
                ->select('dsb.*, bdr.nombre_documento, bdr.descripcion, bdr.obligatorio')
                ->join('becas_documentos_requisitos bdr', 'bdr.id = dsb.documento_requerido_id')
                ->where('dsb.solicitud_beca_id', $solicitudId)
                ->orderBy('dsb.orden_revision', 'ASC')
                ->get()
                ->getResultArray();

            if (empty($documentos)) {
                $documentosRequeridos = $this->db->table('becas_documentos_requisitos')
                    ->where('beca_id', $solicitud['beca_id'])
                    ->where('estado', 'Activo')
                    ->orderBy('orden_verificacion', 'ASC')
                    ->get()
                    ->getResultArray();

                foreach ($documentosRequeridos as $doc) {
                    $this->db->table('documentos_solicitud_becas')->insert([
                        'solicitud_beca_id' => $solicitudId,
                        'documento_requerido_id' => $doc['id'],
                        'nombre_archivo' => '',
                        'ruta_archivo' => '',
                        'orden_revision' => $doc['orden_verificacion'],
                        'estado' => 'Pendiente',
                        'fecha_subida' => date('Y-m-d H:i:s')
                    ]);
                }

                $documentos = $this->db->table('documentos_solicitud_becas dsb')
                    ->select('dsb.*, bdr.nombre_documento, bdr.descripcion, bdr.obligatorio')
                    ->join('becas_documentos_requisitos bdr', 'bdr.id = dsb.documento_requerido_id')
                    ->where('dsb.solicitud_beca_id', $solicitudId)
                    ->orderBy('dsb.orden_revision', 'ASC')
                    ->get()
                    ->getResultArray();
            }

            $totalDocumentos = count($documentos);
            $documentosSubidos = count(array_filter($documentos, fn($d) => $d['estado'] !== 'Pendiente'));
            $documentosAprobados = count(array_filter($documentos, fn($d) => $d['estado'] === 'Aprobado'));
            $porcentajeAvance = $totalDocumentos > 0 ? round(($documentosAprobados / $totalDocumentos) * 100, 1) : 0;

            $data = [
                'solicitud' => $solicitud,
                'beca' => $beca,
                'documentos' => $documentos,
                'estadisticas' => [
                    'total' => $totalDocumentos,
                    'subidos' => $documentosSubidos,
                    'aprobados' => $documentosAprobados,
                    'porcentaje' => $porcentajeAvance
                ]
            ];

            return view('estudiante/documentos_beca', $data);

        } catch (\Exception $e) {
            log_message('error', 'Error mostrando documentos de beca: ' . $e->getMessage());
            return redirect()->to('estudiante/becas')->with('error', 'Error del sistema');
        }
    }

    /**
     * Subir documento para una solicitud de beca
     */
    public function subirDocumento()
    {
        try {
            $estudianteId = session('id');
            $solicitudId = $this->getPostInt('solicitud_id');
            $documentoId = $this->getPostInt('documento_id');

            // Verificar que la solicitud pertenece al estudiante
            $solicitud = $this->db->table('solicitudes_becas')
                ->select('solicitudes_becas.*, b.id as beca_id, p.id as periodo_id')
                ->join('becas b', 'b.id = solicitudes_becas.beca_id')
                ->join('periodos_academicos p', 'p.id = solicitudes_becas.periodo_id')
                ->where('solicitudes_becas.id', $solicitudId)
                ->where('solicitudes_becas.estudiante_id', $estudianteId)
                ->get()
                ->getRowArray();

            if (!$solicitud) {
                return $this->response->setJSON(['success' => false, 'message' => 'Solicitud no encontrada']);
            }

            // Verificar que el documento existe
            $documento = $this->db->table('documentos_solicitud_becas')
                ->where('id', $documentoId)
                ->where('solicitud_beca_id', $solicitudId)
                ->get()
                ->getRowArray();

            if (!$documento) {
                return $this->response->setJSON(['success' => false, 'message' => 'Documento no encontrado']);
            }

            // Obtener el archivo
            $archivo = $this->request->getFile('archivo');
            if (!$archivo || !$archivo->isValid()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Archivo no válido']);
            }

            // Validar tipo de archivo (solo PDF)
            if ($archivo->getMimeType() !== 'application/pdf') {
                return $this->response->setJSON(['success' => false, 'message' => 'Solo se permiten archivos PDF']);
            }

            // Validar tamaño (máximo 2MB)
            if ($archivo->getSize() > 2 * 1024 * 1024) {
                return $this->response->setJSON(['success' => false, 'message' => 'El archivo no puede superar 2MB']);
            }

            // Generar nombre único para el archivo
            $nombreArchivo = 'doc_' . $solicitudId . '_' . $documentoId . '_' . time() . '.pdf';
            $rutaDestino = 'uploads/documentos_becas/' . $nombreArchivo;

            // Crear directorio si no existe
            $directorio = FCPATH . 'uploads/documentos_becas/';
            if (!is_dir($directorio)) {
                mkdir($directorio, 0755, true);
            }

            // Mover archivo
            if (!$archivo->move($directorio, $nombreArchivo)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Error al guardar el archivo']);
            }

            // Subir a Google Drive si la integración está activa
            $googleDriveId = null;
            try {
                $googleDriveId = GoogleDriveHelper::subirArchivo($rutaDestino, $archivo->getClientName(), $archivo->getMimeType());
            } catch (\Exception $ex) {
                log_message('error', 'Error al subir a Google Drive en DocumentoBecaController::subirDocumento: ' . $ex->getMessage());
            }

            // Actualizar documento en la base de datos
            $updateData = [
                'nombre_archivo' => $archivo->getClientName(),
                'ruta_archivo' => $rutaDestino,
                'estado' => 'En Revision',
                'fecha_subida' => date('Y-m-d H:i:s'),
                'tamaño_archivo' => $archivo->getSize(),
                'tipo_mime' => $archivo->getMimeType()
            ];

            if ($googleDriveId) {
                $updateData['google_drive_id'] = $googleDriveId;
            }

            $this->db->table('documentos_solicitud_becas')
                ->where('id', $documentoId)
                ->update($updateData);

            // Actualizar progreso de la solicitud
            $this->actualizarProgresoSolicitud($solicitudId);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Documento subido exitosamente' . ($googleDriveId ? ' y respaldado en la nube' : ''),
                'archivo' => $archivo->getClientName(),
                'google_drive_id' => $googleDriveId
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error subiendo documento: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Error del sistema']);
        }
    }

    /**
     * Descargar documento
     */
    public function descargarDocumento($documentoId)
    {
        try {
            $estudianteId = session('id');
            
            $documento = $this->db->table('documentos_solicitud_becas dsb')
                ->select('dsb.*, sb.estudiante_id')
                ->join('solicitudes_becas sb', 'sb.id = dsb.solicitud_beca_id')
                ->where('dsb.id', $documentoId)
                ->where('sb.estudiante_id', $estudianteId)
                ->get()
                ->getRowArray();

            if (!$documento) {
                return redirect()->back()->with('error', 'Documento no encontrado');
            }

            $rutaArchivo = FCPATH . $documento['ruta_archivo'];
            if (!file_exists($rutaArchivo)) {
                return redirect()->back()->with('error', 'Archivo no encontrado');
            }

            return $this->response->download($rutaArchivo, null)->setFileName($documento['nombre_archivo']);

        } catch (\Exception $e) {
            log_message('error', 'Error descargando documento: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error del sistema');
        }
    }

    /**
     * Eliminar documento
     */
    public function eliminarDocumento()
    {
        try {
            $estudianteId = session('id');
            $documentoId = $this->getPostInt('documento_id');

            // Verificar que el documento pertenece a una solicitud del estudiante
            $documento = $this->db->table('documentos_solicitud_becas dsb')
                ->select('dsb.*, sb.estudiante_id')
                ->join('solicitudes_becas sb', 'sb.id = dsb.solicitud_beca_id')
                ->where('dsb.id', $documentoId)
                ->where('sb.estudiante_id', $estudianteId)
                ->get()
                ->getRowArray();

            if (!$documento) {
                return $this->response->setJSON(['success' => false, 'message' => 'Documento no encontrado']);
            }

            // Solo permitir eliminar si está en estado "En Revision" o "Pendiente"
            if (!in_array($documento['estado'], ['En Revision', 'Pendiente'])) {
                return $this->response->setJSON(['success' => false, 'message' => 'No se puede eliminar un documento ya revisado']);
            }

            // Eliminar archivo físico
            $rutaArchivo = FCPATH . $documento['ruta_archivo'];
            if (file_exists($rutaArchivo) && $documento['ruta_archivo'] !== '/temp/pendiente_subida.tmp') {
                unlink($rutaArchivo);
            }

            // Actualizar documento en la base de datos
            $this->db->table('documentos_solicitud_becas')
                ->where('id', $documentoId)
                ->update([
                    'nombre_archivo' => 'pendiente_subida.tmp',
                    'ruta_archivo' => '/temp/pendiente_subida.tmp',
                    'estado' => 'Pendiente',
                    'fecha_subida' => null,
                    'tamaño_archivo' => null,
                    'tipo_mime' => null
                ]);

            // Actualizar progreso de la solicitud
            $this->actualizarProgresoSolicitud($documento['solicitud_beca_id']);

            return $this->response->setJSON(['success' => true, 'message' => 'Documento eliminado exitosamente']);

        } catch (\Exception $e) {
            log_message('error', 'Error eliminando documento: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Error del sistema']);
        }
    }

    // ========================================
    // MÉTODOS PRIVADOS DE APOYO
    // ========================================

    /**
     * Actualizar porcentaje de avance de una solicitud
     */
    private function actualizarPorcentajeAvanceSolicitud($solicitudId)
    {
        try {
            $documentoModel = new SolicitudBecaDocumentoModel();
            $solicitudModel = new SolicitudBecaModel();
            
            $totalDocumentos = $documentoModel->where('solicitud_beca_id', $solicitudId)->countAllResults();
            
            if ($totalDocumentos > 0) {
                $documentosSubidos = $documentoModel->where('solicitud_beca_id', $solicitudId)
                                                    ->where('estado !=', 'Pendiente')
                                                    ->countAllResults();
                
                $documentosAprobados = $documentoModel->where('solicitud_beca_id', $solicitudId)
                                                    ->where('estado', 'Aprobado')
                                                    ->countAllResults();
                
                $porcentaje = round(($documentosSubidos / $totalDocumentos) * 100, 1);
                
                $solicitudModel->update($solicitudId, [
                    'porcentaje_avance' => $porcentaje,
                    'total_documentos' => $totalDocumentos,
                    'documentos_revisados' => $documentosSubidos
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error al actualizar porcentaje de avance: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar progreso de una solicitud de beca
     */
    private function actualizarProgresoSolicitud($solicitudId)
    {
        try {
            $estados = $this->db->table('documentos_solicitud_becas')
                ->select('estado, COUNT(*) as total')
                ->where('solicitud_beca_id', $solicitudId)
                ->groupBy('estado')
                ->get()
                ->getResultArray();

            $totalDocumentos = 0;
            $documentosSubidos = 0;
            $documentosAprobados = 0;

            foreach ($estados as $estado) {
                $totalDocumentos += $estado['total'];
                if ($estado['estado'] !== 'Pendiente') {
                    $documentosSubidos += $estado['total'];
                }
                if ($estado['estado'] === 'Aprobado') {
                    $documentosAprobados += $estado['total'];
                }
            }

            $porcentajeAvance = $totalDocumentos > 0 ? round(($documentosSubidos / $totalDocumentos) * 100, 1) : 0;

            $this->db->table('solicitudes_becas')
                ->where('id', $solicitudId)
                ->update([
                    'documentos_revisados' => $documentosSubidos,
                    'total_documentos' => $totalDocumentos,
                    'porcentaje_avance' => $porcentajeAvance
                ]);

        } catch (\Exception $e) {
            log_message('error', 'Error actualizando progreso de solicitud: ' . $e->getMessage());
        }
    }
}
