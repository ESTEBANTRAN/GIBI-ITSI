<form id="formFichaSocioeconomica">
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="periodo_id" class="form-label">Período Académico *</label>
            <select class="form-select" id="periodo_id" name="periodo_id" required>
                <option value="">Seleccionar período</option>
                <?php foreach ($periodos as $periodo): ?>
                <option value="<?= $periodo['id'] ?>"><?= $periodo['nombre'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <!-- 1. INFORMACIÓN PERSONAL DEL/LA ESTUDIANTE -->
    <div class="card mb-3">
        <div class="card-header"><h6 class="mb-0">1. INFORMACIÓN PERSONAL DEL/LA ESTUDIANTE</h6></div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="apellidos_nombres" class="form-label">Apellidos y Nombres Completos *</label>
                    <input type="text" class="form-control" id="apellidos_nombres" name="apellidos_nombres" value="<?= $estudiante['nombre'] . ' ' . $estudiante['apellido'] ?>" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="nacionalidad" class="form-label">Nacionalidad *</label>
                    <input type="text" class="form-control" id="nacionalidad" name="nacionalidad" value="Ecuatoriana" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="cedula" class="form-label">No. Cédula de Identidad *</label>
                    <input type="text" class="form-control" id="cedula" name="cedula" value="<?= $estudiante['cedula'] ?>" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="lugar_nacimiento" class="form-label">Lugar y Fecha de Nacimiento *</label>
                    <input type="text" class="form-control" id="lugar_nacimiento" name="lugar_nacimiento" required>
                </div>
                <div class="col-md-2 mb-3">
                    <label for="edad" class="form-label">Edad *</label>
                    <input type="number" class="form-control" id="edad" name="edad" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Estado Civil *</label>
                    <div class="d-flex gap-3 flex-wrap">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="estado_civil" id="soltero" value="Soltero/a" required>
                            <label class="form-check-label" for="soltero">Soltero/a</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="estado_civil" id="casado" value="Casado/a">
                            <label class="form-check-label" for="casado">Casado/a</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="estado_civil" id="divorciado" value="Divorciado/a">
                            <label class="form-check-label" for="divorciado">Divorciado/a</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="estado_civil" id="viudo" value="Viudo/a">
                            <label class="form-check-label" for="viudo">Viudo/a</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="estado_civil" id="union_libre" value="Unión Libre">
                            <label class="form-check-label" for="union_libre">U. Libre</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="ciudad" class="form-label">Ciudad *</label>
                    <input type="text" class="form-control" id="ciudad" name="ciudad" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="barrio" class="form-label">Barrio *</label>
                    <input type="text" class="form-control" id="barrio" name="barrio" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="calle_principal" class="form-label">Calle Principal *</label>
                    <input type="text" class="form-control" id="calle_principal" name="calle_principal" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="calle_secundaria" class="form-label">Calle Secundaria</label>
                    <input type="text" class="form-control" id="calle_secundaria" name="calle_secundaria">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Etnia *</label>
                    <div class="d-flex gap-2 flex-wrap">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="etnia" id="blanco" value="Blanco" required>
                            <label class="form-check-label" for="blanco">Blanco</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="etnia" id="mestizo" value="Mestizo">
                            <label class="form-check-label" for="mestizo">Mestizo</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="etnia" id="afro" value="Afro">
                            <label class="form-check-label" for="afro">Afro</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="etnia" id="mulato" value="Mulato">
                            <label class="form-check-label" for="mulato">Mulato</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="etnia" id="indigena" value="Indígena">
                            <label class="form-check-label" for="indigena">Indígena</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="etnia" id="montubio" value="Montubio">
                            <label class="form-check-label" for="montubio">Montubio</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">¿Trabaja? *</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="trabaja" id="trabaja_si" value="SI" required>
                            <label class="form-check-label" for="trabaja_si">SI</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="trabaja" id="trabaja_no" value="NO">
                            <label class="form-check-label" for="trabaja_no">NO</label>
                        </div>
                    </div>
                </div>
            </div>
            <div id="campos_trabajo" style="display: none;">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="lugar_trabajo" class="form-label">Lugar de Trabajo *</label>
                        <input type="text" class="form-control" id="lugar_trabajo" name="lugar_trabajo">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="sueldo_mensual" class="form-label">Sueldo Mensual *</label>
                        <input type="number" class="form-control" id="sueldo_mensual" name="sueldo_mensual" step="0.01">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="tiempo_servicios" class="form-label">Tiempo de Servicios *</label>
                        <input type="text" class="form-control" id="tiempo_servicios" name="tiempo_servicios">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="cargo" class="form-label">Cargo que Desempeña *</label>
                        <input type="text" class="form-control" id="cargo" name="cargo">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">¿Está afiliado al IESS? *</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="afiliado_iess" id="iess_si" value="SI">
                                <label class="form-check-label" for="iess_si">SI</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="afiliado_iess" id="iess_no" value="NO">
                                <label class="form-check-label" for="iess_no">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">¿Mantiene un seguro privado adicional? *</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="seguro_privado" id="seguro_si" value="SI">
                                <label class="form-check-label" for="seguro_si">SI</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="seguro_privado" id="seguro_no" value="NO">
                                <label class="form-check-label" for="seguro_no">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="telefono_domicilio" class="form-label">Teléfono Domicilio</label>
                    <input type="text" class="form-control" id="telefono_domicilio" name="telefono_domicilio">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="telefono_trabajo" class="form-label">Teléfono Trabajo</label>
                    <input type="text" class="form-control" id="telefono_trabajo" name="telefono_trabajo">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="celular" class="form-label">Celular *</label>
                    <input type="text" class="form-control" id="celular" name="celular" value="<?= $estudiante['telefono'] ?>" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="email" class="form-label">E-mail *</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= $estudiante['email'] ?>" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Vive con *</label>
                    <div class="d-flex gap-2 flex-wrap">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="vive_con" id="solo_papa" value="Solo Papa" required>
                            <label class="form-check-label" for="solo_papa">Solo Papa</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="vive_con" id="solo_mama" value="Solo Mama">
                            <label class="form-check-label" for="solo_mama">Solo Mama</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="vive_con" id="ambos_padres" value="Ambos Padres">
                            <label class="form-check-label" for="ambos_padres">Ambos Padres</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="vive_con" id="hermanos" value="Hermanos">
                            <label class="form-check-label" for="hermanos">Hermanos</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="vive_con" id="abuelos" value="Abuelos">
                            <label class="form-check-label" for="abuelos">Abuelos</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="vive_con" id="amigos" value="Amigos">
                            <label class="form-check-label" for="amigos">Amigos</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="vive_con" id="otros_familiares" value="Otros Familiares">
                            <label class="form-check-label" for="otros_familiares">Otros Familiares</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">¿Sus padres están separados? *</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="padres_separados" id="separados_si" value="SI" required>
                            <label class="form-check-label" for="separados_si">SI</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="padres_separados" id="separados_no" value="NO">
                            <label class="form-check-label" for="separados_no">NO</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. DATOS ACADÉMICOS -->
    <div class="card mb-3">
        <div class="card-header"><h6 class="mb-0">2. DATOS ACADÉMICOS</h6></div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="colegio_graduacion" class="form-label">Colegio en que se graduó *</label>
                    <input type="text" class="form-control" id="colegio_graduacion" name="colegio_graduacion" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="ciudad_colegio" class="form-label">Ciudad *</label>
                    <input type="text" class="form-control" id="ciudad_colegio" name="ciudad_colegio" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="provincia_colegio" class="form-label">Provincia *</label>
                    <input type="text" class="form-control" id="provincia_colegio" name="provincia_colegio" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">El colegio es *</label>
                    <div class="d-flex gap-3 flex-wrap">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tipo_colegio" id="fiscal" value="Fiscal" required>
                            <label class="form-check-label" for="fiscal">Fiscal</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tipo_colegio" id="fiscomisional" value="Fiscomisional">
                            <label class="form-check-label" for="fiscomisional">Fiscomisional</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tipo_colegio" id="particular" value="Particular">
                            <label class="form-check-label" for="particular">Particular</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tipo_colegio" id="municipal" value="Municipal">
                            <label class="form-check-label" for="municipal">Municipal</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="anio_grado" class="form-label">Año de Grado *</label>
                    <input type="number" class="form-control" id="anio_grado" name="anio_grado" min="1990" max="2030" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="carrera" class="form-label">Carrera *</label>
                    <select class="form-select" id="carrera" name="carrera" required>
                        <option value="">Seleccionar carrera</option>
                        <?php foreach ($carreras as $c): ?>
                            <option value="<?= esc($c['nombre']) ?>" <?= (isset($estudiante['carrera']) && $estudiante['carrera'] == $c['nombre']) ? 'selected' : '' ?>>
                                <?= esc($c['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="nivel_ingresa" class="form-label">Nivel al que ingresa *</label>
                    <input type="text" class="form-control" id="nivel_ingresa" name="nivel_ingresa" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="modalidad" class="form-label">Modalidad *</label>
                    <input type="text" class="form-control" id="modalidad" name="modalidad" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">¿Estudia otra carrera? *</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="estudia_otra_carrera" id="otra_carrera_si" value="SI" required>
                            <label class="form-check-label" for="otra_carrera_si">SI</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="estudia_otra_carrera" id="otra_carrera_no" value="NO">
                            <label class="form-check-label" for="otra_carrera_no">NO</label>
                        </div>
                    </div>
                </div>
            </div>
            <div id="campos_otra_carrera" style="display: none;">
                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="institucion_otra_carrera" class="form-label">Institución y especialidad *</label>
                        <input type="text" class="form-control" id="institucion_otra_carrera" name="institucion_otra_carrera">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">El pago de valores en el ITSI lo realiza *</label>
                    <div class="d-flex gap-3 flex-wrap">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="forma_pago" id="contado" value="Al Contado" required>
                            <label class="form-check-label" for="contado">Al Contado</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="forma_pago" id="credito_iece" value="Crédito IECE">
                            <label class="form-check-label" for="credito_iece">Crédito IECE</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="forma_pago" id="tarjeta_credito" value="Tarjeta de Crédito">
                            <label class="form-check-label" for="tarjeta_credito">Tarjeta de Crédito</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="forma_pago" id="prestamo_bancario" value="Préstamo Bancario">
                            <label class="form-check-label" for="prestamo_bancario">Préstamo Bancario</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="forma_pago" id="beca" value="Beca">
                            <label class="form-check-label" for="beca">Beca</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="forma_pago" id="otros" value="Otros">
                            <label class="form-check-label" for="otros">Otros</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="especificar_pago" class="form-label">Especificar (si es otros)</label>
                    <input type="text" class="form-control" id="especificar_pago" name="especificar_pago">
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">¿Toma inglés en el ITSI? *</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="toma_ingles" id="ingles_si" value="SI" required>
                            <label class="form-check-label" for="ingles_si">SI</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="toma_ingles" id="ingles_no" value="NO">
                            <label class="form-check-label" for="ingles_no">NO</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="modalidad_ingles" class="form-label">Modalidad</label>
                    <input type="text" class="form-control" id="modalidad_ingles" name="modalidad_ingles">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="nivel_ingles" class="form-label">Nivel que cursa</label>
                    <input type="text" class="form-control" id="nivel_ingles" name="nivel_ingles">
                </div>
            </div>
        </div>
    </div>

    <!-- 3. DATOS DE QUIEN DEPENDE EL ESTUDIANTE -->
    <div class="card mb-3">
        <div class="card-header"><h6 class="mb-0">3. DATOS DE QUIEN DEPENDE EL ESTUDIANTE</h6></div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="apellidos_nombres_dependiente" class="form-label">Apellidos y Nombres Completos *</label>
                    <input type="text" class="form-control" id="apellidos_nombres_dependiente" name="apellidos_nombres_dependiente" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="cedula_dependiente" class="form-label">No. Cédula de Identidad *</label>
                    <input type="text" class="form-control" id="cedula_dependiente" name="cedula_dependiente" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="parentesco" class="form-label">Parentesco con el estudiante *</label>
                    <input type="text" class="form-control" id="parentesco" name="parentesco" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="ciudad_dependiente" class="form-label">Ciudad *</label>
                    <input type="text" class="form-control" id="ciudad_dependiente" name="ciudad_dependiente" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="barrio_dependiente" class="form-label">Barrio *</label>
                    <input type="text" class="form-control" id="barrio_dependiente" name="barrio_dependiente" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="parroquia_dependiente" class="form-label">Parroquia</label>
                    <input type="text" class="form-control" id="parroquia_dependiente" name="parroquia_dependiente">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="calle_principal_dependiente" class="form-label">Calle Principal *</label>
                    <input type="text" class="form-control" id="calle_principal_dependiente" name="calle_principal_dependiente" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="calle_secundaria_dependiente" class="form-label">Calle Secundaria</label>
                    <input type="text" class="form-control" id="calle_secundaria_dependiente" name="calle_secundaria_dependiente">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="profesion_dependiente" class="form-label">Profesión *</label>
                    <input type="text" class="form-control" id="profesion_dependiente" name="profesion_dependiente" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">¿Trabaja? *</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="trabaja_dependiente" id="trabaja_dep_si" value="SI" required>
                            <label class="form-check-label" for="trabaja_dep_si">SI</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="trabaja_dependiente" id="trabaja_dep_no" value="NO">
                            <label class="form-check-label" for="trabaja_dep_no">NO</label>
                        </div>
                    </div>
                </div>
            </div>
            <div id="campos_trabajo_dependiente" style="display: none;">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="lugar_trabajo_dependiente" class="form-label">Lugar de Trabajo *</label>
                        <input type="text" class="form-control" id="lugar_trabajo_dependiente" name="lugar_trabajo_dependiente">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="direccion_trabajo_dependiente" class="form-label">Dirección *</label>
                        <input type="text" class="form-control" id="direccion_trabajo_dependiente" name="direccion_trabajo_dependiente">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="tiempo_servicios_dependiente" class="form-label">Tiempo de Servicios *</label>
                        <input type="text" class="form-control" id="tiempo_servicios_dependiente" name="tiempo_servicios_dependiente">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="sueldo_mensual_dependiente" class="form-label">Sueldo Mensual *</label>
                        <input type="number" class="form-control" id="sueldo_mensual_dependiente" name="sueldo_mensual_dependiente" step="0.01">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">¿Posee seguro social? *</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="seguro_social_dependiente" id="seguro_social_si" value="SI">
                                <label class="form-check-label" for="seguro_social_si">SI</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="seguro_social_dependiente" id="seguro_social_no" value="NO">
                                <label class="form-check-label" for="seguro_social_no">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">¿Mantiene un seguro privado adicional? *</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="seguro_privado_dependiente" id="seguro_privado_dep_si" value="SI">
                                <label class="form-check-label" for="seguro_privado_dep_si">SI</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="seguro_privado_dependiente" id="seguro_privado_dep_no" value="NO">
                                <label class="form-check-label" for="seguro_privado_dep_no">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="telefono_domicilio_dependiente" class="form-label">Teléfono Domicilio</label>
                    <input type="text" class="form-control" id="telefono_domicilio_dependiente" name="telefono_domicilio_dependiente">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="telefono_trabajo_dependiente" class="form-label">Teléfono Trabajo</label>
                    <input type="text" class="form-control" id="telefono_trabajo_dependiente" name="telefono_trabajo_dependiente">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="celular_dependiente" class="form-label">Celular</label>
                    <input type="text" class="form-control" id="celular_dependiente" name="celular_dependiente">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="email_dependiente" class="form-label">E-mail</label>
                    <input type="email" class="form-control" id="email_dependiente" name="email_dependiente">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="lugar_negocio_propio" class="form-label">Lugar de trabajo (si tiene negocio propio)</label>
                    <input type="text" class="form-control" id="lugar_negocio_propio" name="lugar_negocio_propio">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="actividad_dependiente" class="form-label">Actividad a que se dedica</label>
                    <input type="text" class="form-control" id="actividad_dependiente" name="actividad_dependiente">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="ingreso_estimado_dependiente" class="form-label">Ingreso estimado mensual</label>
                    <input type="number" class="form-control" id="ingreso_estimado_dependiente" name="ingreso_estimado_dependiente" step="0.01">
                </div>
            </div>
        </div>
    </div>

    <!-- 4. DATOS DEL GRUPO FAMILIAR -->
    <div class="card mb-3">
        <div class="card-header"><h6 class="mb-0">4. DATOS DEL GRUPO FAMILIAR</h6></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="tablaFamilia">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nombre y apellido</th>
                            <th>Parentesco con el/la estudiante</th>
                            <th>Edad</th>
                            <th>Estado civil</th>
                            <th>Ocupación/actividad</th>
                            <th>Institución donde estudia/trabaja</th>
                            <th>Ingresos (solo si trabajan)</th>
                            <th>Observaciones</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyFamilia">
                        <!-- Las filas se agregarán dinámicamente -->
                    </tbody>
                </table>
            </div>
            <button type="button" class="btn btn-success" onclick="agregarFilaFamilia()">
                <i class="bi bi-plus-circle me-2"></i>Agregar Familiar
            </button>
        </div>
    </div>

    <!-- 5. INGRESOS Y EGRESOS FAMILIARES -->
    <div class="card mb-3">
        <div class="card-header"><h6 class="mb-0">5. INGRESOS Y EGRESOS FAMILIARES</h6></div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="total_ingresos_familiares" class="form-label">Total Ingresos Familiares *</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" class="form-control" id="total_ingresos_familiares" name="total_ingresos_familiares" step="0.01" required>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="total_gastos_familiares" class="form-label">Total Gastos Familiares por Mes *</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" class="form-control" id="total_gastos_familiares" name="total_gastos_familiares" step="0.01" required>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="diferencia_ingresos_egresos" class="form-label">Diferencia entre Ingresos y Egresos *</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" class="form-control" id="diferencia_ingresos_egresos" name="diferencia_ingresos_egresos" step="0.01" required>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 6. DATOS DE LA VIVIENDA -->
    <div class="card mb-3">
        <div class="card-header"><h6 class="mb-0">6. DATOS DE LA VIVIENDA</h6></div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">El/la estudiante vive en casa *</label>
                    <div class="d-flex gap-3 flex-wrap">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tipo_vivienda" id="propia" value="Propia" required>
                            <label class="form-check-label" for="propia">Propia</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tipo_vivienda" id="arrendada" value="Arrendada">
                            <label class="form-check-label" for="arrendada">Arrendada</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tipo_vivienda" id="cedida" value="Cedida">
                            <label class="form-check-label" for="cedida">Cedida</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tipo_vivienda" id="compartida" value="Compartida">
                            <label class="form-check-label" for="compartida">Compartida</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="costo_arriendo" class="form-label">Costo del arriendo mensual (si es arrendada)</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" class="form-control" id="costo_arriendo" name="costo_arriendo" step="0.01">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="especificar_cedida_compartida" class="form-label">Especificar quién cede o con quiénes comparte</label>
                    <input type="text" class="form-control" id="especificar_cedida_compartida" name="especificar_cedida_compartida">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tipo de vivienda *</label>
                    <div class="d-flex gap-3 flex-wrap">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tipo_construccion" id="casa" value="Casa" required>
                            <label class="form-check-label" for="casa">Casa</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tipo_construccion" id="departamento" value="Departamento">
                            <label class="form-check-label" for="departamento">Departamento</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tipo_construccion" id="cuarto" value="Cuarto">
                            <label class="form-check-label" for="cuarto">Cuarto</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tipo_construccion" id="otro" value="Otro">
                            <label class="form-check-label" for="otro">Otro</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="especificar_tipo_vivienda" class="form-label">Especificar tipo de vivienda</label>
                    <input type="text" class="form-control" id="especificar_tipo_vivienda" name="especificar_tipo_vivienda">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tipo de construcción *</label>
                    <div class="d-flex gap-3 flex-wrap">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="material_construccion" id="hormigon" value="Hormigón" required>
                            <label class="form-check-label" for="hormigon">Hormigón</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="material_construccion" id="ladrillo" value="Ladrillo">
                            <label class="form-check-label" for="ladrillo">Ladrillo</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="material_construccion" id="madera" value="Madera">
                            <label class="form-check-label" for="madera">Madera</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="material_construccion" id="adobe" value="Adobe">
                            <label class="form-check-label" for="adobe">Adobe</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="material_construccion" id="bloque" value="Bloque">
                            <label class="form-check-label" for="bloque">Bloque</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="material_construccion" id="mixta" value="Mixta">
                            <label class="form-check-label" for="mixta">Mixta</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 mb-3">
                    <label class="form-label">Cuenta con servicios básicos como *</label>
                    <div class="d-flex gap-3 flex-wrap">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="servicios_basicos[]" id="agua_potable" value="Agua Potable">
                            <label class="form-check-label" for="agua_potable">Agua Potable</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="servicios_basicos[]" id="alcantarillado" value="Alcantarillado">
                            <label class="form-check-label" for="alcantarillado">Alcantarillado</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="servicios_basicos[]" id="luz_electrica" value="Luz Eléctrica">
                            <label class="form-check-label" for="luz_electrica">Luz Eléctrica</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="servicios_basicos[]" id="telefono" value="Teléfono">
                            <label class="form-check-label" for="telefono">Teléfono</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="servicios_basicos[]" id="internet" value="Internet">
                            <label class="form-check-label" for="internet">Internet</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="servicios_basicos[]" id="tv_cable" value="TV Cable">
                            <label class="form-check-label" for="tv_cable">TV Cable</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 7. OTROS DATOS ECONÓMICOS FAMILIARES -->
    <div class="card mb-3">
        <div class="card-header"><h6 class="mb-0">7. OTROS DATOS ECONÓMICOS FAMILIARES</h6></div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">¿La familia dispone de vehículo propio? *</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="vehiculo_propio" id="vehiculo_si" value="SI" required>
                            <label class="form-check-label" for="vehiculo_si">SI</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="vehiculo_propio" id="vehiculo_no" value="NO">
                            <label class="form-check-label" for="vehiculo_no">NO</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="marca_ano_vehiculo" class="form-label">Marca y año del vehículo</label>
                    <input type="text" class="form-control" id="marca_ano_vehiculo" name="marca_ano_vehiculo">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">El vehículo es de uso *</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="uso_vehiculo" id="uso_familiar" value="Familiar">
                            <label class="form-check-label" for="uso_familiar">Familiar</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="uso_vehiculo" id="uso_herramienta" value="Herramienta de Trabajo">
                            <label class="form-check-label" for="uso_herramienta">Herramienta de Trabajo</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="especificar_uso_vehiculo" class="form-label">Especificar su uso</label>
                    <input type="text" class="form-control" id="especificar_uso_vehiculo" name="especificar_uso_vehiculo">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">¿La familia posee otras propiedades como terrenos, casas? *</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="otras_propiedades" id="propiedades_si" value="SI" required>
                            <label class="form-check-label" for="propiedades_si">SI</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="otras_propiedades" id="propiedades_no" value="NO">
                            <label class="form-check-label" for="propiedades_no">NO</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="especificar_propiedades" class="form-label">Especificar propiedades y uso</label>
                    <input type="text" class="form-control" id="especificar_propiedades" name="especificar_propiedades">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">La familia posee cuentas de *</label>
                    <div class="d-flex gap-3 flex-wrap">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="tipo_cuentas[]" id="ahorro" value="Ahorro">
                            <label class="form-check-label" for="ahorro">Ahorro</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="tipo_cuentas[]" id="corriente" value="Corriente">
                            <label class="form-check-label" for="corriente">Corriente</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="tipo_cuentas[]" id="tarjetas_credito" value="Tarjetas de Crédito">
                            <label class="form-check-label" for="tarjetas_credito">Tarjetas de Crédito</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">¿La familia registra préstamos? *</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="registra_prestamos" id="prestamos_si" value="SI" required>
                            <label class="form-check-label" for="prestamos_si">SI</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="registra_prestamos" id="prestamos_no" value="NO">
                            <label class="form-check-label" for="prestamos_no">NO</label>
                        </div>
                    </div>
                </div>
            </div>
            <div id="campos_prestamos" style="display: none;">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="valor_deuda_actual" class="form-label">Valor actual de la deuda *</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" id="valor_deuda_actual" name="valor_deuda_actual" step="0.01">
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="valor_pago_mensual_deuda" class="form-label">Valor que paga al mes por la deuda *</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" id="valor_pago_mensual_deuda" name="valor_pago_mensual_deuda" step="0.01">
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="motivo_deuda" class="form-label">Motivo de la deuda y entidad financiera *</label>
                        <input type="text" class="form-control" id="motivo_deuda" name="motivo_deuda">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 8. ANTECEDENTES DE SALUD EN LA FAMILIA -->
    <div class="card mb-3">
        <div class="card-header"><h6 class="mb-0">8. ANTECEDENTES DE SALUD EN LA FAMILIA</h6></div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">¿El/la estudiante o un miembro de su familia sufre alguna enfermedad grave? *</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="enfermedad_grave" id="enfermedad_si" value="SI" required>
                            <label class="form-check-label" for="enfermedad_si">SI</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="enfermedad_grave" id="enfermedad_no" value="NO">
                            <label class="form-check-label" for="enfermedad_no">NO</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="especificar_enfermedad" class="form-label">Especificar quién y de qué tipo</label>
                    <input type="text" class="form-control" id="especificar_enfermedad" name="especificar_enfermedad">
                </div>
            </div>
            <div id="campos_enfermedad" style="display: none;">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">¿Se encuentra en tratamiento? *</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="en_tratamiento" id="tratamiento_si" value="SI">
                                <label class="form-check-label" for="tratamiento_si">SI</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="en_tratamiento" id="tratamiento_no" value="NO">
                                <label class="form-check-label" for="tratamiento_no">NO</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="anio_inicio_tratamiento" class="form-label">Año que inició tratamiento</label>
                        <input type="number" class="form-control" id="anio_inicio_tratamiento" name="anio_inicio_tratamiento" min="1990" max="2030">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">¿El/la estudiante o un miembro de su familia sufre alguna discapacidad? *</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="discapacidad" id="discapacidad_si" value="SI" required>
                            <label class="form-check-label" for="discapacidad_si">SI</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="discapacidad" id="discapacidad_no" value="NO">
                            <label class="form-check-label" for="discapacidad_no">NO</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="especificar_discapacidad" class="form-label">Especificar quién y la discapacidad</label>
                    <input type="text" class="form-control" id="especificar_discapacidad" name="especificar_discapacidad">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">¿Posee carnet del CONADIS? *</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="carnet_conadis" id="conadis_si" value="SI" required>
                            <label class="form-check-label" for="conadis_si">SI</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="carnet_conadis" id="conadis_no" value="NO">
                            <label class="form-check-label" for="conadis_no">NO</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="porcentaje_discapacidad" class="form-label">Porcentaje de discapacidad</label>
                    <input type="number" class="form-control" id="porcentaje_discapacidad" name="porcentaje_discapacidad" min="0" max="100">
                </div>
            </div>
        </div>
    </div>

    <!-- 9. OTROS ANTECEDENTES FAMILIARES -->
    <div class="card mb-3">
        <div class="card-header"><h6 class="mb-0">9. OTROS ANTECEDENTES FAMILIARES</h6></div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">¿El/la estudiante tiene algún familiar cercano emigrante? *</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="familiar_emigrante" id="emigrante_si" value="SI" required>
                            <label class="form-check-label" for="emigrante_si">SI</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="familiar_emigrante" id="emigrante_no" value="NO">
                            <label class="form-check-label" for="emigrante_no">NO</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Si es positivo, señale quién/quienes</label>
                    <div class="d-flex gap-3 flex-wrap">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="quien_emigrante[]" id="padre_emigrante" value="Padre">
                            <label class="form-check-label" for="padre_emigrante">Padre</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="quien_emigrante[]" id="madre_emigrante" value="Madre">
                            <label class="form-check-label" for="madre_emigrante">Madre</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="quien_emigrante[]" id="ambos_padres_emigrante" value="Ambos Padres">
                            <label class="form-check-label" for="ambos_padres_emigrante">Ambos Padres</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="quien_emigrante[]" id="hermanos_emigrante" value="Hermanos">
                            <label class="form-check-label" for="hermanos_emigrante">Hermanos</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="quien_emigrante[]" id="otros_emigrante" value="Otros">
                            <label class="form-check-label" for="otros_emigrante">Otros</label>
                        </div>
                    </div>
                </div>
            </div>
            <div id="campos_emigrante" style="display: none;">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="especificar_otros_emigrante" class="form-label">Especificar otros familiares emigrantes</label>
                        <input type="text" class="form-control" id="especificar_otros_emigrante" name="especificar_otros_emigrante">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="lugar_emigrante" class="form-label">Lugar donde se encuentran</label>
                        <input type="text" class="form-control" id="lugar_emigrante" name="lugar_emigrante">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="tiempo_permanencia_emigrante" class="form-label">Tiempo de permanencia en años</label>
                        <input type="number" class="form-control" id="tiempo_permanencia_emigrante" name="tiempo_permanencia_emigrante" min="0">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 10. ESPACIO PARA EL/LA ESTUDIANTE -->
    <div class="card mb-3">
        <div class="card-header"><h6 class="mb-0">10. ESPACIO PARA EL/LA ESTUDIANTE</h6></div>
        <div class="card-body">
            <div class="row">
                <div class="col-12 mb-3">
                    <label for="comentarios_estudiante" class="form-label">Comentarios o situaciones adicionales</label>
                    <textarea class="form-control" id="comentarios_estudiante" name="comentarios_estudiante" rows="4" placeholder="Escribe aquí cualquier comentario o situación que no se haya considerado en la ficha..."></textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Botones de acción -->
    <div class="d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-2"></i>Guardar Ficha
        </button>
    </div>
</form> 