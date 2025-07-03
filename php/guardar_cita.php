<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
/**
 * Archivo para guardar citas en la base de datos
 * Taller Mecánico OM
 */

// Incluir archivo de conexión
require_once 'db.php';

// Configurar headers para JSON
header('Content-Type: application/json; charset=utf-8');

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

// Log de depuración para ver los datos POST recibidos
file_put_contents('/tmp/debug_post.txt', print_r($_POST, true));

try {
    // Obtener y sanitizar datos del formulario
    // Asegúrate de que estos campos coincidan con los del formulario HTML
    $nombre = sanitizeInput($_POST['nombre'] ?? '');
    $telefono = sanitizeInput($_POST['telefono'] ?? '');
    $placa = sanitizeInput($_POST['placa'] ?? '');
    $marca = sanitizeInput($_POST['marca'] ?? '');
    $modelo = sanitizeInput($_POST['modelo'] ?? '');
    $servicio = sanitizeInput($_POST['servicio'] ?? '');
    $fecha = sanitizeInput($_POST['fecha'] ?? '');
    $hora = sanitizeInput($_POST['hora'] ?? '');
    $descripcion = sanitizeInput($_POST['descripcion'] ?? '');

    // Validar campos requeridos
    if (empty($nombre) || empty($telefono) || empty($placa) || 
        empty($marca) || empty($modelo) || empty($servicio) || 
        empty($fecha) || empty($hora)) {
        throw new Exception('Todos los campos son obligatorios');
    }

    // Validar formato de datos
    if (!validarPlaca($placa)) {
        throw new Exception('Formato de placa inválido. Use formato: ABC123');
    }

    if (!validarTelefono($telefono)) {
        throw new Exception('Formato de teléfono inválido. Use 10 dígitos');
    }

    if (!validarFecha($fecha)) {
        throw new Exception('Fecha inválida');
    }

    if (!validarHora($hora)) {
        throw new Exception('Hora inválida');
    }

    // Verificar que la fecha no sea domingo
    $fechaObj = new DateTime($fecha);
    if ($fechaObj->format('N') == 7) { // 7 = domingo
        throw new Exception('No trabajamos los domingos');
    }

    // Verificar que la fecha no sea en el pasado
    $hoy = new DateTime();
    $hoy->setTime(0, 0, 0);
    if ($fechaObj < $hoy) {
        throw new Exception('No se pueden agendar citas en fechas pasadas');
    }

    // Verificar horario de trabajo (8:00 AM - 5:00 PM)
    $horaObj = DateTime::createFromFormat('H:i', $hora);
    $horaInt = (int)$horaObj->format('H');
    if ($horaInt < 8 || $horaInt > 17) {
        throw new Exception('Horario de trabajo: 8:00 AM - 5:00 PM');
    }

    // Combinar fecha y hora
    $fechaHora = $fecha . ' ' . $hora . ':00';

    // Verificar disponibilidad de horario
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM citas WHERE fecha = ? AND hora = ?");
    $stmt->execute([$fecha, $hora]);
    $citasExistentes = $stmt->fetchColumn();

    if ($citasExistentes > 0) {
        throw new Exception('Este horario ya está ocupado. Por favor selecciona otro horario.');
    }

    // Insertar la cita en la base de datos
    $stmt = $pdo->prepare("
        INSERT INTO citas (nombre, telefono, placa, marca, modelo, servicio, fecha, hora, descripcion, fecha_creacion) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");

    $stmt->execute([
        $nombre,
        $telefono,
        strtoupper($placa),
        $marca,
        $modelo,
        $servicio,
        $fecha,
        $hora,
        $descripcion
    ]);

    $citaId = $pdo->lastInsertId();

    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'message' => 'Cita agendada correctamente',
        'cita_id' => $citaId,
        'datos' => [
            'nombre' => $nombre,
            'placa' => strtoupper($placa),
            'servicio' => $servicio,
            'fecha' => $fecha,
            'hora' => $hora
        ]
    ]);

} catch (PDOException $e) {
    // Error de base de datos
    error_log("Error de base de datos: " . $e->getMessage());
    http_response_code(500);
    // Mostrar el error real en desarrollo
    echo json_encode(['error' => $e->getMessage()]);
    
} catch (Exception $e) {
    // Error de validación o lógica
    http_response_code(400);
    // Mostrar el error real en desarrollo
    echo json_encode(['error' => $e->getMessage()]);
}
?> 