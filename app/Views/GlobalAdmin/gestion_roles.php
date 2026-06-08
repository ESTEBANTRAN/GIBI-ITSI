<?= $this->extend('layouts/mainGlobalAdmin') ?>

<?= $this->section('breadcrumb') ?>Gestión de Roles<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <div>
                <h4 class="mb-0">Gestión de Roles</h4>
                <p class="text-muted mb-0">Administra roles y permisos del sistema</p>
            </div>
            <div class="page-title-right">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrearRol">
                    <i class="bi bi-shield-plus me-2"></i>Nuevo Rol
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-muted">Total Roles</h6>
                        <h3 class="mb-0 text-primary"><?= $estadisticas['total_roles'] ?? 0 ?></h3>
                    </div>
                    <div class="text-primary">
                        <i class="bi bi-shield fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-muted">Roles Activos</h6>
                        <h3 class="mb-0 text-success"><?= $estadisticas['roles_activos'] ?? 0 ?></h3>
                    </div>
                    <div class="text-success">
                        <i class="bi bi-shield-check fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-muted">Total Usuarios</h6>
                        <h3 class="mb-0 text-warning"><?= $estadisticas['total_usuarios'] ?? 0 ?></h3>
                    </div>
                    <div class="text-warning">
                        <i class="bi bi-people fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-muted">Promedio por Rol</h6>
                        <h3 class="mb-0 text-info"><?= $estadisticas['total_roles'] > 0 ? round($estadisticas['total_usuarios'] / $estadisticas['total_roles'], 1) : 0 ?></h3>
                    </div>
                    <div class="text-info">
                        <i class="bi bi-graph-up fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search and Info -->
<?php if (isset($search) && !empty($search)): ?>
<div class="row mb-3">
    <div class="col-12">
        <div class="alert alert-info">
            <i class="bi bi-search me-2"></i>
            <strong>Búsqueda activa:</strong> Mostrando resultados para "<?= htmlspecialchars($search) ?>"
            <a href="<?= base_url('global-admin/roles') ?>" class="alert-link">Limpiar búsqueda</a>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Roles Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-table me-2"></i>Roles del Sistema
                </h5>
                <div class="d-flex gap-2">
                    <div class="input-group" style="width: 300px;">
                        <input type="text" class="form-control" id="searchInput" placeholder="Buscar roles..." value="<?= isset($search) ? htmlspecialchars($search) : '' ?>">
                        <button class="btn btn-outline-secondary" type="button" onclick="buscarRoles()">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="rolesTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre del Rol</th>
                                <th>Usuarios Asignados</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($roles)): ?>
                                <?php foreach ($roles as $rol): ?>
                                <tr>
                                    <td><?= $rol['id'] ?></td>
                                    <td>
                                        <div class="fw-bold"><?= htmlspecialchars($rol['nombre']) ?></div>
                                        <small class="text-muted">ID: <?= $rol['id'] ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info"><?= $rol['usuarios_count'] ?? 0 ?> usuarios</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">Activo</span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-outline-primary" onclick="editarRol(<?= $rol['id'] ?>)">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-info" onclick="verPermisos(<?= $rol['id'] ?>)">
                                                <i class="bi bi-shield-check"></i>
                                            </button>
                                            <?php if ($rol['id'] > 4): // No permitir eliminar roles del sistema ?>
                                            <button class="btn btn-sm btn-outline-danger" onclick="eliminarRol(<?= $rol['id'] ?>)" <?= ($rol['usuarios_count'] > 0) ? 'disabled' : '' ?>>
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="bi bi-shield-x fs-1 mb-3"></i>
                                        <p>No hay roles configurados</p>
                                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrearRol">
                                            <i class="bi bi-shield-plus me-2"></i>Crear Primer Rol
                                        </button>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Crear Rol -->
<div class="modal fade" id="modalCrearRol" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-shield-plus me-2"></i>Crear Nuevo Rol
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formCrearRol">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre del Rol *</label>
                        <input type="text" class="form-control" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="descripcion" rows="3" placeholder="Describe las funciones de este rol"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>Crear Rol
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Rol -->
<div class="modal fade" id="modalEditarRol" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-pencil me-2"></i>Editar Rol
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditarRol">
                <input type="hidden" name="id" id="edit_rol_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre del Rol *</label>
                        <input type="text" class="form-control" name="nombre" id="edit_nombre" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="descripcion" id="edit_descripcion" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>Actualizar Rol
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Ver Permisos -->
<div class="modal fade" id="modalVerPermisos" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-shield-check me-2"></i>Permisos del Rol
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="permisosContent">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Buscar roles
function buscarRoles() {
    const searchTerm = document.getElementById('searchInput').value;
    if (searchTerm.trim()) {
        window.location.href = '<?= base_url('global-admin/roles') ?>?search=' + encodeURIComponent(searchTerm);
    } else {
        window.location.href = '<?= base_url('global-admin/roles') ?>';
    }
}

// Crear rol
document.getElementById('formCrearRol').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('<?= base_url('global-admin/crear-rol') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Rol creado exitosamente');
            location.reload();
        } else {
            alert('Error: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al crear rol');
    });
});

// Editar rol
function editarRol(id) {
    fetch('<?= base_url('global-admin/obtener-rol') ?>/' + id)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const rol = data.rol;
                document.getElementById('edit_rol_id').value = rol.id;
                                 document.getElementById('edit_nombre').value = rol.nombre;
                document.getElementById('edit_descripcion').value = rol.descripcion || '';
                
                const modal = new bootstrap.Modal(document.getElementById('modalEditarRol'));
                modal.show();
            } else {
                alert('Error: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar datos del rol');
        });
}

// Actualizar rol
document.getElementById('formEditarRol').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('<?= base_url('global-admin/actualizar-rol') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Rol actualizado exitosamente');
            location.reload();
        } else {
            alert('Error: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al actualizar rol');
    });
});

// Ver permisos
function verPermisos(id) {
    const modal = new bootstrap.Modal(document.getElementById('modalVerPermisos'));
    document.getElementById('permisosContent').innerHTML = `
        <div class="text-center">
            <i class="bi bi-hourglass-split fs-1 text-muted mb-3"></i>
            <p>Cargando permisos del rol...</p>
        </div>
    `;
    modal.show();
    
    fetch('<?= base_url('global-admin/permisos-rol') ?>/' + id)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const rol = data.rol;
                const permisos = data.permisos;
                
                                 let permisosHTML = `
                     <h6>Permisos del Rol: ${rol.nombre}</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Módulos Principales</h6>
                            <ul class="list-unstyled">
                                <li><i class="bi bi-${permisos.dashboard ? 'check-circle text-success' : 'x-circle text-danger'} me-2"></i>Dashboard</li>
                                <li><i class="bi bi-${permisos.usuarios ? 'check-circle text-success' : 'x-circle text-danger'} me-2"></i>Gestión de Usuarios</li>
                                <li><i class="bi bi-${permisos.roles ? 'check-circle text-success' : 'x-circle text-danger'} me-2"></i>Gestión de Roles</li>
                                <li><i class="bi bi-${permisos.configuracion ? 'check-circle text-success' : 'x-circle text-danger'} me-2"></i>Configuración del Sistema</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Módulos de Bienestar</h6>
                            <ul class="list-unstyled">
                                <li><i class="bi bi-${permisos.fichas ? 'check-circle text-success' : 'x-circle text-danger'} me-2"></i>Formularios Socioeconómicos</li>
                                <li><i class="bi bi-${permisos.becas ? 'check-circle text-success' : 'x-circle text-danger'} me-2"></i>Gestión de Becas</li>
                                <li><i class="bi bi-${permisos.solicitudes ? 'check-circle text-success' : 'x-circle text-danger'} me-2"></i>Solicitudes de Ayuda</li>
                                <li><i class="bi bi-${permisos.reportes ? 'check-circle text-success' : 'x-circle text-danger'} me-2"></i>Reportes</li>
                            </ul>
                        </div>
                    </div>
                    <div class="mt-3">
                        <h6>Resumen</h6>
                        <div class="bg-light p-3 rounded">
                            <p class="mb-1"><strong>Total de permisos:</strong> ${Object.values(permisos).filter(p => p).length} de ${Object.keys(permisos).length}</p>
                            <p class="mb-1"><strong>Nivel de acceso:</strong> ${Object.values(permisos).filter(p => p).length > 4 ? 'Alto' : Object.values(permisos).filter(p => p).length > 2 ? 'Medio' : 'Bajo'}</p>
                            <p class="mb-0"><strong>Última modificación:</strong> ${new Date().toLocaleDateString()}</p>
                        </div>
                    </div>
                `;
                
                document.getElementById('permisosContent').innerHTML = permisosHTML;
            } else {
                document.getElementById('permisosContent').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Error: ${data.error}
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('permisosContent').innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Error al cargar los permisos
                </div>
            `;
        });
}

// Eliminar rol
function eliminarRol(id) {
    if (confirm('¿Está seguro de que desea eliminar este rol?')) {
        const formData = new FormData();
        formData.append('id', id);
        
        fetch('<?= base_url('global-admin/eliminar-rol') ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Rol eliminado exitosamente');
                location.reload();
            } else {
                alert('Error: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al eliminar rol');
        });
    }
}
</script>
<?= $this->endSection() ?> 