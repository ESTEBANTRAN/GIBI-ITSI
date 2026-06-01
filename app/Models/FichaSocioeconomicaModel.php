<?php

namespace App\Models;

use CodeIgniter\Model;

class FichaSocioeconomicaModel extends Model
{
    protected $table = 'fichas_socioeconomicas';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'estudiante_id', 
        'periodo_id', 
        'json_data', 
        'estado', 
        'revisada_por_admin',
        'fecha_revision_admin',
        'observaciones_admin',
        'fecha_creacion', 
        'fecha_envio', 
        'fecha_revision',
        'revisado_por',
        'fecha_actualizacion',
        'actualizado_por',
        'puntaje_calculado',
        'relacionada_beca',
        'fecha_relacion_beca'
    ];
    protected $useTimestamps = false;
    protected $createdField = 'fecha_creacion';
    protected $updatedField = 'fecha_actualizacion';

    public function getFichasConPeriodo($estudiante_id)
    {
        return $this->select('fichas_socioeconomicas.*, periodos_academicos.nombre as nombre_periodo')
                    ->join('periodos_academicos', 'periodos_academicos.id = fichas_socioeconomicas.periodo_id')
                    ->where('fichas_socioeconomicas.estudiante_id', $estudiante_id)
                    ->orderBy('fichas_socioeconomicas.fecha_creacion', 'DESC')
                    ->findAll();
    }

    public function getFichaCompleta($id, $estudiante_id)
    {
        return $this->select('fichas_socioeconomicas.*, periodos_academicos.nombre as nombre_periodo')
                    ->join('periodos_academicos', 'periodos_academicos.id = fichas_socioeconomicas.periodo_id')
                    ->where('fichas_socioeconomicas.id', $id)
                    ->where('fichas_socioeconomicas.estudiante_id', $estudiante_id)
                    ->first();
    }

    public function getFichaCompletaAdmin($id)
    {
        return $this->select('fichas_socioeconomicas.*, usuarios.nombre, usuarios.apellido, usuarios.cedula, usuarios.email, periodos_academicos.nombre as nombre_periodo')
                    ->join('usuarios', 'usuarios.id = fichas_socioeconomicas.estudiante_id')
                    ->join('periodos_academicos', 'periodos_academicos.id = fichas_socioeconomicas.periodo_id')
                    ->where('fichas_socioeconomicas.id', $id)
                    ->first();
    }
} 