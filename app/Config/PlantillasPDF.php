<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class PlantillasPDF extends BaseConfig
{
    /**
     * Configuración de plantillas para PDFs
     */
    
    // Colores institucionales
    public $colores = [
        'primario' => '2E86AB',      // Azul institucional
        'secundario' => 'A23B72',    // Morado
        'acento' => 'F18F01',        // Naranja
        'exito' => '28A745',         // Verde
        'info' => '17A2B8',          // Azul claro
        'advertencia' => 'FFC107',    // Amarillo
        'peligro' => 'DC3545',       // Rojo
        'gris' => '6C757D'           // Gris
    ];
    
    // Estilos de texto
    public $estilosTexto = [
        'titulo_principal' => [
            'bold' => true,
            'size' => 18,
            'color' => '2E86AB',
            'spaceAfter' => 200,
            'spaceBefore' => 200
        ],
        'titulo_secundario' => [
            'bold' => true,
            'size' => 16,
            'color' => 'A23B72',
            'spaceAfter' => 150,
            'spaceBefore' => 150
        ],
        'titulo_seccion' => [
            'bold' => true,
            'size' => 14,
            'color' => 'F18F01',
            'spaceAfter' => 100,
            'spaceBefore' => 100
        ],
        'subtitulo' => [
            'bold' => true,
            'size' => 12,
            'color' => '2E86AB',
            'spaceAfter' => 80,
            'spaceBefore' => 80
        ],
        'texto_normal' => [
            'size' => 11,
            'spaceAfter' => 60,
            'spaceBefore' => 0
        ],
        'texto_pequeno' => [
            'size' => 10,
            'spaceAfter' => 40,
            'spaceBefore' => 0
        ],
        'campo_formulario' => [
            'size' => 11,
            'spaceAfter' => 40,
            'spaceBefore' => 0,
            'indent' => 0.5
        ]
    ];
    
    // Estilos de tablas
    public $estilosTablas = [
        'principal' => [
            'borderSize' => 2,
            'borderColor' => '2E86AB',
            'cellMargin' => 80,
            'cellSpacing' => 0
        ],
        'secundaria' => [
            'borderSize' => 1,
            'borderColor' => 'A23B72',
            'cellMargin' => 60,
            'cellSpacing' => 0
        ],
        'informacion' => [
            'borderSize' => 1,
            'borderColor' => 'F18F01',
            'cellMargin' => 60,
            'cellSpacing' => 0
        ],
        'administracion' => [
            'borderSize' => 1,
            'borderColor' => '6C757D',
            'cellMargin' => 60,
            'cellSpacing' => 0
        ]
    ];
    
    // Colores de fondo para celdas
    public $coloresFondo = [
        'encabezado_principal' => 'E8F4FD',
        'encabezado_secundario' => 'F0F8FF',
        'encabezado_informacion' => 'FFF8E1',
        'encabezado_administracion' => 'F8F9FA',
        'resaltado' => 'E8F5E8',
        'advertencia' => 'FFF3CD',
        'peligro' => 'F8D7DA'
    ];
    
    // Configuración de márgenes
    public $margenes = [
        'izquierda' => 15,
        'derecha' => 15,
        'arriba' => 15,
        'abajo' => 25,
        'encabezado' => 5,
        'pie' => 10
    ];
    
    // Configuración de página
    public $configuracionPagina = [
        'orientacion' => 'P',           // P = Portrait, L = Landscape
        'unidad' => 'mm',               // mm, cm, in, pt
        'formato' => 'A4',              // A4, A3, Letter, etc.
        'encoding' => 'UTF-8',
        'diskcache' => false
    ];
    
    // Información institucional
    public $institucion = [
        'nombre' => 'Instituto Tecnologico Superior Ibarra',
        'unidad' => 'UNIDAD DE BIENESTAR INSTITUCIONAL',
        'direccion' => 'Ibarra, Ecuador',
        'telefono' => '+593 6 264 0000',
        'email' => 'info@itsi.edu.ec',
        'web' => 'www.itsi.edu.ec',
        'logo' => 'logo_itsi.svg'
    ];
    
    // Configuración de fuentes
    public $fuentes = [
        'principal' => 'Arial',
        'secundaria' => 'Times New Roman',
        'monoespaciada' => 'Courier New'
    ];
    
    // Configuración de imágenes
    public $imagenes = [
        'logo_max_width' => 80,
        'logo_max_height' => 80,
        'imagen_max_width' => 150,
        'imagen_max_height' => 150,
        'calidad' => 90
    ];
    
    // Configuración de seguridad
    public $seguridad = [
        'proteger_pdf' => false,
        'password_usuario' => '',
        'password_administrador' => '',
        'permisos' => [
            'imprimir' => true,
            'copiar' => true,
            'editar' => false,
            'anotar' => false
        ]
    ];
    
    // Configuración de metadatos
    public $metadatos = [
        'creador' => 'Sistema de Bienestar Estudiantil',
        'autor' => 'ITSI - Administrador',
        'asunto' => 'Documento generado automáticamente',
        'palabras_clave' => 'ITSI, Bienestar, Estudiantil, PDF',
        'creador_original' => 'ITSI',
        'producido_por' => 'Sistema de Bienestar Estudiantil'
    ];
}
