<?php

namespace App\Services;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\Settings;
use TCPDF;

/**
 * Servicio para generar PDFs usando plantillas de Word
 * Integra PHPWord + TCPDF para máxima calidad
 */
class PlantillaPDFService
{
    private $plantillasPath;
    private $tempPath;

    public function __construct()
    {
        $this->plantillasPath = FCPATH . 'sistema/assets/plantilla/';
        $this->tempPath = WRITEPATH . 'temp/';
        
        // Crear directorio temporal si no existe
        if (!is_dir($this->tempPath)) {
            mkdir($this->tempPath, 0755, true);
        }
        
        // Configurar PHPWord
        Settings::setOutputEscapingEnabled(true);
    }

    /**
     * Generar PDF de ficha socioeconómica usando plantilla
     */
    public function generarFichaSocioeconomicaPDF($ficha, $estudiante, $periodo, $datosFicha)
    {
        try {
            // Generar PDF directamente con TCPDF (más simple y confiable)
            $pdf = $this->generarPDFDirecto($ficha, $estudiante, $periodo, $datosFicha);
            
            return $pdf;
            
        } catch (\Exception $e) {
            log_message('error', 'Error generando PDF con plantilla: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generar PDF directamente con TCPDF (método simplificado)
     */
    private function generarPDFDirecto($ficha, $estudiante, $periodo, $datosFicha)
    {
        // Crear nueva instancia de TCPDF
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // Configurar información del documento
        $pdf->SetCreator('Sistema de Bienestar Estudiantil');
        $pdf->SetAuthor('ITSI - Administrador');
        $pdf->SetTitle('Ficha Socioeconómica');
        $pdf->SetSubject('Ficha Socioeconómica - Vista Administrador');
        
        // Configurar márgenes
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetHeaderMargin(5);
        $pdf->SetFooterMargin(10);
        
        // Configurar auto page breaks
        $pdf->SetAutoPageBreak(TRUE, 25);
        
        // Agregar página
        $pdf->AddPage();
        
        // Insertar imagen del header usando métodos nativos de TCPDF
        $this->insertarHeaderImagen($pdf);
        
        // Generar contenido HTML (sin el header)
        $html = $this->generarHTMLDirecto($ficha, $estudiante, $periodo, $datosFicha);
        
        // Escribir HTML en el PDF
        $pdf->writeHTML($html, true, false, true, false, '');
        
        return $pdf;
    }
    
    /**
     * Insertar imagen del header usando métodos nativos de TCPDF
     */
    private function insertarHeaderImagen($pdf)
    {
        $headerImagePath = FCPATH . 'sistema/assets/images/docs/header_doc.jpg';
        $headerImagePathPNG = FCPATH . 'sistema/assets/images/docs/header_doc.png';
        
        if (file_exists($headerImagePath)) {
            // Obtener dimensiones de la página
            $pageWidth = $pdf->getPageWidth();
            $pageHeight = $pdf->getPageHeight();
            $marginLeft = $pdf->getMargins()['left'];
            $marginRight = $pdf->getMargins()['right'];
            
            // Calcular ancho disponible para la imagen
            $availableWidth = $pageWidth - $marginLeft - $marginRight;
            
            // Insertar imagen usando TCPDF nativo
            $pdf->Image($headerImagePath, $marginLeft, $pdf->GetY(), $availableWidth, 0, 'JPG', '', '', false, 300, '', false, false, 0, false, false, false);
            
            // Mover el cursor después de la imagen
            $pdf->SetY($pdf->GetY() + 30); // Espacio después del header
            
        } elseif (file_exists($headerImagePathPNG)) {
            // Si solo existe PNG, mostrar mensaje de error
            $pdf->SetTextColor(220, 53, 69); // Rojo
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->Cell(0, 10, '⚠️ ERROR: header_doc.png no es compatible con TCPDF', 0, 1, 'C');
            $pdf->Cell(0, 10, 'SOLUCIÓN: Convierte la imagen a JPG', 0, 1, 'C');
            $pdf->SetTextColor(0, 0, 0); // Volver a negro
            $pdf->SetFont('helvetica', '', 11);
            $pdf->Ln(10);
        } else {
            // Fallback si no hay imagen
            $pdf->SetTextColor(46, 134, 171); // Azul ITSI
            $pdf->SetFont('helvetica', 'B', 16);
            $pdf->Cell(0, 10, 'ITSI - Instituto Superior Tecnológico Ibarra', 0, 1, 'C');
            $pdf->SetFont('helvetica', 'B', 14);
            $pdf->Cell(0, 10, 'UNIDAD DE BIENESTAR INSTITUCIONAL', 0, 1, 'C');
            $pdf->SetTextColor(0, 0, 0); // Volver a negro
            $pdf->SetFont('helvetica', '', 11);
            $pdf->Ln(10);
                }
    }
    
    /**
     * Generar código de verificación para el PDF
     */
    private function generarCodigoVerificacion($idDocumento, $tipoDocumento)
    {
        $codigoModel = new \App\Models\PdfCodigoVerificacionModel();
        $idUsuario = session('id') ?? 1; // Usuario actual o default
        
        return $codigoModel->generarCodigoUnico($tipoDocumento, $idDocumento, $idUsuario);
    }
    
    /**
     * Generar HTML directamente para el PDF
     */
    private function generarHTMLDirecto($ficha, $estudiante, $periodo, $datosFicha)
    {
        $html = '
        <style>
            body { font-family: Arial, sans-serif; font-size: 11px; line-height: 1.4; }
            .header-image { width: 100%; height: auto; margin-bottom: 20px; }
            .header-info { text-align: center; margin-bottom: 20px; background-color: #f8f9fa; padding: 15px; border-radius: 5px; }
            .section { margin-bottom: 15px; }
            .section-title { background-color: #f0f0f0; padding: 8px; font-weight: bold; margin-bottom: 10px; color: #2E86AB; }
            .field { margin-bottom: 8px; }
            .field-label { font-weight: bold; display: inline-block; width: 200px; color: #2E86AB; }
            .field-value { display: inline-block; }
            .table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
            .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            .table th { background-color: #E8F4FD; font-weight: bold; color: #2E86AB; }
            .total { background-color: #E8F5E8; padding: 10px; text-align: center; font-weight: bold; color: #28A745; }
            .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #666; border-top: 1px solid #ddd; padding-top: 10px; }
        </style>';
        
        // El header ya se insertó usando TCPDF nativo, no es necesario aquí
        
        $html .= '
        <!-- INFORMACIÓN DEL DOCUMENTO -->
        <div class="header-info">
            <h2 style="color: #2E86AB; margin: 0 0 10px 0;">FICHA SOCIOECONÓMICA - VISTA ADMINISTRADOR</h2>
            <div style="display: flex; justify-content: space-between; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 200px; margin: 5px;">
                    <strong style="color: #2E86AB;">Período:</strong> ' . htmlspecialchars($periodo['nombre'] ?? 'N/A') . '
                </div>
                <div style="flex: 1; min-width: 200px; margin: 5px;">
                    <strong style="color: #2E86AB;">Estado:</strong> <span style="background-color: #17A2B8; color: white; padding: 2px 8px; border-radius: 3px;">' . htmlspecialchars($ficha['estado']) . '</span>
                </div>
                <div style="flex: 2; min-width: 200px; margin: 5px;">
                    <strong style="color: #2E86AB;">Fecha de Creación:</strong> ' . date('d/m/Y H:i', strtotime($ficha['fecha_creacion'])) . '
                </div>
            </div>
        </div>';
        
        // 1. INFORMACIÓN DEL ESTUDIANTE
        $html .= '
        <div class="section">
            <div class="section-title">1. INFORMACIÓN DEL ESTUDIANTE</div>
            <div class="field">
                <span class="field-label">Apellidos y Nombres:</span>
                <span class="field-value">' . htmlspecialchars($estudiante['apellido'] . ' ' . $estudiante['nombre']) . '</span>
            </div>
            <div class="field">
                <span class="field-label">Cédula:</span>
                <span class="field-value">' . htmlspecialchars($estudiante['cedula']) . '</span>
            </div>
            <div class="field">
                <span class="field-label">Email:</span>
                <span class="field-value">' . htmlspecialchars($estudiante['email']) . '</span>
            </div>
            <div class="field">
                <span class="field-label">ID de Ficha:</span>
                <span class="field-value">' . htmlspecialchars($ficha['id']) . '</span>
            </div>
        </div>';
        
        // 2. DATOS SOCIOECONÓMICOS
        if (!empty($datosFicha)) {
            $html .= '
            <div class="section">
                <div class="section-title">2. DATOS SOCIOECONÓMICOS</div>';
            
            // Tabla de ingresos
            $html .= '
            <div class="section">
                <h4 style="color: #A23B72;">2.1 Ingresos Familiares</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Concepto</th>
                            <th>Monto ($)</th>
                        </tr>
                    </thead>
                    <tbody>';
            
            $ingresos = [
                'ingresos_padre' => 'Ingresos del Padre',
                'ingresos_madre' => 'Ingresos de la Madre',
                'otros_ingresos' => 'Otros Ingresos'
            ];
            
            foreach ($ingresos as $campo => $label) {
                if (isset($datosFicha[$campo])) {
                    $html .= '<tr><td>' . htmlspecialchars($label) . '</td><td>$' . number_format($datosFicha[$campo], 2) . '</td></tr>';
                }
            }
            
            $html .= '</tbody></table></div>';
            
            // Tabla de gastos
            $html .= '
            <div class="section">
                <h4 style="color: #F18F01;">2.2 Gastos Familiares</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Concepto</th>
                            <th>Monto ($)</th>
                        </tr>
                    </thead>
                    <tbody>';
            
            $gastos = [
                'gastos_vivienda' => 'Gastos de Vivienda',
                'gastos_alimentacion' => 'Gastos de Alimentación',
                'otros_gastos' => 'Otros Gastos'
            ];
            
            foreach ($gastos as $campo => $label) {
                if (isset($datosFicha[$campo])) {
                    $html .= '<tr><td>' . htmlspecialchars($label) . '</td><td>$' . number_format($datosFicha[$campo], 2) . '</td></tr>';
                }
            }
            
            $html .= '</tbody></table></div>';
            
            // Información familiar
            $html .= '
            <div class="section">
                <h4 style="color: #2E86AB;">2.3 Información Familiar</h4>';
            
            $info = [
                'numero_dependientes' => 'Número de Dependientes',
                'tipo_vivienda' => 'Tipo de Vivienda',
                'zona_residencia' => 'Zona de Residencia',
                'nivel_educativo_padres' => 'Nivel Educativo de los Padres'
            ];
            
            foreach ($info as $campo => $label) {
                if (isset($datosFicha[$campo])) {
                    $html .= '<div class="field">
                        <span class="field-label">' . htmlspecialchars($label) . ':</span>
                        <span class="field-value">' . htmlspecialchars($datosFicha[$campo]) . '</span>
                    </div>';
                }
            }
            
            $html .= '</div>';
            
            // Total de ingresos
            $totalIngresos = 0;
            if (isset($datosFicha['ingresos_padre'])) $totalIngresos += floatval($datosFicha['ingresos_padre']);
            if (isset($datosFicha['ingresos_madre'])) $totalIngresos += floatval($datosFicha['ingresos_madre']);
            if (isset($datosFicha['otros_ingresos'])) $totalIngresos += floatval($datosFicha['otros_ingresos']);
            
            $html .= '
            <div class="total">
                <h3 style="margin: 0; color: #28A745;">TOTAL DE INGRESOS FAMILIARES</h3>
                <h2 style="margin: 5px 0; color: #28A745;">$' . number_format($totalIngresos, 2) . '</h2>
            </div>';
        }
        
        // 3. INFORMACIÓN DE ADMINISTRACIÓN
        $html .= '
        <div class="section">
            <div class="section-title">3. INFORMACIÓN DE ADMINISTRACIÓN</div>
            <div class="field">
                <span class="field-label">Administrador Revisor:</span>
                <span class="field-value">' . htmlspecialchars(session('nombre') ?? 'N/A') . '</span>
            </div>
            <div class="field">
                <span class="field-label">Fecha de Revisión:</span>
                <span class="field-value">' . date('d/m/Y H:i:s') . '</span>
            </div>
            <div class="field">
                <span class="field-label">ID de Administrador:</span>
                <span class="field-value">' . htmlspecialchars(session('id') ?? 'N/A') . '</span>
            </div>
            <div class="field">
                <span class="field-label">Rol:</span>
                <span class="field-value">Administrador de Bienestar Estudiantil</span>
            </div>
        </div>';
        
        // 4. FOOTER CON CÓDIGO DE VERIFICACIÓN
        $codigoVerificacion = $this->generarCodigoVerificacion($ficha['id'], 'ficha_socioeconomica');
        
        $html .= '
        <div class="footer">
            <div style="border-top: 2px solid #ddd; padding-top: 15px; margin-top: 20px;">
                <div style="text-align: center; margin-bottom: 10px;">
                    <p style="margin: 5px 0; font-size: 10px; color: #666;">Documento generado automáticamente por el Sistema de Bienestar Estudiantil</p>
                    <p style="margin: 5px 0; font-size: 10px; color: #666;">Instituto Tecnológico Superior de Ibarra - ' . date('Y') . '</p>
                </div>
                <div style="text-align: center; background-color: #f8f9fa; padding: 10px; border: 1px solid #dee2e6; border-radius: 5px;">
                    <p style="margin: 0; font-size: 11px; color: #495057;"><strong>Código de Verificación:</strong></p>
                    <p style="margin: 5px 0; font-size: 14px; color: #2E86AB; font-family: monospace; font-weight: bold;">' . $codigoVerificacion . '</p>
                    <p style="margin: 0; font-size: 9px; color: #6c757d;">Este código puede ser verificado en el sistema para confirmar la autenticidad del documento</p>
                </div>
            </div>
        </div>';
        
        return $html;
    }

}
