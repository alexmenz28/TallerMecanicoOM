Frontend: HTML + CSS + JS puro

Backend: PHP (por su compatibilidad nativa con Apache)

Base de datos: MySQL o MariaDB (ambas funcionan bien en Ubuntu)

Servidor Web: Apache2

📦 Estructura del Proyecto (actualizada)

taller-mecanico/
├── index.html
├── agendar.html
├── historial.html
├── servicios.html
│
├── css/
│   └── styles.css
│
├── js/
│   ├── main.js
│   ├── agendar.js
│   └── historial.js
│
├── php/
│   ├── db.php             <- conexión a la base de datos
│   ├── guardar_cita.php   <- guarda una cita en la BD
│   └── buscar_historial.php <- busca datos por placa
│
├── assets/
│   └── img/
│
└── README.md
🧠 Lógica de Conexión a Base de Datos (db.php)

<?php
$host = "localhost";
$db = "taller";
$user = "usuario";
$pass = "clave";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Conexión fallida: " . $e->getMessage());
}
?>
📋 Ejemplo de Inserción (guardar_cita.php)

<?php
require 'db.php';

$nombre = $_POST['nombre'];
$placa = $_POST['placa'];
$servicio = $_POST['servicio'];
$fecha = $_POST['fecha'];

$stmt = $pdo->prepare("INSERT INTO citas (nombre, placa, servicio, fecha) VALUES (?, ?, ?, ?)");
$stmt->execute([$nombre, $placa, $servicio, $fecha]);

echo "Cita registrada correctamente";
?>
Y en tu formulario HTML usarías:

<form action="php/guardar_cita.php" method="POST">
🧩 Esquema de Base de Datos sugerido

CREATE DATABASE taller;

USE taller;

CREATE TABLE citas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100),
    placa VARCHAR(20),
    servicio VARCHAR(100),
    fecha DATETIME
);
📌 Modificación sugerida del README.md
Agrega esto al final:

## 🗄️ Conexión a Base de Datos

- Se usa MySQL/MariaDB para persistencia.
- Archivo `php/db.php` gestiona la conexión usando PDO.
- El sistema incluye scripts para guardar citas y consultar historial.
- Tabla principal: `citas`

### Instalación rápida en Ubuntu 18.04

```bash
sudo apt install mysql-server php-mysql
sudo mysql_secure_installation

---

¿Te gustaría que prepare todo esto como ZIP descargable o quieres que trabajemos archivo por archivo contigo?
Perfecto. A continuación te dejo el contenido actualizado y completo que puedes copiar y pegar directamente en tu archivo README.md dentro del proyecto para que Cursor lo utilice como contexto. Incluye frontend, backend y conexión a MySQL.

# 🛠️ Proyecto Web para Taller Mecánico de Autos

## 📌 Descripción General

Sistema web desarrollado con **HTML, CSS y JavaScript puro**, desplegado en un servidor **Ubuntu Server 18.04.3** con **Apache2**. Se utiliza **PHP** como lenguaje backend y **MySQL** como sistema de base de datos relacional.

Este sistema permite que los clientes puedan agendar citas para sus vehículos, ver los servicios ofrecidos y consultar el historial de atenciones mediante su placa. 

---

## 🧱 Estructura del Proyecto

taller-mecanico/
├── index.html # Página principal
├── agendar.html # Formulario de reserva de cita
├── historial.html # Consulta de historial por placa
├── servicios.html # Listado de servicios ofrecidos
│
├── css/
│ └── styles.css # Estilos generales del sitio
│
├── js/
│ ├── main.js # Comportamiento general
│ ├── agendar.js # Lógica del formulario de citas
│ └── historial.js # Lógica para historial por placa
│
├── php/
│ ├── db.php # Conexión PDO a la base de datos MySQL
│ ├── guardar_cita.php # Inserta cita en la base de datos
│ └── buscar_historial.php # Consulta de historial por placa
│
├── assets/
│ └── img/ # Logos e imágenes estáticas
│
└── README.md # Documentación principal del proyecto

---

## ⚙️ Requisitos Técnicos

- Ubuntu Server 18.04.3
- Apache2
- PHP 7.2+
- MySQL 5.7+
- Navegadores modernos (para JS puro y CSS3)

---

## 🌐 Instalación en Ubuntu Server

### 1. Instalar Apache, PHP y MySQL
```bash
sudo apt update
sudo apt install apache2 php php-mysql mysql-server
sudo systemctl enable apache2 mysql
sudo systemctl start apache2 mysql
2. Subir archivos del proyecto

sudo mkdir -p /var/www/taller-mecanico
sudo cp -r ./taller-mecanico/* /var/www/taller-mecanico/
3. Crear Virtual Host
Archivo: /etc/apache2/sites-available/taller-mecanico.conf


<VirtualHost *:80>
    ServerAdmin admin@taller.local
    DocumentRoot /var/www/taller-mecanico
    ServerName taller.local

    <Directory /var/www/taller-mecanico>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/taller-error.log
    CustomLog ${APACHE_LOG_DIR}/taller-access.log combined
</VirtualHost>
Activar el sitio:


sudo a2ensite taller-mecanico.conf
sudo systemctl reload apache2
🗄️ Base de Datos MySQL
1. Crear Base de Datos y Tabla

CREATE DATABASE taller;

USE taller;

CREATE TABLE citas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100),
    placa VARCHAR(20),
    servicio VARCHAR(100),
    fecha DATETIME
);
2. Archivo de Conexión php/db.php

<?php
$host = "localhost";
$db = "taller";
$user = "usuario";
$pass = "clave";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Conexión fallida: " . $e->getMessage());
}
?>
3. Insertar una cita php/guardar_cita.php

<?php
require 'db.php';

$nombre = $_POST['nombre'];
$placa = $_POST['placa'];
$servicio = $_POST['servicio'];
$fecha = $_POST['fecha'];

$stmt = $pdo->prepare("INSERT INTO citas (nombre, placa, servicio, fecha) VALUES (?, ?, ?, ?)");
$stmt->execute([$nombre, $placa, $servicio, $fecha]);

echo "Cita registrada correctamente";
?>
🔐 Seguridad (pendiente de implementar)
Validación de formularios en frontend y backend

Escape de parámetros de entrada

Prevención de inyecciones SQL

Posible autenticación para módulo de administración (futuro)

🧠 Siguientes Pasos
Agregar autenticación para el mecánico

Crear un dashboard administrativo

Validación de horarios disponibles

Generación de reportes de clientes y servicios

👨‍🔧 Autor
Desarrollado por Alex (Ingeniero de Sistemas)
Proyecto para despliegue en entorno Linux autoalojado

---

Este archivo servirá como **documento fuente** para que Cursor entienda el contexto completo de tu sistema, la estructura, tecnologías usadas y el comportamiento esperado. ¿Quieres que prepare este contenido como un archivo `.md` para que lo descargues directamente o lo vas a copiar manualmente?