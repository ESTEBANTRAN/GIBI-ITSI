
$(document).ready(function() {
    cargarRespaldos();
    cargarEstadisticas();
});

function cargarRespaldos() {
    $.ajax({
        url: ''URL'',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                mostrarRespaldos(response.respaldos);
            } else {
                console.error('Error al cargar respaldos:', response.error);
            }
        },
        error: function() {
            console.error('Error de conexión al cargar respaldos');
        }
    });
}

function mostrarRespaldos(respaldos) {
    const tbody = $('#tbodyRespaldos');
    tbody.empty();
    
    if (respaldos.length === 0) {
        tbody.append(`
            <tr>
                <td colspan="6" class="text-center text-muted">
                    <i class="bi bi-database me-2"></i>No hay respaldos disponibles
                </td>
            </tr>
        `);
        return;
    }
    
    respaldos.forEach(respaldo => {
        const row = `
            <tr>
                <td>${respaldo.nombre}</td>
                <td>${respaldo.fecha}</td>
                <td>${formatearBytes(respaldo.tamaño)}</td>
                <td><span class="badge bg-primary">${respaldo.tipo}</span></td>
                <td><span class="badge bg-success">${respaldo.estado}</span></td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-primary" onclick="descargarRespaldo(${respaldo.id})" title="Descargar">
                            <i class="bi bi-download"></i>
                        </button>
                        <button type="button" class="btn btn-outline-info" onclick="mostrarModalEnvioEmail(${respaldo.id}, '${respaldo.nombre}')" title="Enviar por Email">
                            <i class="bi bi-envelope"></i>
                        </button>
                        <button type="button" class="btn btn-outline-success" onclick="restaurarRespaldo(${respaldo.id})" title="Restaurar">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                        <button type="button" class="btn btn-outline-danger" onclick="eliminarRespaldo(${respaldo.id})" title="Eliminar">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        tbody.append(row);
    });
}

function cargarEstadisticas() {
    $.ajax({
        url: ''URL'',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                $('#totalRespaldos').text(response.estadisticas.total);
                $('#ultimoRespaldo').text(response.estadisticas.ultimo);
                $('#tamañoTotal').text(formatearBytes(response.estadisticas.tamaño_total));
                $('#estadoRespaldo').text(response.estadisticas.estado);
            }
        },
        error: function() {
            console.error('Error al cargar estadísticas');
        }
    });
}

function crearRespaldo() {
    Swal.fire({
        title: 'Crear Respaldo',
        text: '¿Estás seguro de que quieres crear un nuevo respaldo de la base de datos?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, crear respaldo',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar loading
            Swal.fire({
                title: 'Creando Respaldo',
                text: 'Por favor espera mientras se crea el respaldo de la base de datos...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: ''URL'',
                type: 'POST',
                success: function(response) {
                                         if (response.success) {
                         // Mostrar opciones después de crear el respaldo
                         Swal.fire({
                             title: '¡Respaldo Creado!',
                             text: response.message,
                             icon: 'success',
                             showCancelButton: true,
                             showDenyButton: true,
                             confirmButtonText: 'Descargar Copia',
                             denyButtonText: 'Enviar por Email',
                             cancelButtonText: 'Solo Guardar'
                         }).then((result) => {
                             if (result.isConfirmed) {
                                 // Descargar copia adicional
                                 descargarArchivoConDialogo(response.download_url, response.filename);
                             } else if (result.isDenied) {
                                 // Enviar por email
                                 mostrarModalEnvioEmail(response.respaldo_id, response.filename);
                             }
                         });
                        cargarRespaldos();
                        cargarEstadisticas();
                    } else {
                        Swal.fire(
                            'Error',
                            response.error || 'Error al crear el respaldo.',
                            'error'
                        );
                    }
                },
                error: function() {
                    Swal.fire(
                        'Error de Conexión',
                        'No se pudo conectar con el servidor.',
                        'error'
                    );
                }
            });
        }
    });
}

function restaurarRespaldo(id = null) {
    if (!id) {
        Swal.fire('Error', 'Selecciona un respaldo para restaurar.', 'error');
        return;
    }
    
    Swal.fire({
        title: 'Restaurar Respaldo',
        text: '¿Estás seguro de que quieres restaurar este respaldo? Esta acción sobrescribirá los datos actuales.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, restaurar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: ''URL'',
                type: 'POST',
                data: { id: id },
                success: function(response) {
                    if (response.success) {
                        Swal.fire(
                            '¡Restaurado!',
                            'El respaldo se ha restaurado exitosamente.',
                            'success'
                        );
                    } else {
                        Swal.fire(
                            'Error',
                            response.error || 'Error al restaurar el respaldo.',
                            'error'
                        );
                    }
                },
                error: function() {
                    Swal.fire(
                        'Error de Conexión',
                        'No se pudo conectar con el servidor.',
                        'error'
                    );
                }
            });
        }
    });
}

function descargarRespaldo(id = null) {
    if (id) {
        Swal.fire({
            title: 'Descargar Respaldo',
            text: '¿Cómo deseas obtener el respaldo?',
            icon: 'question',
            showCancelButton: true,
            showDenyButton: true,
            confirmButtonText: 'Descargar',
            denyButtonText: 'Enviar por Email',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                descargarArchivoConDialogo(''URL'/' + id);
            } else if (result.isDenied) {
                mostrarModalEnvioEmail(id);
            }
        });
    } else {
        Swal.fire('Error', 'Selecciona un respaldo para descargar.', 'error');
    }
}

function eliminarRespaldo(id) {
    Swal.fire({
        title: 'Eliminar Respaldo',
        text: '¿Estás seguro de que quieres eliminar este respaldo? Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: ''URL'',
                type: 'POST',
                data: { id: id },
                success: function(response) {
                    if (response.success) {
                        Swal.fire(
                            '¡Eliminado!',
                            'El respaldo se ha eliminado exitosamente.',
                            'success'
                        );
                        cargarRespaldos();
                        cargarEstadisticas();
                    } else {
                        Swal.fire(
                            'Error',
                            response.error || 'Error al eliminar el respaldo.',
                            'error'
                        );
                    }
                },
                error: function() {
                    Swal.fire(
                        'Error de Conexión',
                        'No se pudo conectar con el servidor.',
                        'error'
                    );
                }
            });
        }
    });
}

function configurarRespaldos() {
    $('#configuracionModal').modal('show');
}

function guardarConfiguracion() {
    const formData = new FormData($('#configuracionRespaldosForm')[0]);
    
    $.ajax({
        url: ''URL'',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                Swal.fire(
                    '¡Configuración Guardada!',
                    'La configuración de respaldos se ha actualizado.',
                    'success'
                );
                $('#configuracionModal').modal('hide');
            } else {
                Swal.fire(
                    'Error',
                    response.error || 'Error al guardar la configuración.',
                    'error'
                );
            }
        },
        error: function() {
            Swal.fire(
                'Error de Conexión',
                'No se pudo conectar con el servidor.',
                'error'
            );
        }
    });
}

function limpiarRespaldos() {
    Swal.fire({
        title: 'Limpiar Respaldos Antiguos',
        text: '¿Estás seguro de que quieres eliminar los respaldos antiguos?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, limpiar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: ''URL'',
                type: 'POST',
                success: function(response) {
                    if (response.success) {
                        Swal.fire(
                            '¡Limpieza Completada!',
                            'Los respaldos antiguos han sido eliminados.',
                            'success'
                        );
                        cargarRespaldos();
                        cargarEstadisticas();
                    } else {
                        Swal.fire(
                            'Error',
                            response.error || 'Error al limpiar respaldos.',
                            'error'
                        );
                    }
                },
                error: function() {
                    Swal.fire(
                        'Error de Conexión',
                        'No se pudo conectar con el servidor.',
                        'error'
                    );
                }
            });
        }
    });
}

function actualizarLista() {
    cargarRespaldos();
    cargarEstadisticas();
}

function formatearBytes(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function descargarArchivoConDialogo(url, filename = '') {
    // Mostrar mensaje informativo
    Swal.fire({
        title: 'Descargando Respaldo',
        text: 'Se abrirá el diálogo para guardar el archivo. Elige la ubicación donde quieres guardar la copia.',
        icon: 'info',
        showConfirmButton: true,
        confirmButtonText: 'Entendido'
    }).then(() => {
        // Método 1: Usar fetch para descargar con más control
        fetch(url)
            .then(response => {
                if (response.ok) {
                    return response.blob();
                }
                throw new Error('Error en la descarga');
            })
            .then(blob => {
                // Crear URL del blob
                const blobUrl = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = blobUrl;
                link.download = filename || 'respaldo.sql';
                link.style.display = 'none';
                
                // Agregar al DOM, hacer clic y remover
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                // Limpiar URL del blob
                setTimeout(() => {
                    window.URL.revokeObjectURL(blobUrl);
                }, 100);
                
                // Mostrar confirmación
                Swal.fire({
                    title: '¡Descarga Iniciada!',
                    text: 'El archivo se está descargando. Revisa tu carpeta de descargas.',
                    icon: 'success',
                    timer: 3000,
                    showConfirmButton: false
                });
            })
            .catch(error => {
                console.error('Error al descargar:', error);
                
                // Método 2: Fallback con elemento <a> directo
                const link = document.createElement('a');
                link.href = url;
                link.download = filename || 'respaldo.sql';
                link.target = '_blank';
                link.style.display = 'none';
                
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                Swal.fire({
                    title: 'Descarga Iniciada',
                    text: 'Se abrió una nueva ventana para la descarga.',
                    icon: 'info',
                    timer: 3000,
                    showConfirmButton: false
                });
            });
    });
}

function verRespaldosServidor() {
    Swal.fire({
        title: 'Respaldos en el Servidor',
        html: `
            <div class="text-start">
                <p><strong>Ubicación:</strong> <code>writable/backups/</code></p>
                <p><strong>Estado:</strong> Todos los respaldos se guardan automáticamente en el servidor.</p>
                <p><strong>Ventajas:</strong></p>
                <ul class="text-start">
                    <li>✓ Seguridad: Respaldos protegidos en el servidor</li>
                    <li>✓ Accesibilidad: Disponibles desde cualquier lugar</li>
                    <li>✓ Historial: Mantiene todos los respaldos creados</li>
                    <li>✓ Restauración: Puedes restaurar desde el servidor</li>
                </ul>
                <p class="mt-3"><strong>Nota:</strong> Los respaldos se pueden descargar individualmente usando los botones de la tabla.</p>
            </div>
        `,
        icon: 'info',
        confirmButtonText: 'Entendido',
        width: '600px'
    });
}

function mostrarModalEnvioEmail(respaldoId, filename = '') {
    Swal.fire({
        title: 'Enviar Respaldo por Email',
        html: `
            <div class="form-group">
                <label for="emailDestino" class="form-label">Correo Electrónico:</label>
                <input type="email" id="emailDestino" class="form-control" placeholder="ejemplo@correo.com" required>
            </div>
            ${filename ? '<p class="mt-2"><strong>Archivo:</strong> ' + filename + '</p>' : ''}
        `,
        showCancelButton: true,
        confirmButtonText: 'Enviar',
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
            const email = document.getElementById('emailDestino').value;
            if (!email) {
                Swal.showValidationMessage('Por favor ingresa un correo electrónico válido');
                return false;
            }
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                Swal.showValidationMessage('Por favor ingresa un correo electrónico válido');
                return false;
            }
            return email;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const email = result.value;
            
            // Mostrar loading
            Swal.fire({
                title: 'Enviando Email',
                text: 'Por favor espera mientras se envía el respaldo...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            $.ajax({
                url: ''URL'',
                type: 'POST',
                data: {
                    respaldo_id: respaldoId,
                    email: email
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire(
                            '¡Email Enviado!',
                            response.message,
                            'success'
                        );
                    } else {
                        Swal.fire(
                            'Error',
                            response.error || 'Error al enviar el email.',
                            'error'
                        );
                    }
                },
                error: function() {
                    Swal.fire(
                        'Error de Conexión',
                        'No se pudo conectar con el servidor.',
                        'error'
                    );
                }
            });
        }
    });
}
