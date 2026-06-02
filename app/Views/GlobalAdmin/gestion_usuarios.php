<?= $this->extend('layouts/mainGlobalAdmin') ?>

<?= $this->section('breadcrumb') ?>Gestión de Usuarios<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <div>
                <h4 class="mb-0">Gestión de Usuarios</h4>
                <p class="text-muted mb-0">Administra usuarios, roles y permisos del sistema</p>
            </div>
            <div class="page-title-right">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrearUsuario">
                    <i class="bi bi-person-plus me-2"></i>Nuevo Usuario
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Pagination Info -->
<div class="row mb-3">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted">
                <?php if (isset($search) && !empty($search)): ?>
                    <?php if ($total > 0): ?>
                        Mostrando <?= (($current_page - 1) * $per_page) + 1 ?> a <?= min($current_page * $per_page, $total) ?> de <?= $total ?> usuarios encontrados para "<?= htmlspecialchars($search) ?>"
                    <?php else: ?>
                        No se encontraron usuarios para "<?= htmlspecialchars($search) ?>"
                    <?php endif; ?>
                <?php else: ?>
                    Mostrando <?= (($current_page - 1) * $per_page) + 1 ?> a <?= min($current_page * $per_page, $total) ?> de <?= $total ?> usuarios
                <?php endif; ?>
            </div>
            <div class="text-muted">
                <?php if ($total_pages > 0): ?>
                    Página <?= $current_page ?> de <?= $total_pages ?>
                <?php else: ?>
                    Sin resultados
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if (isset($search) && !empty($search) && $total == 0): ?>
<div class="row mb-3">
    <div class="col-12">
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>No se encontraron resultados</strong> para "<?= htmlspecialchars($search) ?>". 
            <a href="<?= base_url('index.php/global-admin/usuarios') ?>" class="alert-link">Ver todos los usuarios</a>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (isset($search) && !empty($search) && $total > 0 && count($usuarios) == 0): ?>
<div class="row mb-3">
    <div class="col-12">
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            <strong>Búsqueda activa:</strong> Se encontraron <?= $total ?> resultados para "<?= htmlspecialchars($search) ?>" pero no hay resultados en esta página. 
            <a href="<?= base_url('index.php/global-admin/usuarios?search=' . urlencode($search) . '&page=1') ?>" class="alert-link">Ver todos los resultados</a>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (isset($search) && !empty($search) && $total > 0 && count($usuarios) > 0): ?>
<div class="row mb-3">
    <div class="col-12">
        <div class="alert alert-success">
            <i class="bi bi-check-circle me-2"></i>
            <strong>Búsqueda exitosa:</strong> Se encontraron <?= $total ?> resultados para "<?= htmlspecialchars($search) ?>". 
            <a href="<?= base_url('index.php/global-admin/usuarios') ?>" class="alert-link">Limpiar búsqueda</a>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-muted">Total Usuarios</h6>
                        <h3 class="mb-0 text-primary"><?= $total ?></h3>
                    </div>
                    <div class="text-primary">
                        <i class="bi bi-people fs-1"></i>
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
                        <h6 class="card-title text-muted">Usuarios Activos</h6>
                        <h3 class="mb-0 text-success"><?= $total ?></h3>
                    </div>
                    <div class="text-success">
                        <i class="bi bi-person-check fs-1"></i>
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
                        <h6 class="card-title text-muted">Estudiantes</h6>
                        <h3 class="mb-0 text-warning"><?= count(array_filter($usuarios, function($u) { return $u['rol_id'] == 1; })) ?></h3>
                    </div>
                    <div class="text-warning">
                        <i class="bi bi-mortarboard fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-danger">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-muted">Administradores</h6>
                        <h3 class="mb-0 text-danger"><?= count(array_filter($usuarios, function($u) { return in_array($u['rol_id'], [2, 4]); })) ?></h3>
                    </div>
                    <div class="text-danger">
                        <i class="bi bi-shield-check fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Users Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-table me-2"></i>Lista de Usuarios
                </h5>
                <div class="d-flex gap-2">
                    <form class="d-flex" method="GET" action="<?= base_url('index.php/global-admin/usuarios') ?>">
                        <div class="input-group" style="width: 300px;">
                            <input type="text" class="form-control" name="search" id="searchInput" 
                                   placeholder="Buscar usuarios..." 
                                   value="<?= isset($search) ? htmlspecialchars($search) : '' ?>">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </form>
                    <button class="btn btn-outline-secondary" onclick="exportarUsuarios()">
                        <i class="bi bi-download me-2"></i>Exportar PDF
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="usuariosTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Foto</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Cédula</th>
                                <th>Rol</th>
                                <th>Estado</th>
                                <th>Fecha Registro</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td><?= $usuario['id'] ?></td>
                                <td>
                                    <img src="<?= base_url('uploads/perfiles/' . ($usuario['foto_perfil'] ?: 'default.jpg')) ?>" 
                                         alt="Foto" class="rounded-circle" width="40" height="40">
                                </td>
                                <td>
                                    <div class="fw-bold"><?= $usuario['nombre'] . ' ' . $usuario['apellido'] ?></div>
                                    <small class="text-muted"><?= $usuario['carrera'] ?? 'N/A' ?></small>
                                </td>
                                <td><?= $usuario['email'] ?></td>
                                <td><?= $usuario['cedula'] ?></td>
                                <td>
                                    <span class="badge bg-<?= $usuario['rol_id'] == 4 ? 'danger' : ($usuario['rol_id'] == 2 ? 'warning' : 'primary') ?>">
                                        <?= $usuario['nombre_rol'] ?? 'N/A' ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-success">
                                        Activo
                                    </span>
                                </td>
                                <td><?= date('d/m/Y', strtotime($usuario['created_at'])) ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-primary" onclick="editarUsuario(<?= $usuario['id'] ?>)">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-info" onclick="verUsuario(<?= $usuario['id'] ?>)">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="eliminarUsuario(<?= $usuario['id'] ?>)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pagination Controls -->
<?php if ($total_pages > 1): ?>
<div class="row mt-4">
    <div class="col-12">
        <nav aria-label="Navegación de páginas">
            <ul class="pagination justify-content-center">
                <!-- Botón Anterior -->
                <?php if ($current_page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?= base_url('index.php/global-admin/usuarios?page=' . ($current_page - 1) . (isset($search) && !empty($search) ? '&search=' . urlencode($search) : '')) ?>">
                            <i class="bi bi-chevron-left"></i> Anterior
                        </a>
                    </li>
                <?php else: ?>
                    <li class="page-item disabled">
                        <span class="page-link">
                            <i class="bi bi-chevron-left"></i> Anterior
                        </span>
                    </li>
                <?php endif; ?>

                <!-- Números de página -->
                <?php
                $start_page = max(1, $current_page - 2);
                $end_page = min($total_pages, $current_page + 2);
                $search_param = isset($search) && !empty($search) ? '&search=' . urlencode($search) : '';
                
                // Mostrar primera página si no está en el rango
                if ($start_page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?= base_url('index.php/global-admin/usuarios?page=1' . $search_param) ?>">1</a>
                    </li>
                    <?php if ($start_page > 2): ?>
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>

                <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                    <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
                        <a class="page-link" href="<?= base_url('index.php/global-admin/usuarios?page=' . $i . $search_param) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <!-- Mostrar última página si no está en el rango -->
                <?php if ($end_page < $total_pages): ?>
                    <?php if ($end_page < $total_pages - 1): ?>
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    <?php endif; ?>
                    <li class="page-item">
                        <a class="page-link" href="<?= base_url('index.php/global-admin/usuarios?page=' . $total_pages . $search_param) ?>"><?= $total_pages ?></a>
                    </li>
                <?php endif; ?>

                <!-- Botón Siguiente -->
                <?php if ($current_page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?= base_url('index.php/global-admin/usuarios?page=' . ($current_page + 1) . $search_param) ?>">
                            Siguiente <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                <?php else: ?>
                    <li class="page-item disabled">
                        <span class="page-link">
                            Siguiente <i class="bi bi-chevron-right"></i>
                        </span>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</div>
<?php endif; ?>

<!-- Modal Crear Usuario -->
<div class="modal fade" id="modalCrearUsuario" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-person-plus me-2"></i>Crear Nuevo Usuario
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formCrearUsuario">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nombre *</label>
                                <input type="text" class="form-control" name="nombre" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Apellido *</label>
                                <input type="text" class="form-control" name="apellido" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email *</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Cédula *</label>
                                <input type="text" class="form-control" name="cedula" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Teléfono</label>
                                <input type="text" class="form-control" name="telefono">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Rol *</label>
                                <select class="form-select" name="rol_id" required>
                                    <option value="">Seleccionar rol</option>
                                    <option value="1">Estudiante</option>
                                    <option value="2">Administrativo Bienestar</option>
                                    <option value="4">Super Administrador</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Contraseña *</label>
                                <input type="password" class="form-control" name="password" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Confirmar Contraseña *</label>
                                <input type="password" class="form-control" name="password_confirm" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Carrera</label>
                                <input type="text" class="form-control" name="carrera">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Semestre</label>
                                <input type="number" class="form-control" name="semestre" min="1" max="10">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dirección</label>
                        <textarea class="form-control" name="direccion" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>Crear Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Usuario -->
<div class="modal fade" id="modalEditarUsuario" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-pencil me-2"></i>Editar Usuario
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditarUsuario">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body">
                    <!-- Mismo contenido que el modal crear, pero con IDs diferentes -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nombre *</label>
                                <input type="text" class="form-control" name="nombre" id="edit_nombre" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Apellido *</label>
                                <input type="text" class="form-control" name="apellido" id="edit_apellido" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email *</label>
                                <input type="email" class="form-control" name="email" id="edit_email" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Cédula *</label>
                                <input type="text" class="form-control" name="cedula" id="edit_cedula" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Teléfono</label>
                                <input type="text" class="form-control" name="telefono" id="edit_telefono">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Rol *</label>
                                <select class="form-select" name="rol_id" id="edit_rol_id" required>
                                    <option value="">Seleccionar rol</option>
                                    <option value="1">Estudiante</option>
                                    <option value="2">Administrativo Bienestar</option>
                                    <option value="4">Super Administrador</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nueva Contraseña</label>
                                <input type="password" class="form-control" name="password" id="edit_password">
                                <small class="text-muted">Dejar vacío para mantener la actual</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Confirmar Contraseña</label>
                                <input type="password" class="form-control" name="password_confirm" id="edit_password_confirm">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Carrera</label>
                                <input type="text" class="form-control" name="carrera" id="edit_carrera">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Semestre</label>
                                <input type="number" class="form-control" name="semestre" id="edit_semestre" min="1" max="10">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dirección</label>
                        <textarea class="form-control" name="direccion" id="edit_direccion" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>Actualizar Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Search functionality
document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase();
    const table = document.getElementById('usuariosTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    
    // Si el término de búsqueda está vacío, mostrar todos los usuarios de la página actual
    if (searchTerm === '') {
        for (let row of rows) {
            row.style.display = '';
        }
        return;
    }
    
    // Filtrar usuarios en la página actual
    for (let row of rows) {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    }
    
    // Mostrar mensaje si no hay resultados en la página actual
    const visibleRows = Array.from(rows).filter(row => row.style.display !== 'none');
    if (visibleRows.length === 0) {
        // Crear mensaje de "no hay resultados en esta página"
        const tbody = table.getElementsByTagName('tbody')[0];
        const noResultsRow = tbody.querySelector('.no-results-row');
        if (!noResultsRow) {
            const newRow = document.createElement('tr');
            newRow.className = 'no-results-row';
            newRow.innerHTML = `
                <td colspan="9" class="text-center text-muted py-4">
                    <i class="bi bi-search me-2"></i>
                    No se encontraron usuarios en esta página que coincidan con "${searchTerm}"
                    <br>
                    <small>Intenta buscar en otra página o ajusta tu búsqueda</small>
                </td>
            `;
            tbody.appendChild(newRow);
        }
    } else {
        // Remover mensaje de "no hay resultados" si existe
        const noResultsRow = table.querySelector('.no-results-row');
        if (noResultsRow) {
            noResultsRow.remove();
        }
    }
});

// Crear usuario
document.getElementById('formCrearUsuario').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const password = formData.get('password');
    const passwordConfirm = formData.get('password_confirm');
    
    if (password !== passwordConfirm) {
        alert('Las contraseñas no coinciden');
        return;
    }
    
    fetch('<?= base_url('index.php/global-admin/crear-usuario') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Usuario creado exitosamente');
            location.reload();
        } else {
            alert('Error: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al crear usuario');
    });
});

// Actualizar usuario
document.getElementById('formEditarUsuario').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const password = formData.get('password');
    const passwordConfirm = formData.get('password_confirm');
    
    // Validar contraseñas si se proporcionaron
    if (password && password !== passwordConfirm) {
        alert('Las contraseñas no coinciden');
        return;
    }
    
    fetch('<?= base_url('index.php/global-admin/actualizar-usuario') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Usuario actualizado exitosamente');
            location.reload();
        } else {
            alert('Error: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al actualizar usuario');
    });
});

// Editar usuario
function editarUsuario(id) {
    // Obtener datos del usuario
    fetch(`<?= base_url('index.php/global-admin/obtener-usuario') ?>/${id}`)
        .then(response => {
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const usuario = data.usuario;
                
                // Llenar el modal con los datos del usuario
                document.getElementById('edit_id').value = usuario.id;
                document.getElementById('edit_nombre').value = usuario.nombre;
                document.getElementById('edit_apellido').value = usuario.apellido;
                document.getElementById('edit_email').value = usuario.email;
                document.getElementById('edit_cedula').value = usuario.cedula;
                document.getElementById('edit_telefono').value = usuario.telefono || '';
                document.getElementById('edit_rol_id').value = usuario.rol_id;
                document.getElementById('edit_carrera').value = usuario.carrera || '';
                document.getElementById('edit_semestre').value = usuario.semestre || '';
                document.getElementById('edit_direccion').value = usuario.direccion || '';
                
                // Limpiar campos de contraseña
                document.getElementById('edit_password').value = '';
                document.getElementById('edit_password_confirm').value = '';
                
                // Abrir modal
                const modal = new bootstrap.Modal(document.getElementById('modalEditarUsuario'));
                modal.show();
            } else {
                alert('Error: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al obtener datos del usuario');
        });
}

// Ver usuario
function verUsuario(id) {
    // Obtener datos del usuario
    fetch(`<?= base_url('index.php/global-admin/obtener-usuario') ?>/${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const usuario = data.usuario;
                
                // Crear contenido del modal de vista
                let modalContent = `
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-eye me-2"></i>Detalles del Usuario
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4 text-center">
                                <img src="<?= base_url('uploads/perfiles/') ?>${usuario.foto_perfil || 'default.jpg'}" 
                                     alt="Foto de perfil" class="rounded-circle mb-3" width="100" height="100">
                            </div>
                            <div class="col-md-8">
                                <h5>${usuario.nombre} ${usuario.apellido}</h5>
                                <p class="text-muted">${usuario.email}</p>
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Cédula:</strong> ${usuario.cedula}</p>
                                        <p><strong>Teléfono:</strong> ${usuario.telefono || 'No especificado'}</p>
                                        <p><strong>Carrera:</strong> ${usuario.carrera || 'No especificado'}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Rol:</strong> <span class="badge bg-${usuario.rol_id == 4 ? 'danger' : (usuario.rol_id == 2 ? 'warning' : 'primary')}">${usuario.nombre_rol}</span></p>
                                        <p><strong>Semestre:</strong> ${usuario.semestre || 'No especificado'}</p>
                                        <p><strong>Fecha Registro:</strong> ${new Date(usuario.created_at).toLocaleDateString()}</p>
                                    </div>
                                </div>
                                ${usuario.direccion ? `<p><strong>Dirección:</strong> ${usuario.direccion}</p>` : ''}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-primary" onclick="editarUsuario(${usuario.id})">
                            <i class="bi bi-pencil me-2"></i>Editar Usuario
                        </button>
                    </div>
                `;
                
                // Crear modal dinámico
                const modal = document.createElement('div');
                modal.className = 'modal fade';
                modal.id = 'modalVerUsuario';
                modal.innerHTML = `
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            ${modalContent}
                        </div>
                    </div>
                `;
                
                // Agregar al body y mostrar
                document.body.appendChild(modal);
                const bootstrapModal = new bootstrap.Modal(modal);
                bootstrapModal.show();
                
                // Limpiar modal al cerrar
                modal.addEventListener('hidden.bs.modal', function() {
                    document.body.removeChild(modal);
                });
            } else {
                alert('Error: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al obtener datos del usuario');
        });
}

// Eliminar usuario
function eliminarUsuario(id) {
    if (confirm('¿Está seguro de que desea eliminar este usuario? Esta acción no se puede deshacer.')) {
        const formData = new FormData();
        formData.append('id', id);
        
        fetch('<?= base_url('index.php/global-admin/eliminar-usuario') ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Usuario eliminado exitosamente');
                location.reload();
            } else {
                alert('Error: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al eliminar usuario');
        });
    }
}

// Exportar usuarios
function exportarUsuarios() {
    // Redirigir a la exportación PDF
    window.open('<?= base_url('index.php/global-admin/exportar-usuarios-pdf') ?>', '_blank');
}
</script>
<?= $this->endSection() ?> 