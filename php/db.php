<?php
/**
 * Archivo de conexión a la base de datos MySQL
 * Taller Mecánico OM
 */

// Configuración de la base de datos
$host = "localhost";
$db = "taller";
$user = "usuario";
$pass = "clave";

// Configuración de zona horaria
date_default_timezone_set('America/Bogota');

try {
    // Crear conexión PDO
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    
    // Configurar PDO para lanzar excepciones en caso de error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Configurar PDO para usar consultas preparadas por defecto
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    // En producción, no mostrar detalles del error
    error_log("Error de conexión a la base de datos: " . $e->getMessage());
    die("Error de conexión a la base de datos. Por favor, contacta al administrador.");
}

/**
 * Función para sanitizar datos de entrada
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Función para validar formato de placa colombiana
 */
function validarPlaca($placa) {
    $placa = strtoupper($placa);
    return preg_match('/^[A-Z]{3}\d{3}$/', $placa);
}

/**
 * Función para validar formato de teléfono
 */
function validarTelefono($telefono) {
    $telefono = preg_replace('/\D/', '', $telefono);
    return strlen($telefono) === 10;
}

/**
 * Función para validar fecha
 */
function validarFecha($fecha) {
    $fechaObj = DateTime::createFromFormat('Y-m-d', $fecha);
    return $fechaObj && $fechaObj->format('Y-m-d') === $fecha;
}

/**
 * Función para validar hora
 */
function validarHora($hora) {
    $horaObj = DateTime::createFromFormat('H:i', $hora);
    return $horaObj && $horaObj->format('H:i') === $hora;
}
?> 