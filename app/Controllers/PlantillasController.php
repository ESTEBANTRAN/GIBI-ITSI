<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Services\PlantillaPDFService;
use App\Services\PlantillaWordService;

/**
 * Controlador para probar y gestionar plantillas de PDF
 */
class PlantillasController extends Controller
{
    /**
     * Vista de prueba de plantillas
     */
    public function index()
    {
        $plantillaWordService = new PlantillaWordService();
        $plantillas = $plantillaWordService->listarPlantillas();
        
        return view('plantillas/index', [
            'plantillas' => $plantillas
        ]);
    }

    /**
     * Probar generación de PDF con plantilla personalizada
     */
    public function probarPlantillaPersonalizada()
    {
        try {
            $plantillaService = new PlantillaPDFService();
            
            // Datos de ejemplo
            $ficha = [
                'id' => 1,
                'estado' => 'Enviada',
                'fecha_creacion' => date('Y-m-d H:i:s')
            ];
            
            $estudiante = [
                'nombre' => 'Juan Carlos',
                'apellido' => 'Pérez González',
                'cedula' => '1234567890',
                'email' => 'juan.perez@example.com'
            ];
            
            $periodo = [
                'nombre' => 'Primer Semestre 2024-2025'
            ];
            
            $datosFicha = [
                'ingresos_padre' => 1200.00,
                'ingresos_madre' => 800.00,
                'otros_ingresos' => 300.00,
                'gastos_vivienda' => 400.00,
                'gastos_alimentacion' => 600.00,
                'otros_gastos' => 200.00,
                'numero_dependientes' => 3,
                'tipo_vivienda' => 'Alquilada',
                'zona_residencia' => 'Urbana',
                'nivel_educativo_padres' => 'Secundaria'
            ];
            
            // Generar PDF
            $pdf = $plantillaService->generarFichaSocioeconomicaPDF($ficha, $estudiante, $periodo, $datosFicha);
            
            // Configurar nombre del archivo
            $filename = 'Ficha_Socioeconomica_Ejemplo.pdf';
            
            // Salida del PDF como descarga
            $pdf->Output($filename, 'D');
            
        } catch (\Exception $e) {
            log_message('error', 'Error probando plantilla personalizada: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Error generando PDF'
            ]);
        }
    }

    /**
     * Probar generación de PDF con plantilla de Word existente
     */
    public function probarPlantillaWord()
    {
        try {
            $plantillaWordService = new PlantillaWordService();
            
            // Verificar si existe la plantilla
            if (!$plantillaWordService->plantillaExiste('PLANTILLA PDFS.docx')) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Plantilla PLANTILLA PDFS.docx no encontrada'
                ]);
            }
            
            // Datos de ejemplo
            $ficha = [
                'id' => 1,
                'estado' => 'Enviada',
                'fecha_creacion' => date('Y-m-d H:i:s')
            ];
            
            $estudiante = [
                'nombre' => 'María Elena',
                'apellido' => 'Rodríguez Silva',
                'cedula' => '0987654321',
                'email' => 'maria.rodriguez@example.com',
                'carrera_nombre' => 'Ingeniería en Sistemas'
            ];
            
            $periodo = [
                'nombre' => 'Segundo Semestre 2024-2025',
                'fecha_inicio' => '2024-08-01'
            ];
            
            $datosFicha = [
                'ingresos_padre' => 1500.00,
                'ingresos_madre' => 1200.00,
                'otros_ingresos' => 500.00,
                'gastos_vivienda' => 500.00,
                'gastos_alimentacion' => 700.00,
                'otros_gastos' => 300.00,
                'numero_dependientes' => 2,
                'tipo_vivienda' => 'Propia',
                'zona_residencia' => 'Semi-urbana',
                'nivel_educativo_padres' => 'Superior'
            ];
            
            // Generar PDF usando plantilla de Word
            $pdf = $plantillaWordService->generarFichaSocioeconomicaConPlantilla($ficha, $estudiante, $periodo, $datosFicha);
            
            // Configurar nombre del archivo
            $filename = 'Ficha_Socioeconomica_Plantilla_Word.pdf';
            
            // Salida del PDF como descarga
            $pdf->Output($filename, 'D');
            
        } catch (\Exception $e) {
            log_message('error', 'Error probando plantilla de Word: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Error generando PDF'
            ]);
        }
    }

    /**
     * Vista de gestión de plantillas
     */
    public function gestionar()
    {
        $plantillaWordService = new PlantillaWordService();
        $plantillas = $plantillaWordService->listarPlantillas();
        
        return view('plantillas/gestionar', [
            'plantillas' => $plantillas
        ]);
    }

    /**
     * Subir nueva plantilla
     */
    public function subirPlantilla()
    {
        try {
            $archivo = $this->request->getFile('plantilla');
            
            if (!$archivo->isValid()) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Archivo no válido'
                ]);
            }
            
            // Verificar tipo de archivo
            if ($archivo->getMimeType() !== 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Solo se permiten archivos .docx'
                ]);
            }
            
            // Verificar tamaño (máximo 10MB)
            if ($archivo->getSize() > 10 * 1024 * 1024) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'El archivo es demasiado grande (máximo 10MB)'
                ]);
            }
            
            // Mover archivo a directorio de plantillas
            $nombreArchivo = $archivo->getName();
            $rutaDestino = FCPATH . 'sistema/assets/plantilla/' . $nombreArchivo;
            
            if ($archivo->move(FCPATH . 'sistema/assets/plantilla/', $nombreArchivo)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Plantilla subida correctamente',
                    'archivo' => $nombreArchivo
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Error al mover el archivo'
                ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Error subiendo plantilla: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Error del sistema'
            ]);
        }
    }

    /**
     * Eliminar plantilla
     */
    public function eliminarPlantilla($nombreArchivo)
    {
        try {
            $rutaArchivo = FCPATH . 'sistema/assets/plantilla/' . $nombreArchivo;
            
            if (!file_exists($rutaArchivo)) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Plantilla no encontrada'
                ]);
            }
            
            if (unlink($rutaArchivo)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Plantilla eliminada correctamente'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Error al eliminar la plantilla'
                ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Error eliminando plantilla: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Error del sistema'
            ]);
        }
    }

    /**
     * Vista previa de plantilla
     */
    public function vistaPrevia($nombreArchivo)
    {
        try {
            $plantillaWordService = new PlantillaWordService();
            $info = $plantillaWordService->obtenerInfoPlantilla($nombreArchivo);
            
            if (!$info) {
                return redirect()->back()->with('error', 'Plantilla no encontrada');
            }
            
            return view('plantillas/vista_previa', [
                'plantilla' => $info
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error mostrando vista previa: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error del sistema');
        }
    }
}
