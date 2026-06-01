<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoriaSolicitudAyudaModel extends Model
{
    protected $table            = 'categorias_solicitud_ayuda';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    
    protected $allowedFields = [
        'nombre', 'descripcion', 'color', 'icono', 'activo', 'orden'
    ];
    
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    
    /**
     * Obtener todas las categorías activas ordenadas
     */
    public function getCategoriasActivas()
    {
        return $this->where('activo', true)
                   ->orderBy('orden', 'ASC')
                   ->findAll();
    }
    
    /**
     * Verificar si una categoría es "Otro Asunto"
     */
    public function esOtroAsunto($categoriaId)
    {
        $categoria = $this->find($categoriaId);
        return $categoria && $categoria['nombre'] === 'Otro Asunto';
    }
}
