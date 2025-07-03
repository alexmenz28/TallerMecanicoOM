-- =====================================================
-- ESQUEMA DE BASE DE DATOS - TALLER MECÁNICO OM
-- =====================================================

-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS taller CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Usar la base de datos
USE taller;

-- Tabla principal de citas
CREATE TABLE citas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    telefono VARCHAR(15) NOT NULL,
    placa VARCHAR(20) NOT NULL,
    marca VARCHAR(50) NOT NULL,
    modelo VARCHAR(50) NOT NULL,
    servicio VARCHAR(100) NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    descripcion TEXT,
    estado ENUM('Pendiente', 'En Proceso', 'Completado', 'Cancelado') DEFAULT 'Pendiente',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Índices para mejorar el rendimiento
    INDEX idx_placa (placa),
    INDEX idx_fecha (fecha),
    INDEX idx_estado (estado),
    INDEX idx_fecha_hora (fecha, hora)
);

-- Tabla de servicios disponibles
CREATE TABLE servicios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio_base DECIMAL(10,2),
    duracion_estimada INT COMMENT 'Duración en minutos',
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de horarios de trabajo
CREATE TABLE horarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dia_semana ENUM('Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo') NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    activo BOOLEAN DEFAULT TRUE,
    UNIQUE KEY unique_dia (dia_semana)
);

-- Tabla de configuración del sistema
CREATE TABLE configuracion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(50) NOT NULL UNIQUE,
    valor TEXT,
    descripcion VARCHAR(255),
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insertar datos iniciales

-- Servicios disponibles
INSERT INTO servicios (nombre, descripcion, precio_base, duracion_estimada) VALUES
('Cambio de aceite', 'Cambio de aceite sintético y filtro de aceite', 45.00, 60),
('Revisión de frenos', 'Revisión completa del sistema de frenos', 35.00, 45),
('Diagnóstico de motor', 'Análisis computarizado del motor', 25.00, 30),
('Sistema eléctrico', 'Revisión y reparación de sistemas eléctricos', 40.00, 90),
('Aire acondicionado', 'Servicio de climatización', 50.00, 120),
('Suspensión', 'Revisión y reparación de suspensión', 60.00, 120),
('Transmisión', 'Servicio de transmisión automática/manual', 80.00, 180),
('Inspección técnica', 'Revisión completa del vehículo', 30.00, 60);

-- Horarios de trabajo
INSERT INTO horarios (dia_semana, hora_inicio, hora_fin) VALUES
('Lunes', '08:00:00', '18:00:00'),
('Martes', '08:00:00', '18:00:00'),
('Miércoles', '08:00:00', '18:00:00'),
('Jueves', '08:00:00', '18:00:00'),
('Viernes', '08:00:00', '18:00:00'),
('Sábado', '08:00:00', '14:00:00');

-- Configuración del sistema
INSERT INTO configuracion (clave, valor, descripcion) VALUES
('nombre_taller', 'Taller Mecánico OM', 'Nombre del taller'),
('telefono', '(555) 123-4567', 'Teléfono de contacto'),
('email', 'info@tallermecanicoom.com', 'Email de contacto'),
('direccion', 'Calle Principal 123, Ciudad', 'Dirección del taller'),
('max_citas_dia', '20', 'Máximo número de citas por día'),
('tiempo_entre_citas', '60', 'Tiempo mínimo entre citas en minutos');

-- Crear usuario para la aplicación (cambiar credenciales en producción)
CREATE USER IF NOT EXISTS 'taller_user'@'localhost' IDENTIFIED BY 'taller_password_2024';
GRANT SELECT, INSERT, UPDATE, DELETE ON taller.* TO 'taller_user'@'localhost';
FLUSH PRIVILEGES;

-- Datos de ejemplo para pruebas
INSERT INTO citas (nombre, telefono, placa, marca, modelo, servicio, fecha, hora, descripcion, estado) VALUES
('Juan Pérez', '3001234567', 'ABC123', 'Toyota', 'Corolla 2020', 'Cambio de aceite', '2024-01-15', '09:00:00', 'Cambio de aceite sintético y filtro', 'Completado'),
('María García', '3109876543', 'XYZ789', 'Honda', 'Civic 2019', 'Revisión de frenos', '2024-01-16', '10:00:00', 'Revisión de pastillas y discos', 'Completado'),
('Carlos López', '3155551234', 'DEF456', 'Ford', 'Focus 2021', 'Diagnóstico de motor', '2024-01-17', '14:00:00', 'Luz de check engine encendida', 'Completado'),
('Ana Rodríguez', '3201112222', 'GHI789', 'Chevrolet', 'Spark 2018', 'Cambio de aceite', '2024-01-18', '11:00:00', 'Mantenimiento preventivo', 'Pendiente');

-- Mostrar información de la base de datos
SELECT 'Base de datos creada exitosamente' AS mensaje;
SELECT COUNT(*) AS total_servicios FROM servicios;
SELECT COUNT(*) AS total_citas FROM citas; 