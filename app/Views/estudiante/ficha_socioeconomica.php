<?= $this->extend('layouts/mainEstudiante') ?>

<?= $this->section('breadcrumb') ?>Ficha Socioeconómica<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <div>
                <h4 class="mb-0">Ficha Socioeconómica</h4>
                <p class="text-muted mb-0">Gestiona tu información socioeconómica</p>
            </div>
            <div class="page-title-right">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevaFicha">
                    <i class="bi bi-plus-circle me-2"></i>Nueva Ficha
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Fichas Existentes -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-file-earmark-text me-2"></i>Mis Fichas Socioeconómicas
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($fichas)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Período</th>
                                    <th>Estado</th>
                                    <th>Fecha de Creación</th>
                                    <th>Fecha de Envío</th>
                                    <th>Observaciones</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($fichas as $ficha): ?>
                                <tr>
                                    <td>
                                        <strong><?= $ficha['nombre_periodo'] ?? 'Período ' . $ficha['periodo_id'] ?></strong>
                                    </td>
                                    <td>
                                        <?php
                                        $estadoClass = '';
                                        switch ($ficha['estado']) {
                                            case 'Borrador':
                                                $estadoClass = 'bg-secondary';
                                                break;
                                            case 'Enviada':
                                                $estadoClass = 'bg-info';
                                                break;
                                            case 'Revisada':
                                                $estadoClass = 'bg-warning';
                                                break;
                                            case 'Aprobada':
                                                $estadoClass = 'bg-success';
                                                break;
                                            case 'Rechazada':
                                                $estadoClass = 'bg-danger';
                                                break;
                                        }
                                        ?>
                                        <span class="badge <?= $estadoClass ?>"><?= $ficha['estado'] ?></span>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($ficha['fecha_creacion'])) ?></td>
                                    <td>
                                        <?= $ficha['fecha_envio'] ? date('d/m/Y H:i', strtotime($ficha['fecha_envio'])) : 'No enviada' ?>
                                    </td>
                                    <td>
                                        <?= $ficha['observaciones_admin'] ? htmlspecialchars($ficha['observaciones_admin']) : 'Sin observaciones' ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-outline-primary" onclick="verFicha(<?= $ficha['id'] ?>)">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-info" onclick="exportarFichaPDF(<?= $ficha['id'] ?>)">
                                                <i class="bi bi-file-pdf"></i>
                                            </button>
                                            <?php if ($ficha['estado'] == 'Borrador'): ?>
                                            <button class="btn btn-sm btn-outline-warning" onclick="editarFicha(<?= $ficha['id'] ?>)">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <?php endif; ?>
                                            <?php if ($ficha['estado'] == 'Borrador'): ?>
                                            <button class="btn btn-sm btn-outline-success" onclick="enviarFicha(<?= $ficha['id'] ?>)">
                                                <i class="bi bi-send"></i>
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-file-earmark-text display-1 text-muted"></i>
                        <h5 class="mt-3">No tienes fichas socioeconómicas</h5>
                        <p class="text-muted">Crea tu primera ficha socioeconómica para comenzar</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nueva Ficha (formulario completo) -->
<div class="modal fade" id="modalNuevaFicha" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nueva Ficha Socioeconómica</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <?php include APPPATH.'Views/estudiante/partials/ficha_formulario_completo.php'; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ver Ficha -->
<div class="modal fade" id="modalVerFicha" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Vista Previa de Ficha Socioeconómica</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="contenidoFicha">
                <!-- El contenido se cargará dinámicamente -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-info" id="btnExportarPDF">
                    <i class="bi bi-file-pdf me-2"></i>Exportar PDF
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar Ficha -->
<div class="modal fade" id="modalEditarFicha" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Ficha Socioeconómica</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formEditarFicha">
                    <input type="hidden" id="ficha_id_editar" name="ficha_id">
                    
                    <!-- Información básica -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="periodo_id_editar" class="form-label">Período Académico *</label>
                            <select class="form-select" id="periodo_id_editar" name="periodo_id" required>
                                <option value="">Seleccionar período</option>
                                <?php foreach ($periodos as $periodo): ?>
                                <option value="<?= $periodo['id'] ?>"><?= $periodo['nombre'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Información personal -->
                    <div class="card mb-3">
                        <div class="card-header"><h6 class="mb-0">1. INFORMACIÓN PERSONAL</h6></div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="apellidos_nombres_editar" class="form-label">Apellidos y Nombres *</label>
                                    <input type="text" class="form-control" id="apellidos_nombres_editar" name="apellidos_nombres" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="nacionalidad_editar" class="form-label">Nacionalidad *</label>
                                    <input type="text" class="form-control" id="nacionalidad_editar" name="nacionalidad" value="Ecuatoriana" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="cedula_editar" class="form-label">Cédula *</label>
                                    <input type="text" class="form-control" id="cedula_editar" name="cedula" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="lugar_nacimiento_editar" class="form-label">Lugar y Fecha de Nacimiento *</label>
                                    <input type="text" class="form-control" id="lugar_nacimiento_editar" name="lugar_nacimiento" required>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="edad_editar" class="form-label">Edad *</label>
                                    <input type="number" class="form-control" id="edad_editar" name="edad" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Estado Civil *</label>
                                    <div class="d-flex gap-3 flex-wrap">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="estado_civil" id="soltero_editar" value="Soltero/a" required>
                                            <label class="form-check-label" for="soltero_editar">Soltero/a</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="estado_civil" id="casado_editar" value="Casado/a">
                                            <label class="form-check-label" for="casado_editar">Casado/a</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="estado_civil" id="divorciado_editar" value="Divorciado/a">
                                            <label class="form-check-label" for="divorciado_editar">Divorciado/a</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="estado_civil" id="viudo_editar" value="Viudo/a">
                                            <label class="form-check-label" for="viudo_editar">Viudo/a</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="estado_civil" id="union_libre_editar" value="Unión Libre">
                                            <label class="form-check-label" for="union_libre_editar">U. Libre</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="ciudad_editar" class="form-label">Ciudad *</label>
                                    <input type="text" class="form-control" id="ciudad_editar" name="ciudad" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="barrio_editar" class="form-label">Barrio *</label>
                                    <input type="text" class="form-control" id="barrio_editar" name="barrio" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="calle_principal_editar" class="form-label">Calle Principal *</label>
                                    <input type="text" class="form-control" id="calle_principal_editar" name="calle_principal" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="calle_secundaria_editar" class="form-label">Calle Secundaria</label>
                                    <input type="text" class="form-control" id="calle_secundaria_editar" name="calle_secundaria">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Etnia *</label>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="etnia" id="blanco_editar" value="Blanco" required>
                                            <label class="form-check-label" for="blanco_editar">Blanco</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="etnia" id="mestizo_editar" value="Mestizo">
                                            <label class="form-check-label" for="mestizo_editar">Mestizo</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="etnia" id="afro_editar" value="Afro">
                                            <label class="form-check-label" for="afro_editar">Afro</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="etnia" id="mulato_editar" value="Mulato">
                                            <label class="form-check-label" for="mulato_editar">Mulato</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="etnia" id="indigena_editar" value="Indígena">
                                            <label class="form-check-label" for="indigena_editar">Indígena</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="etnia" id="montubio_editar" value="Montubio">
                                            <label class="form-check-label" for="montubio_editar">Montubio</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">¿Trabaja? *</label>
                                    <div class="d-flex gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="trabaja" id="trabaja_si_editar" value="SI" required>
                                            <label class="form-check-label" for="trabaja_si_editar">SI</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="trabaja" id="trabaja_no_editar" value="NO">
                                            <label class="form-check-label" for="trabaja_no_editar">NO</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="telefono_domicilio_editar" class="form-label">Teléfono Domicilio</label>
                                    <input type="text" class="form-control" id="telefono_domicilio_editar" name="telefono_domicilio">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="celular_editar" class="form-label">Celular *</label>
                                    <input type="text" class="form-control" id="celular_editar" name="celular" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="email_editar" class="form-label">E-mail *</label>
                                    <input type="email" class="form-control" id="email_editar" name="email" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Vive con *</label>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="vive_con" id="solo_papa_editar" value="Solo Papa" required>
                                            <label class="form-check-label" for="solo_papa_editar">Solo Papa</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="vive_con" id="solo_mama_editar" value="Solo Mama">
                                            <label class="form-check-label" for="solo_mama_editar">Solo Mama</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="vive_con" id="ambos_padres_editar" value="Ambos Padres">
                                            <label class="form-check-label" for="ambos_padres_editar">Ambos Padres</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="vive_con" id="hermanos_editar" value="Hermanos">
                                            <label class="form-check-label" for="hermanos_editar">Hermanos</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="vive_con" id="abuelos_editar" value="Abuelos">
                                            <label class="form-check-label" for="abuelos_editar">Abuelos</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="vive_con" id="amigos_editar" value="Amigos">
                                            <label class="form-check-label" for="amigos_editar">Amigos</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="vive_con" id="otros_familiares_editar" value="Otros Familiares">
                                            <label class="form-check-label" for="otros_familiares_editar">Otros Familiares</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">¿Sus padres están separados? *</label>
                                    <div class="d-flex gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="padres_separados" id="padres_separados_si_editar" value="SI" required>
                                            <label class="form-check-label" for="padres_separados_si_editar">SI</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="padres_separados" id="padres_separados_no_editar" value="NO">
                                            <label class="form-check-label" for="padres_separados_no_editar">NO</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Datos del grupo familiar -->
                    <div class="card mb-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">4. DATOS DEL GRUPO FAMILIAR</h6>
                            <button type="button" class="btn btn-sm btn-primary" onclick="agregarFilaFamiliaEditar()">
                                <i class="bi bi-plus"></i> Agregar Familiar
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%">#</th>
                                            <th width="18%">Nombre y Apellidos *</th>
                                            <th width="12%">Parentesco *</th>
                                            <th width="10%">Edad *</th>
                                            <th width="12%">Estado Civil *</th>
                                            <th width="15%">Ocupación *</th>
                                            <th width="15%">Institución</th>
                                            <th width="10%">Ingresos</th>
                                            <th width="10%">Observaciones</th>
                                            <th width="10%">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyFamiliaEditar">
                                        <!-- Las filas se agregarán dinámicamente -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- 5. SITUACIÓN ECONÓMICA -->
                    <div class="card mb-3">
                        <div class="card-header"><h6 class="mb-0">5. SITUACIÓN ECONÓMICA</h6></div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="total_ingresos_familiares_editar" class="form-label">Total Ingresos Familiares *</label>
                                    <input type="number" class="form-control" id="total_ingresos_familiares_editar" name="total_ingresos_familiares" step="0.01" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="total_gastos_familiares_editar" class="form-label">Total Gastos Familiares *</label>
                                    <input type="number" class="form-control" id="total_gastos_familiares_editar" name="total_gastos_familiares" step="0.01" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="diferencia_ingresos_egresos_editar" class="form-label">Diferencia Ingresos-Egresos</label>
                                    <input type="number" class="form-control" id="diferencia_ingresos_egresos_editar" name="diferencia_ingresos_egresos" step="0.01" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Servicios Básicos *</label>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="servicios_basicos[]" id="agua_editar" value="Agua">
                                            <label class="form-check-label" for="agua_editar">Agua</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="servicios_basicos[]" id="luz_editar" value="Luz">
                                            <label class="form-check-label" for="luz_editar">Luz</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="servicios_basicos[]" id="telefono_editar" value="Teléfono">
                                            <label class="form-check-label" for="telefono_editar">Teléfono</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="servicios_basicos[]" id="internet_editar" value="Internet">
                                            <label class="form-check-label" for="internet_editar">Internet</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="servicios_basicos[]" id="gas_editar" value="Gas">
                                            <label class="form-check-label" for="gas_editar">Gas</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tipo de Cuentas</label>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="tipo_cuentas[]" id="cuenta_ahorro_editar" value="Cuenta de Ahorro">
                                            <label class="form-check-label" for="cuenta_ahorro_editar">Cuenta de Ahorro</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="tipo_cuentas[]" id="cuenta_corriente_editar" value="Cuenta Corriente">
                                            <label class="form-check-label" for="cuenta_corriente_editar">Cuenta Corriente</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="tipo_cuentas[]" id="tarjeta_credito_editar" value="Tarjeta de Crédito">
                                            <label class="form-check-label" for="tarjeta_credito_editar">Tarjeta de Crédito</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 6. SITUACIÓN DE VIVIENDA -->
                    <div class="card mb-3">
                        <div class="card-header"><h6 class="mb-0">6. SITUACIÓN DE VIVIENDA</h6></div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Tipo de Vivienda *</label>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="tipo_vivienda" id="propia_editar" value="Propia" required>
                                            <label class="form-check-label" for="propia_editar">Propia</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="tipo_vivienda" id="alquilada_editar" value="Alquilada">
                                            <label class="form-check-label" for="alquilada_editar">Alquilada</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="tipo_vivienda" id="prestada_editar" value="Prestada">
                                            <label class="form-check-label" for="prestada_editar">Prestada</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Condición de la Vivienda *</label>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="condicion_vivienda" id="excelente_editar" value="Excelente" required>
                                            <label class="form-check-label" for="excelente_editar">Excelente</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="condicion_vivienda" id="buena_editar" value="Buena">
                                            <label class="form-check-label" for="buena_editar">Buena</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="condicion_vivienda" id="regular_editar" value="Regular">
                                            <label class="form-check-label" for="regular_editar">Regular</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="condicion_vivienda" id="mala_editar" value="Mala">
                                            <label class="form-check-label" for="mala_editar">Mala</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="numero_habitaciones_editar" class="form-label">Número de Habitaciones *</label>
                                    <input type="number" class="form-control" id="numero_habitaciones_editar" name="numero_habitaciones" min="1" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">¿Registra Préstamos? *</label>
                                    <div class="d-flex gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="registra_prestamos" id="registra_prestamos_si_editar" value="SI" required>
                                            <label class="form-check-label" for="registra_prestamos_si_editar">SI</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="registra_prestamos" id="registra_prestamos_no_editar" value="NO">
                                            <label class="form-check-label" for="registra_prestamos_no_editar">NO</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="campos_prestamos_editar" style="display: none;">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="monto_prestamos_editar" class="form-label">Monto de Préstamos</label>
                                        <input type="number" class="form-control" id="monto_prestamos_editar" name="monto_prestamos" step="0.01">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="institucion_prestamista_editar" class="form-label">Institución Prestamista</label>
                                        <input type="text" class="form-control" id="institucion_prestamista_editar" name="institucion_prestamista">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 7. SITUACIÓN DE SALUD -->
                    <div class="card mb-3">
                        <div class="card-header"><h6 class="mb-0">7. SITUACIÓN DE SALUD</h6></div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">¿Hay Enfermedad Grave? *</label>
                                    <div class="d-flex gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="enfermedad_grave" id="enfermedad_grave_si_editar" value="SI" required>
                                            <label class="form-check-label" for="enfermedad_grave_si_editar">SI</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="enfermedad_grave" id="enfermedad_grave_no_editar" value="NO">
                                            <label class="form-check-label" for="enfermedad_grave_no_editar">NO</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">¿Hay Familiar Emigrante? *</label>
                                    <div class="d-flex gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="familiar_emigrante" id="familiar_emigrante_si_editar" value="SI" required>
                                            <label class="form-check-label" for="familiar_emigrante_si_editar">SI</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="familiar_emigrante" id="familiar_emigrante_no_editar" value="NO">
                                            <label class="form-check-label" for="familiar_emigrante_no_editar">NO</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="campos_enfermedad_editar" style="display: none;">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="tipo_enfermedad_editar" class="form-label">Tipo de Enfermedad</label>
                                        <input type="text" class="form-control" id="tipo_enfermedad_editar" name="tipo_enfermedad">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="familiar_afectado_editar" class="form-label">Familiar Afectado</label>
                                        <input type="text" class="form-control" id="familiar_afectado_editar" name="familiar_afectado">
                                    </div>
                                </div>
                            </div>
                            <div id="campos_emigrante_editar" style="display: none;">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">¿Quién Emigró?</label>
                                        <div class="d-flex gap-2 flex-wrap">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="quien_emigrante[]" id="padre_emigrante_editar" value="Padre">
                                                <label class="form-check-label" for="padre_emigrante_editar">Padre</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="quien_emigrante[]" id="madre_emigrante_editar" value="Madre">
                                                <label class="form-check-label" for="madre_emigrante_editar">Madre</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="quien_emigrante[]" id="hermano_emigrante_editar" value="Hermano">
                                                <label class="form-check-label" for="hermano_emigrante_editar">Hermano</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="quien_emigrante[]" id="otro_emigrante_editar" value="Otro">
                                                <label class="form-check-label" for="otro_emigrante_editar">Otro</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="pais_destino_editar" class="form-label">País de Destino</label>
                                        <input type="text" class="form-control" id="pais_destino_editar" name="pais_destino">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 8. COMENTARIOS ADICIONALES -->
                    <div class="card mb-3">
                        <div class="card-header"><h6 class="mb-0">8. COMENTARIOS ADICIONALES</h6></div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="comentarios_estudiante_editar" class="form-label">Comentarios del Estudiante</label>
                                <textarea class="form-control" id="comentarios_estudiante_editar" name="comentarios_estudiante" rows="4" placeholder="Agregue cualquier comentario adicional que considere importante..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Campo oculto para comentarios -->
                    <input type="hidden" name="comentarios_estudiante" value="">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="formEditarFicha" class="btn btn-primary">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Contador para las filas de familia
    let contadorFamilia = 0;

    // Mostrar/ocultar campos de trabajo del estudiante (formulario nuevo)
    $('input[name="trabaja"]').change(function() {
        if ($(this).val() === 'SI') {
            $('#campos_trabajo').show();
            $('#campos_trabajo input, #campos_trabajo select').prop('required', true);
        } else {
            $('#campos_trabajo').hide();
            $('#campos_trabajo input, #campos_trabajo select').prop('required', false);
        }
    });

    // Mostrar/ocultar campos de trabajo del estudiante (formulario editar)
    $('input[name="trabaja"]').change(function() {
        if ($(this).val() === 'SI') {
            $('#campos_trabajo_editar').show();
            $('#campos_trabajo_editar input, #campos_trabajo_editar select').prop('required', true);
        } else {
            $('#campos_trabajo_editar').hide();
            $('#campos_trabajo_editar input, #campos_trabajo_editar select').prop('required', false);
        }
    });

    // Mostrar/ocultar campos de otra carrera
    $('input[name="estudia_otra_carrera"]').change(function() {
        if ($(this).val() === 'SI') {
            $('#campos_otra_carrera').show();
            $('#institucion_otra_carrera').prop('required', true);
        } else {
            $('#campos_otra_carrera').hide();
            $('#institucion_otra_carrera').prop('required', false);
        }
    });

    // Mostrar/ocultar campos de trabajo del dependiente
    $('input[name="trabaja_dependiente"]').change(function() {
        if ($(this).val() === 'SI') {
            $('#campos_trabajo_dependiente').show();
            $('#campos_trabajo_dependiente input, #campos_trabajo_dependiente select').prop('required', true);
        } else {
            $('#campos_trabajo_dependiente').hide();
            $('#campos_trabajo_dependiente input, #campos_trabajo_dependiente select').prop('required', false);
        }
    });

    // Mostrar/ocultar campos de préstamos
    $('input[name="registra_prestamos"]').change(function() {
        if ($(this).val() === 'SI') {
            $('#campos_prestamos').show();
            $('#campos_prestamos input').prop('required', true);
        } else {
            $('#campos_prestamos').hide();
            $('#campos_prestamos input').prop('required', false);
        }
    });

    // Mostrar/ocultar campos de enfermedad
    $('input[name="enfermedad_grave"]').change(function() {
        if ($(this).val() === 'SI') {
            $('#campos_enfermedad').show();
            $('#campos_enfermedad input').prop('required', true);
        } else {
            $('#campos_enfermedad').hide();
            $('#campos_enfermedad input').prop('required', false);
        }
    });

    // Mostrar/ocultar campos de emigrante
    $('input[name="familiar_emigrante"]').change(function() {
        if ($(this).val() === 'SI') {
            $('#campos_emigrante').show();
            $('#campos_emigrante input').prop('required', true);
        } else {
            $('#campos_emigrante').hide();
            $('#campos_emigrante input').prop('required', false);
        }
    });

    // Mostrar/ocultar campos de emigrante (formulario editar)
    $('input[name="familiar_emigrante"]').change(function() {
        if ($(this).val() === 'SI') {
            $('#campos_emigrante_editar').show();
            $('#campos_emigrante_editar input').prop('required', true);
        } else {
            $('#campos_emigrante_editar').hide();
            $('#campos_emigrante_editar input').prop('required', false);
        }
    });

    // Manejar checkboxes de emigrantes (no requerir todos)
    $('input[name="quien_emigrante[]"]').change(function() {
        // No hacer nada especial, permitir selección libre
    });

    // Manejar checkboxes de emigrantes en edición (no requerir todos)
    $('input[name="quien_emigrante[]"]').change(function() {
        // No hacer nada especial, permitir selección libre
    });

    // Calcular diferencia entre ingresos y egresos automáticamente (formulario editar)
    $('#total_ingresos_familiares_editar, #total_gastos_familiares_editar').on('input', function() {
        const ingresos = parseFloat($('#total_ingresos_familiares_editar').val()) || 0;
        const gastos = parseFloat($('#total_gastos_familiares_editar').val()) || 0;
        const diferencia = ingresos - gastos;
        $('#diferencia_ingresos_egresos_editar').val(diferencia.toFixed(2));
    });

    // Envío del formulario de nueva ficha
    $('#formFichaSocioeconomica').on('submit', function(e) {
        e.preventDefault();
        
        // Validar que todos los campos requeridos estén llenos
        if (!this.checkValidity()) {
            e.stopPropagation();
            $(this).addClass('was-validated');
            return;
        }

        // Recopilar datos de la tabla de familia
        const datosFamilia = [];
        $('#tbodyFamilia tr').each(function() {
            const fila = $(this);
            const datos = {
                nombre_apellido: fila.find('input[name="nombre_apellido[]"]').val(),
                parentesco: fila.find('input[name="parentesco[]"]').val(),
                edad: fila.find('input[name="edad[]"]').val(),
                estado_civil: fila.find('select[name="estado_civil[]"]').val(),
                ocupacion: fila.find('input[name="ocupacion[]"]').val(),
                institucion: fila.find('input[name="institucion[]"]').val(),
                ingresos: fila.find('input[name="ingresos[]"]').val(),
                observaciones: fila.find('input[name="observaciones[]"]').val()
            };
            if (datos.nombre_apellido && datos.parentesco) {
                datosFamilia.push(datos);
            }
        });

        // Crear FormData con todos los datos
        var formData = new FormData(this);
        formData.append('datos_familia', JSON.stringify(datosFamilia));

        // Agregar arrays de checkboxes
        const serviciosBasicos = [];
        $('input[name="servicios_basicos[]"]:checked').each(function() {
            serviciosBasicos.push($(this).val());
        });
        formData.append('servicios_basicos', JSON.stringify(serviciosBasicos));

        const tipoCuentas = [];
        $('input[name="tipo_cuentas[]"]:checked').each(function() {
            tipoCuentas.push($(this).val());
        });
        formData.append('tipo_cuentas', JSON.stringify(tipoCuentas));

        const quienEmigrante = [];
        $('input[name="quien_emigrante[]"]:checked').each(function() {
            quienEmigrante.push($(this).val());
        });
        formData.append('quien_emigrante', JSON.stringify(quienEmigrante));
        
        $.ajax({
            url: '<?= base_url('estudiante/crear-ficha') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: response.message
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.error
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al crear la ficha'
                });
            }
        });
    });

    // Envío del formulario de editar ficha
    $('#formEditarFicha').on('submit', function(e) {
        e.preventDefault();
        
        // Validar que todos los campos requeridos estén llenos
        if (!this.checkValidity()) {
            e.stopPropagation();
            $(this).addClass('was-validated');
            return;
        }

        // Recopilar datos de la tabla de familia
        const datosFamilia = [];
        $('#modalEditarFicha #tbodyFamiliaEditar tr').each(function() {
            const fila = $(this);
            const datos = {
                nombre_apellido: fila.find('input[name="nombre_apellido[]"]').val(),
                parentesco: fila.find('input[name="parentesco[]"]').val(),
                edad: fila.find('input[name="edad[]"]').val(),
                estado_civil: fila.find('select[name="estado_civil[]"]').val(),
                ocupacion: fila.find('input[name="ocupacion[]"]').val(),
                institucion: fila.find('input[name="institucion[]"]').val(),
                ingresos: fila.find('input[name="ingresos[]"]').val(),
                observaciones: fila.find('input[name="observaciones[]"]').val()
            };
            if (datos.nombre_apellido && datos.parentesco) {
                datosFamilia.push(datos);
            }
        });

        // Crear FormData con todos los datos
        var formData = new FormData(this);
        formData.append('datos_familia', JSON.stringify(datosFamilia));

        // Agregar arrays de checkboxes
        const serviciosBasicos = [];
        $('input[name="servicios_basicos[]"]:checked').each(function() {
            serviciosBasicos.push($(this).val());
        });
        formData.append('servicios_basicos', JSON.stringify(serviciosBasicos));

        const tipoCuentas = [];
        $('input[name="tipo_cuentas[]"]:checked').each(function() {
            tipoCuentas.push($(this).val());
        });
        formData.append('tipo_cuentas', JSON.stringify(tipoCuentas));

        const quienEmigrante = [];
        $('input[name="quien_emigrante[]"]:checked').each(function() {
            quienEmigrante.push($(this).val());
        });
        formData.append('quien_emigrante', JSON.stringify(quienEmigrante));
        
        $.ajax({
            url: '<?= base_url('estudiante/actualizar-ficha') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: response.message
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.error
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al actualizar la ficha'
                });
            }
        });
    });
});

// Función para agregar fila a la tabla de familia (para formulario nuevo)
function agregarFilaFamilia() {
    const tbody = $('#tbodyFamilia');
    const numero = tbody.find('tr').length + 1;
    
    const nuevaFila = `
        <tr>
            <td>${numero}</td>
            <td><input type="text" class="form-control" name="nombre_apellido[]" required></td>
            <td><input type="text" class="form-control" name="parentesco[]" required></td>
            <td><input type="number" class="form-control" name="edad[]" min="0" max="120" required style="min-width: 60px;"></td>
            <td>
                <select class="form-select" name="estado_civil[]" required>
                    <option value="">Seleccionar</option>
                    <option value="Soltero/a">Soltero/a</option>
                    <option value="Casado/a">Casado/a</option>
                    <option value="Divorciado/a">Divorciado/a</option>
                    <option value="Viudo/a">Viudo/a</option>
                    <option value="Unión Libre">Unión Libre</option>
                </select>
            </td>
            <td><input type="text" class="form-control" name="ocupacion[]" required></td>
            <td><input type="text" class="form-control" name="institucion[]"></td>
            <td><input type="number" class="form-control" name="ingresos[]" step="0.01"></td>
            <td><input type="text" class="form-control" name="observaciones[]"></td>
            <td>
                <button type="button" class="btn btn-danger btn-sm" onclick="eliminarFilaFamilia(this)">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        </tr>
    `;
    
    tbody.append(nuevaFila);
}

// Función para agregar fila a la tabla de familia (para formulario de edición)
function agregarFilaFamiliaEditar() {
    const tbody = $('#modalEditarFicha #tbodyFamilia');
    const numero = tbody.find('tr').length + 1;
    
    const nuevaFila = `
        <tr>
            <td>${numero}</td>
            <td><input type="text" class="form-control" name="nombre_apellido[]" required></td>
            <td><input type="text" class="form-control" name="parentesco[]" required></td>
            <td><input type="number" class="form-control" name="edad[]" min="0" max="120" required style="min-width: 60px;"></td>
            <td>
                <select class="form-select" name="estado_civil[]" required>
                    <option value="">Seleccionar</option>
                    <option value="Soltero/a">Soltero/a</option>
                    <option value="Casado/a">Casado/a</option>
                    <option value="Divorciado/a">Divorciado/a</option>
                    <option value="Viudo/a">Viudo/a</option>
                    <option value="Unión Libre">Unión Libre</option>
                </select>
            </td>
            <td><input type="text" class="form-control" name="ocupacion[]" required></td>
            <td><input type="text" class="form-control" name="institucion[]"></td>
            <td><input type="number" class="form-control" name="ingresos[]" step="0.01"></td>
            <td><input type="text" class="form-control" name="observaciones[]"></td>
            <td>
                <button type="button" class="btn btn-danger btn-sm" onclick="eliminarFilaFamilia(this)">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        </tr>
    `;
    
    tbody.append(nuevaFila);
}

// Función para eliminar fila de la tabla de familia
function eliminarFilaFamilia(button) {
    $(button).closest('tr').remove();
    // Renumerar las filas
    const tbody = $(button).closest('tbody');
    tbody.find('tr').each(function(index) {
        $(this).find('td:first').text(index + 1);
    });
}

// Función para ver ficha
function verFicha(id) {
    
    // Mostrar loading
    Swal.fire({
        title: 'Cargando ficha...',
        text: 'Por favor espera',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    $.ajax({
        url: '<?= base_url('estudiante/ver-ficha/') ?>' + id,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            Swal.close();
            if (response.success) {
                $('#contenidoFicha').html(response.html);
                $('#modalVerFicha').modal('show');
                
                // Agregar evento al botón de exportar PDF
                $('#btnExportarPDF').off('click').on('click', function() {
                    exportarFichaPDF(id);
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.error || 'Error al cargar la ficha'
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.close();
            console.error('Error AJAX:', error); // Debug
            console.error('Status:', status); // Debug
            console.error('Response:', xhr.responseText); // Debug
            
            // Si la respuesta es HTML (PDF), mostrar error específico
            if (xhr.responseText && xhr.responseText.includes('%PDF')) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de formato',
                    text: 'La respuesta no es válida. Contacta al administrador.'
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo cargar la ficha. Inténtalo de nuevo.'
                });
            }
        }
    });
}

// Función para editar ficha
function editarFicha(id) {
    
    // Mostrar loading
    Swal.fire({
        title: 'Cargando ficha...',
        text: 'Por favor espera',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    $.ajax({
        url: '<?= base_url('estudiante/editar-ficha/') ?>' + id,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            Swal.close();
            if (response.success) {
                // Cargar datos en el formulario
                cargarDatosEnFormulario(response.datos);
                $('#ficha_id_editar').val(id);
                $('#modalEditarFicha').modal('show');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.error || 'Error al cargar la ficha para editar'
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.close();
            console.error('Error AJAX editar:', error); // Debug
            Swal.fire({
                icon: 'error',
                title: 'Error de conexión',
                text: 'No se pudo cargar la ficha para editar. Inténtalo de nuevo.'
            });
        }
    });
}

// Función para cargar datos en el formulario de edición
function cargarDatosEnFormulario(datos) {
    
    // Limpiar formulario
    $('#formEditarFicha')[0].reset();
    
    // Cargar campos básicos
    Object.keys(datos).forEach(key => {
        const elemento = $(`#formEditarFicha [name="${key}"]`);
        if (elemento.length > 0) {
            if (elemento.attr('type') === 'radio') {
                $(`#formEditarFicha [name="${key}"][value="${datos[key]}"]`).prop('checked', true);
            } else if (elemento.attr('type') === 'checkbox') {
                // Manejar checkboxes
                if (Array.isArray(datos[key])) {
                    datos[key].forEach(valor => {
                        $(`#formEditarFicha [name="${key}"][value="${valor}"]`).prop('checked', true);
                    });
                }
            } else {
                elemento.val(datos[key]);
            }
        }
    });
    
    // Cargar arrays especiales
    if (datos.datos_familia && Array.isArray(datos.datos_familia)) {
        $('#tbodyFamiliaEditar').empty();
        datos.datos_familia.forEach(familiar => {
            agregarFilaFamiliaConDatos(familiar);
        });
    }
    
    // Trigger change events para mostrar campos condicionales
    $('#formEditarFicha input[type="radio"]:checked').trigger('change');
    $('#formEditarFicha input[type="checkbox"]:checked').trigger('change');
}

// Función para agregar fila de familia con datos
function agregarFilaFamiliaConDatos(datos) {
    const tbody = $('#modalEditarFicha #tbodyFamiliaEditar');
    const numero = tbody.find('tr').length + 1;
    
    const nuevaFila = `
        <tr>
            <td>${numero}</td>
            <td><input type="text" class="form-control" name="nombre_apellido[]" value="${datos.nombre_apellido || ''}" required></td>
            <td><input type="text" class="form-control" name="parentesco[]" value="${datos.parentesco || ''}" required></td>
            <td><input type="number" class="form-control" name="edad[]" value="${datos.edad || ''}" min="0" max="120" required></td>
            <td>
                <select class="form-select" name="estado_civil[]" required>
                    <option value="">Seleccionar</option>
                    <option value="Soltero/a" ${datos.estado_civil === 'Soltero/a' ? 'selected' : ''}>Soltero/a</option>
                    <option value="Casado/a" ${datos.estado_civil === 'Casado/a' ? 'selected' : ''}>Casado/a</option>
                    <option value="Divorciado/a" ${datos.estado_civil === 'Divorciado/a' ? 'selected' : ''}>Divorciado/a</option>
                    <option value="Viudo/a" ${datos.estado_civil === 'Viudo/a' ? 'selected' : ''}>Viudo/a</option>
                    <option value="Unión Libre" ${datos.estado_civil === 'Unión Libre' ? 'selected' : ''}>Unión Libre</option>
                </select>
            </td>
            <td><input type="text" class="form-control" name="ocupacion[]" value="${datos.ocupacion || ''}" required></td>
            <td><input type="text" class="form-control" name="institucion[]" value="${datos.institucion || ''}"></td>
            <td><input type="number" class="form-control" name="ingresos[]" value="${datos.ingresos || ''}" step="0.01"></td>
            <td><input type="text" class="form-control" name="observaciones[]" value="${datos.observaciones || ''}"></td>
            <td>
                <button type="button" class="btn btn-danger btn-sm" onclick="eliminarFilaFamilia(this)">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        </tr>
    `;
    
    tbody.append(nuevaFila);
}

// Función para enviar ficha con confirmación
function enviarFicha(id) {
    
    Swal.fire({
        title: '¿Estás seguro de enviar la ficha?',
        html: `
            <div class="text-start">
                <p><strong>⚠️ Importante:</strong></p>
                <ul class="text-start">
                    <li>Una vez enviada, <strong>NO podrás modificar</strong> la ficha</li>
                    <li>La ficha será revisada por el administrador</li>
                    <li>Recibirás notificación cuando sea evaluada</li>
                </ul>
                <p class="text-warning"><strong>¿Deseas continuar con el envío?</strong></p>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#dc3545',
        confirmButtonText: 'Sí, enviar ficha',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar loading
            Swal.fire({
                title: 'Enviando ficha...',
                text: 'Por favor espera',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            $.ajax({
                url: '<?= base_url('estudiante/enviar-ficha') ?>',
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Ficha enviada exitosamente!',
                            text: response.message,
                            confirmButtonText: 'Entendido'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error al enviar',
                            text: response.error || 'No se pudo enviar la ficha'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error AJAX envío:', error); // Debug
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de conexión',
                        text: 'No se pudo enviar la ficha. Inténtalo de nuevo.'
                    });
                }
            });
        }
    });
}

// Event listener para el formulario de nueva ficha
$(document).ready(function() {
    $('#formFichaSocioeconomica').on('submit', function(e) {
        e.preventDefault();
        crearFicha();
    });
});

// Función para crear nueva ficha
function crearFicha() {
    
    // Obtener el formulario
    const form = document.getElementById('formFichaSocioeconomica');
    if (!form) {
        console.error('Formulario no encontrado');
        return;
    }
    
    // Validar que se haya seleccionado un período
    const periodoSelect = document.getElementById('periodo_id');
    if (!periodoSelect.value) {
        Swal.fire({
            icon: 'error',
            title: 'Período requerido',
            text: 'Debes seleccionar un período académico'
        });
        return;
    }
    
    // Mostrar loading
    Swal.fire({
        title: 'Creando ficha...',
        text: 'Por favor espera',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Recopilar datos del formulario
    const formData = new FormData(form);
    
    // Enviar petición AJAX
    $.ajax({
        url: '<?= base_url('estudiante/crear-ficha') ?>',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Ficha creada exitosamente!',
                    text: response.message,
                    confirmButtonText: 'Entendido'
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error al crear ficha',
                    text: response.error || 'No se pudo crear la ficha'
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('Error AJAX creación:', error); // Debug
            Swal.fire({
                icon: 'error',
                title: 'Error de conexión',
                text: 'No se pudo crear la ficha. Inténtalo de nuevo.'
            });
        }
    });
}

// Función para exportar PDF
function exportarFichaPDF(id) {
    
    // Mostrar loading
    Swal.fire({
        title: 'Generando PDF...',
        text: 'Por favor espera',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Crear un enlace temporal para la descarga
    const url = '<?= base_url('estudiante/exportar-ficha-pdf/') ?>' + id;
    const link = document.createElement('a');
    link.href = url;
    link.download = 'Ficha_Socioeconomica.pdf';
    link.target = '_blank';
    
    // Simular clic en el enlace
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    // Cerrar loading después de un momento
    setTimeout(() => {
        Swal.close();
        Swal.fire({
            icon: 'success',
            title: 'PDF generado',
            text: 'El archivo PDF se está descargando',
            timer: 2000,
            showConfirmButton: false
        });
    }, 1000);
}
</script>
<?= $this->endSection() ?> 