<?php

namespace App\Helpers;

/**
 * Helper de Integración con Google Drive API v3 del Sistema GIBI-ITSI
 */
class GoogleDriveHelper
{
    /**
     * Sube un archivo local a Google Drive (mirroring).
     * 
     * @param string $localPath Ruta absoluta o relativa del archivo en el servidor local
     * @param string $filename Nombre que tendrá el archivo en Google Drive
     * @param string $mimeType Tipo MIME del archivo
     * @return string|bool Retorna el ID del archivo en Google Drive si se subió con éxito, o False en caso de error
     */
    public static function subirArchivo(string $localPath, string $filename, string $mimeType, string $subfolderName = '')
    {
        try {
            $db = \Config\Database::connect();
            
            // Verificar si la integración de Drive está activa
            $driveActivo = self::getValorDb($db, 'drive_activo', '0');
            if ($driveActivo !== '1') {
                log_message('debug', 'GoogleDriveHelper::subirArchivo - Integración de Google Drive inactiva.');
                return false;
            }

            // Obtener credenciales de la base de datos
            $clientId = self::getValorDb($db, 'drive_client_id', '');
            $clientSecret = self::getValorDb($db, 'drive_client_secret', '');
            $refreshToken = self::getValorDb($db, 'drive_refresh_token', '');
            $folderId = self::getValorDb($db, 'drive_folder_id', ''); // Carpeta opcional

            if (empty($clientId) || empty($clientSecret) || empty($refreshToken)) {
                log_message('error', 'GoogleDriveHelper::subirArchivo - Credenciales de Google Drive incompletas en la base de datos.');
                return false;
            }

            // Validar existencia del archivo local
            $fullPath = FCPATH . $localPath;
            if (!file_exists($fullPath)) {
                $fullPath = $localPath;
                if (!file_exists($fullPath)) {
                    log_message('error', "GoogleDriveHelper::subirArchivo - El archivo local no existe: {$localPath}");
                    return false;
                }
            }

            // 1. Obtener Access Token dinámico
            $accessToken = self::obtenerAccessToken($clientId, $clientSecret, $refreshToken);
            if (!$accessToken) {
                log_message('error', 'GoogleDriveHelper::subirArchivo - No se pudo generar el access token de Google API.');
                return false;
            }

            // 1.5 Determinar y crear subcarpeta si aplica
            $parentId = $folderId;
            if (!empty($subfolderName)) {
                $parentId = self::crearOObtenerCarpeta($subfolderName, $parentId, $accessToken);
            }

            // 1.6 Verificar si el archivo ya existe para evitar duplicados
            $queryFile = "name='" . str_replace("'", "\\'", $filename) . "' and trashed=false";
            if (!empty($parentId)) {
                $queryFile .= " and '" . $parentId . "' in parents";
            }
            
            $chSearch = curl_init('https://www.googleapis.com/drive/v3/files?q=' . urlencode($queryFile) . '&fields=files(id)');
            curl_setopt($chSearch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($chSearch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($chSearch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $accessToken]);
            $resSearch = curl_exec($chSearch);
            curl_close($chSearch);
            
            $existingFileId = null;
            if ($resSearch) {
                $searchResult = json_decode($resSearch, true);
                if (!empty($searchResult['files'])) {
                    $existingFileId = $searchResult['files'][0]['id'];
                }
            }

            $fileData = file_get_contents($fullPath);

            // Si existe, actualizamos su contenido en lugar de subir uno nuevo
            if ($existingFileId) {
                $ch = curl_init('https://www.googleapis.com/upload/drive/v3/files/' . $existingFileId . '?uploadType=media');
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fileData);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Authorization: Bearer ' . $accessToken,
                    'Content-Type: ' . $mimeType,
                    'Content-Length: ' . strlen($fileData)
                ]);

                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($httpCode === 200) {
                    log_message('info', "GoogleDriveHelper::subirArchivo - Archivo '{$filename}' actualizado (Reemplazado) en Google Drive con ID: {$existingFileId}");
                    return $existingFileId;
                }
                log_message('error', "GoogleDriveHelper::subirArchivo - Error actualizando archivo: HTTP {$httpCode}");
                return false;
            }

            // Si no existe, creamos el archivo
            $metadata = [
                'name' => $filename
            ];
            if (!empty($parentId)) {
                $metadata['parents'] = [$parentId];
            }

            $metadataJson = json_encode($metadata);
            
            $boundary = '---------------GIBI_ITSI_BOUNDARY_' . md5(time());
            $body = "";
            $body .= "--" . $boundary . "\r\n";
            $body .= "Content-Type: application/json; charset=UTF-8\r\n\r\n";
            $body .= $metadataJson . "\r\n";
            $body .= "--" . $boundary . "\r\n";
            $body .= "Content-Type: " . $mimeType . "\r\n\r\n";
            $body .= $fileData . "\r\n";
            $body .= "--" . $boundary . "--\r\n";

            $ch = curl_init('https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: multipart/related; boundary=' . $boundary,
                'Content-Length: ' . strlen($body)
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 || $httpCode === 201) {
                $result = json_decode($response, true);
                if (isset($result['id'])) {
                    $driveFileId = $result['id'];
                    log_message('info', "GoogleDriveHelper::subirArchivo - Archivo '{$filename}' subido a Google Drive con ID: {$driveFileId}");
                    return $driveFileId;
                }
            }

            log_message('error', "GoogleDriveHelper::subirArchivo - Error de API (HTTP {$httpCode}): " . $response);
            return false;

        } catch (\Exception $e) {
            log_message('error', 'GoogleDriveHelper::subirArchivo - Excepción: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Crea o devuelve el ID de una subcarpeta en Google Drive
     */
    private static function crearOObtenerCarpeta(string $folderName, string $parentId, string $accessToken): string
    {
        $query = "mimeType='application/vnd.google-apps.folder' and name='" . str_replace("'", "\\'", $folderName) . "' and trashed=false";
        if (!empty($parentId)) {
            $query .= " and '" . $parentId . "' in parents";
        }

        $ch = curl_init('https://www.googleapis.com/drive/v3/files?q=' . urlencode($query) . '&fields=files(id)');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $accessToken]);
        
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response) {
            $result = json_decode($response, true);
            if (!empty($result['files'])) {
                return $result['files'][0]['id'];
            }
        }

        // Crear carpeta
        $metadata = [
            'name' => $folderName,
            'mimeType' => 'application/vnd.google-apps.folder'
        ];
        if (!empty($parentId)) {
            $metadata['parents'] = [$parentId];
        }

        $chCreate = curl_init('https://www.googleapis.com/drive/v3/files');
        curl_setopt($chCreate, CURLOPT_POST, true);
        curl_setopt($chCreate, CURLOPT_POSTFIELDS, json_encode($metadata));
        curl_setopt($chCreate, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($chCreate, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($chCreate, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json'
        ]);

        $resCreate = curl_exec($chCreate);
        curl_close($chCreate);

        if ($resCreate) {
            $resultCreate = json_decode($resCreate, true);
            return $resultCreate['id'] ?? $parentId;
        }

        return $parentId;
    }

    /**
     * Obtiene un token de acceso a partir del refresh token
     */
    private static function obtenerAccessToken(string $clientId, string $clientSecret, string $refreshToken)
    {
        try {
            $postFields = [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'refresh_token' => $refreshToken,
                'grant_type' => 'refresh_token'
            ];

            $ch = curl_init('https://oauth2.googleapis.com/token');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/x-www-form-urlencoded'
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200) {
                $result = json_decode($response, true);
                if (isset($result['access_token'])) {
                    return $result['access_token'];
                }
            }

            log_message('error', "GoogleDriveHelper::obtenerAccessToken - Error (HTTP {$httpCode}): " . $response);
            return false;
        } catch (\Exception $e) {
            log_message('error', 'GoogleDriveHelper::obtenerAccessToken - Excepción: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Auxiliar para obtener configuración de la base de datos de manera segura
     */
    private static function getValorDb($db, string $clave, string $default = ''): string
    {
        try {
            if ($db->tableExists('configuracion_sistema')) {
                $row = $db->table('configuracion_sistema')
                    ->where('clave', $clave)
                    ->get()
                    ->getRowArray();
                if ($row && !empty($row['valor'])) {
                    return $row['valor'];
                }
            }
        } catch (\Exception $e) {
            log_message('warning', "GoogleDriveHelper::getValorDb - No se pudo leer clave {$clave}: " . $e->getMessage());
        }
        return $default;
    }
}
