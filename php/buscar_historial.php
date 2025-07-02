<?php
/**
 * Archivo para buscar historial de servicios por placa
 * Taller Mecánico OM
 */

// Incluir archivo de conexión
require_once 'db.php';

// Configurar headers para JSON
header('Content-Type: application/json; charset=utf-8');

// Verificar que sea una petición GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

try {
    // Obtener y sanitizar la placa
    $placa = sanitizeInput($_GET['placa'] ?? '');

    // Validar que se proporcione la placa
    if (empty($placa)) {
        throw new Exception('Se requiere la placa del vehículo');
    }

    // Validar formato de placa
    if (!validarPlaca($placa)) {
        throw new Exception('Formato de placa inválido. Use formato: ABC123');
    }

    $placa = strtoupper($placa);

    // Buscar información del vehículo
    $stmt = $pdo->prepare("
        SELECT DISTINCT 
            placa,
            marca,
            modelo,
            COUNT(*) as total_servicios,
            MAX(fecha) as ultimo_servicio
        FROM citas 
        WHERE placa = ?
        GROUP BY placa, marca, modelo
    ");
    $stmt->execute([$placa]);
    $vehiculo = $stmt->fetch();

    if (!$vehiculo) {
        // No se encontraron registros
        echo json_encode([
            'success' => true,
            'encontrado' => false,
            'message' => 'No se encontraron registros para esta placa'
        ]);
        exit;
    }

    // Buscar todos los servicios del vehículo
    $stmt = $pdo->prepare("
        SELECT 
            id,
            nombre,
            telefono,
            servicio,
            fecha,
            hora,
            descripcion,
            estado,
            fecha_creacion
        FROM citas 
        WHERE placa = ?
        ORDER BY fecha DESC, hora DESC
    ");
    $stmt->execute([$placa]);
    $servicios = $stmt->fetchAll();

    // Calcular estadísticas adicionales
    $serviciosCount = [];
    foreach ($servicios as $servicio) {
        $tipo = $servicio['servicio'];
        $serviciosCount[$tipo] = ($serviciosCount[$tipo] ?? 0) + 1;
    }

    $servicioFrecuente = '';
    $maxCount = 0;
    foreach ($serviciosCount as $tipo => $count) {
        if ($count > $maxCount) {
            $maxCount = $count;
            $servicioFrecuente = $tipo;
        }
    }

    // Formatear datos para la respuesta
    $serviciosFormateados = [];
    foreach ($servicios as $servicio) {
        $serviciosFormateados[] = [
            'id' => $servicio['id'],
            'nombre' => $servicio['nombre'],
            'telefono' => $servicio['telefono'],
            'servicio' => $servicio['servicio'],
            'fecha' => $servicio['fecha'],
            'hora' => $servicio['hora'],
            'descripcion' => $servicio['descripcion'],
            'estado' => $servicio['estado'] ?? 'Completado',
            'fecha_creacion' => $servicio['fecha_creacion']
        ];
    }

    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'encontrado' => true,
        'vehiculo' => [
            'placa' => $vehiculo['placa'],
            'marca' => $vehiculo['marca'],
            'modelo' => $vehiculo['modelo'],
            'total_servicios' => (int)$vehiculo['total_servicios'],
            'ultimo_servicio' => $vehiculo['ultimo_servicio'],
            'servicio_frecuente' => $servicioFrecuente
        ],
        'servicios' => $serviciosFormateados
    ]);

} catch (PDOException $e) {
    // Error de base de datos
    error_log("Error de base de datos en buscar_historial: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Error interno del servidor']);
    
} catch (Exception $e) {
    // Error de validación o lógica
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
?> 