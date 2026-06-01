<?php

namespace App\Models;

use CodeIgniter\Model;

class SolicitudAyudaModel extends Model
{
    protected $table = 'solicitudes_ayuda';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id_estudiante', 
        'asunto', 
        'categoria_id', 
        'asunto_personalizado', 
        'descripcion', 
        'prioridad', 
        'estado', 
        'fecha_solicitud', 
        'fecha_respuesta'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'fecha_solicitud';
    protected $updatedField = 'fecha_actualizacion';
}
