# 🚀 Guía de Despliegue - Taller Mecánico OM

## 📋 Requisitos Previos

- Ubuntu Server 18.04+ o 20.04+
- Acceso root o sudo
- Conexión a internet
- Dominio configurado (opcional)

## 🔧 Instalación Automática

### Opción 1: Script Automático (Recomendado)

```bash
# Descargar el proyecto
git clone <url-del-repositorio>
cd TallerMecanicoOM

# Dar permisos de ejecución al script
chmod +x install.sh

# Ejecutar instalación
sudo ./install.sh
```

### Opción 2: Instalación Manual

#### 1. Actualizar Sistema
```bash
sudo apt update && sudo apt upgrade -y
```

#### 2. Instalar Dependencias
```bash
sudo apt install -y apache2 php php-mysql mysql-server php-json php-curl
```

#### 3. Configurar MySQL
```bash
sudo mysql_secure_installation
```

#### 4. Crear Base de Datos
```bash
sudo mysql -u root -p < database/schema.sql
```

#### 5. Configurar Apache
```bash
# Crear directorio del proyecto
sudo mkdir -p /var/www/taller-mecanico

# Copiar archivos
sudo cp -r ./* /var/www/taller-mecanico/

# Configurar permisos
sudo chown -R www-data:www-data /var/www/taller-mecanico
sudo chmod -R 755 /var/www/taller-mecanico
```

#### 6. Configurar Virtual Host
```bash
sudo nano /etc/apache2/sites-available/taller-mecanico.conf
```

Contenido del archivo:
```apache
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
```

#### 7. Habilitar Sitio
```bash
sudo a2ensite taller-mecanico.conf
sudo a2dissite 000-default.conf
sudo a2enmod rewrite
sudo systemctl reload apache2
```

## 🌐 Configuración de Dominio

### Para Desarrollo Local
```bash
# Editar archivo hosts
sudo nano /etc/hosts

# Agregar línea:
127.0.0.1 taller.local
```

### Para Producción
1. Configurar DNS del dominio
2. Actualizar ServerName en Virtual Host
3. Configurar SSL con Let's Encrypt

## 🔐 Configuración de Seguridad

### 1. Cambiar Contraseñas por Defecto
```bash
# Cambiar contraseña de MySQL
sudo mysql -u root -p
ALTER USER 'taller_user'@'localhost' IDENTIFIED BY 'nueva_contraseña_segura';
FLUSH PRIVILEGES;
```

### 2. Configurar Firewall
```bash
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw allow 22/tcp
sudo ufw --force enable
```

### 3. Configurar SSL (Let's Encrypt)
```bash
# Instalar Certbot
sudo apt install certbot python3-certbot-apache

# Obtener certificado
sudo certbot --apache -d taller.local -d www.taller.local

# Renovar automáticamente
sudo crontab -e
# Agregar línea: 0 12 * * * /usr/bin/certbot renew --quiet
```

## 📊 Verificación de Instalación

### 1. Verificar Servicios
```bash
sudo systemctl status apache2
sudo systemctl status mysql
```

### 2. Verificar Base de Datos
```bash
mysql -u taller_user -p taller -e "SELECT COUNT(*) FROM citas;"
```

### 3. Verificar Sitio Web
```bash
curl -I http://taller.local
```

## 🔧 Configuración Avanzada

### 1. Configurar Respaldos Automáticos
```bash
# Crear script de respaldo
sudo nano /usr/local/bin/backup-taller.sh
```

Contenido:
```bash
#!/bin/bash
BACKUP_DIR="/var/backups/taller"
DATE=$(date +%Y%m%d_%H%M%S)

# Crear directorio de respaldo
mkdir -p $BACKUP_DIR

# Respaldar base de datos
mysqldump -u taller_user -p'taller_password_2024' taller > $BACKUP_DIR/taller_db_$DATE.sql

# Respaldar archivos
tar -czf $BACKUP_DIR/taller_files_$DATE.tar.gz /var/www/taller-mecanico

# Mantener solo los últimos 7 días
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete
```

```bash
# Dar permisos y programar
chmod +x /usr/local/bin/backup-taller.sh
sudo crontab -e
# Agregar línea: 0 2 * * * /usr/local/bin/backup-taller.sh
```

### 2. Configurar Monitoreo
```bash
# Instalar herramientas de monitoreo
sudo apt install -y htop iotop nethogs

# Configurar logrotate
sudo nano /etc/logrotate.d/taller-mecanico
```

Contenido:
```
/var/log/apache2/taller-*.log {
    daily
    missingok
    rotate 52
    compress
    delaycompress
    notifempty
    create 640 root adm
    postrotate
        systemctl reload apache2
    endscript
}
```

## 🐛 Solución de Problemas

### Error de Conexión a Base de Datos
```bash
# Verificar estado de MySQL
sudo systemctl status mysql

# Verificar credenciales
mysql -u taller_user -p taller

# Verificar permisos
sudo chown -R www-data:www-data /var/www/taller-mecanico
```

### Error 500 en Apache
```bash
# Verificar logs
sudo tail -f /var/log/apache2/taller-error.log

# Verificar permisos de archivos PHP
sudo chmod 644 /var/www/taller-mecanico/php/*.php
```

### Problemas de Rendimiento
```bash
# Verificar uso de recursos
htop
df -h
free -h

# Optimizar MySQL
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf
```

## 📞 Soporte

Para soporte técnico:
- Email: soporte@taller.local
- Documentación: https://github.com/taller-mecanico/docs
- Issues: https://github.com/taller-mecanico/issues

## 📝 Notas de Versión

### v1.0.0 (Actual)
- ✅ Sistema completo de agendamiento
- ✅ Consulta de historial
- ✅ Interfaz responsive
- ✅ Base de datos optimizada
- ✅ Script de instalación automática

### Próximas Versiones
- 🔄 Panel de administración
- 🔄 Sistema de notificaciones
- 🔄 Reportes avanzados
- 🔄 API REST completa 