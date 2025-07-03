#!/bin/bash

# =====================================================
# SCRIPT DE INSTALACIÓN - TALLER MECÁNICO OM
# =====================================================

echo "🔧 Iniciando instalación del Taller Mecánico OM..."
echo "=================================================="

# Verificar si se ejecuta como root
if [ "$EUID" -ne 0 ]; then
    echo "❌ Este script debe ejecutarse como root (sudo)"
    exit 1
fi

# Actualizar sistema
echo "📦 Actualizando sistema..."
apt update && apt upgrade -y

# Instalar dependencias
echo "📦 Instalando Apache, PHP y MySQL..."
apt install -y apache2 php php-mysql mysql-server php-json php-curl

# Habilitar y iniciar servicios
echo "🚀 Configurando servicios..."
systemctl enable apache2 mysql
systemctl start apache2 mysql

# Configurar MySQL
echo "🗄️ Configurando MySQL..."
mysql -e "ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'root_password_2024';"
mysql -e "FLUSH PRIVILEGES;"

# Crear directorio del proyecto
echo "📁 Creando directorio del proyecto..."
mkdir -p /var/www/taller-mecanico

# Copiar archivos del proyecto
echo "📋 Copiando archivos del proyecto..."
cp -r ./* /var/www/taller-mecanico/

# Configurar permisos
echo "🔐 Configurando permisos..."
chown -R www-data:www-data /var/www/taller-mecanico
chmod -R 755 /var/www/taller-mecanico

# Crear Virtual Host
echo "🌐 Configurando Virtual Host..."
cat > /etc/apache2/sites-available/taller-mecanico.conf << 'EOF'
<VirtualHost *:80>
    ServerAdmin admin@taller.local
    DocumentRoot /var/www/taller-mecanico
    ServerName taller.local
    ServerAlias www.taller.local

    <Directory /var/www/taller-mecanico>
        AllowOverride All
        Require all granted
        Options Indexes FollowSymLinks
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/taller-error.log
    CustomLog ${APACHE_LOG_DIR}/taller-access.log combined
</VirtualHost>
EOF

# Habilitar sitio
a2ensite taller-mecanico.conf
a2dissite 000-default.conf

# Habilitar módulos necesarios
a2enmod rewrite

# Recargar Apache
systemctl reload apache2

# Importar base de datos
echo "🗄️ Importando base de datos..."
if [ -f "database/schema.sql" ]; then
    mysql < database/schema.sql
    echo "✅ Base de datos importada correctamente"
else
    echo "⚠️ Archivo schema.sql no encontrado. Importa manualmente la base de datos."
fi

# Configurar firewall
echo "🔥 Configurando firewall..."
ufw allow 80/tcp
ufw allow 22/tcp
ufw --force enable

# Crear archivo de configuración
echo "⚙️ Creando archivo de configuración..."
cat > /var/www/taller-mecanico/config.php << 'EOF'
<?php
// Configuración del sistema
define('SITE_NAME', 'Taller Mecánico OM');
define('SITE_URL', 'http://taller.local');
define('ADMIN_EMAIL', 'admin@taller.local');

// Configuración de base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'taller');
define('DB_USER', 'taller_user');
define('DB_PASS', 'taller_password_2024');

// Configuración de seguridad
define('SECURE_SESSION', true);
define('SESSION_TIMEOUT', 3600); // 1 hora

// Configuración de citas
define('MAX_CITAS_DIA', 20);
define('TIEMPO_ENTRE_CITAS', 60); // minutos
?>
EOF

# Mostrar información de instalación
echo ""
echo "🎉 ¡Instalación completada exitosamente!"
echo "=========================================="
echo "📍 URL del sitio: http://taller.local"
echo "📁 Directorio: /var/www/taller-mecanico"
echo "🗄️ Base de datos: taller"
echo "👤 Usuario DB: taller_user"
echo "🔑 Contraseña DB: taller_password_2024"
echo ""
echo "📋 Próximos pasos:"
echo "1. Agregar 'taller.local' a tu archivo hosts"
echo "2. Configurar SSL con Let's Encrypt (opcional)"
echo "3. Cambiar las contraseñas por defecto"
echo "4. Configurar respaldos automáticos"
echo ""
echo "🔧 Para verificar la instalación:"
echo "sudo systemctl status apache2"
echo "sudo systemctl status mysql"
echo "mysql -u taller_user -p taller -e 'SELECT COUNT(*) FROM citas;'" 