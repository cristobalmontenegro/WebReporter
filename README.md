# WebReporter PHP 📊

WebReporter PHP is a lightweight, universal reporting system designed to transform MySQL/MariaDB data into clean, interactive tables and charts. It features a customizable sidebar, dashboard, and a built-in authentication system for easy use and deployment.

---

## 🌎 English Description

WebReporter PHP allows developers and professionals to create quick data visualizations and detailed reports from their existing databases. It is designed to be "plug-and-play" with a simple configuration system and modern branding.

### ✨ Features
- **Interactive Reports**: Filter, search, and export your data to Excel or Print.
- **Visual Dashboard**: Customizable charts (Bar, Pie) for key metrics.
- **User Management**: Integrated roles (Admin/User) and access control.
- **Generic Branding**: Easily customizable with your own logo and name.
- **Security-First**: Built-in protection against CSRF and XSS, with session security.

### 📋 Prerequisites
- PHP 7.4+
- MySQL or MariaDB
- Web server (Apache/Nginx)

### ⚙️ Installation

1. **Clone the repository**:
   ```bash
   git clone https://github.com/cristobalmontenegro/WebReporter.git
   cd WebReporter
   ```

2. **Database Setup**:
   - Run the provided `database.sql` in your MySQL database to create the necessary `user_table`.
   - Add your users and set `is_admin = 1` for administrative access.

3. **Configuration**:
   - Copy `config.php.example` to `config.local.php`.
   - Edit `config.local.php` with your database credentials and define your reports.
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'my_user');
   define('DB_PASS', 'my_password');
   define('DB_NAME', 'my_database');
   ```

4. **Add Reports & Charts**:
   - Define your detailed reports in the `$REPORTS` array and dashboard widgets in the `$CHARTS` array within `config.local.php`.

### 🔒 Security Recommendations
- **Protect your configuration**: Ensure `config.local.php` is NEVER committed (already in `.gitignore`).
- **Use HTTPS**: Mandatory for protecting your login credentials.
- **Database Permissions**: Use a dedicated user with **SELECT** permissions for report data. Note that **UPDATE** permissions on `user_table` are required for administrative functions (enabling/disabling access).
- **Secure Sessions**: Ensure your server has `session.cookie_httponly = 1` and `session.cookie_secure = 1`.

### ☕ Support / Donations
If this project helped you, consider supporting my work:
[https://ko-fi.com/cristobalmontenegro](https://ko-fi.com/cristobalmontenegro)

---

## 🇪🇸 Descripción en Español

WebReporter PHP es un sistema de reportería ligero y universal diseñado para transformar datos de MySQL/MariaDB en tablas y gráficas interactivas y limpias.

### ✨ Características
- **Reportes Interactivos**: Filtra, busca y exporta tus datos a Excel o Impresión.
- **Dashboard Visual**: Gráficas personalizables (Barras, Pastel) para tus métricas clave.
- **Gestión de Usuarios**: Roles integrados (Admin/Usuario) y control de accesos.
- **Branding Genérico**: Fácilmente personalizable con tu propio logo y nombre.
- **Seguridad**: Protección integrada contra CSRF y XSS, además de seguridad por sesiones.

### 📋 Prerrequisitos
- PHP 7.4+
- MySQL o MariaDB
- Servidor web (Apache/Nginx)

### ⚙️ Instalación

1. **Clonar el repositorio**:
   ```bash
   git clone https://github.com/cristobalmontenegro/WebReporter.git
   cd WebReporter
   ```

2. **Configuración de Base de Datos**:
   - Ejecuta `database.sql` en tu base de datos MySQL para crear la tabla `user_table`.
   - Agrega tus usuarios y establece `is_admin = 1` para el acceso administrativo.

3. **Configuración del Sistema**:
   - Copia `config.php.example` a `config.local.php`.
   - Edita `config.local.php` con tus credenciales de base de datos.
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'mi_usuario');
   define('DB_PASS', 'mi_contraseña');
   define('DB_NAME', 'mi_base_de_datos');
   ```

4. **Añadir Reportes y Gráficas**:
   - Define tus reportes detallados en el array `$REPORTS` y los widgets del tablero en `$CHARTS` dentro de `config.local.php`.

### 🔒 Recomendaciones de Seguridad
- **Protege tu configuración**: Asegúrate de que `config.local.php` NUNCA sea subido al repositorio (incluido en `.gitignore`).
- **Usa HTTPS**: Obligatorio para proteger las credenciales de acceso.
- **Permisos de Base de Datos**: Usa un usuario dedicado con permisos de **SELECT** para los datos de los reportes. Ten en cuenta que se requieren permisos de **UPDATE** sobre la tabla `user_table` para las funciones administrativas (habilitar/deshabilitar accesos).
- **Sesiones Seguras**: Asegura que tu servidor tenga activo `session.cookie_httponly = 1` y `session.cookie_secure = 1`.

### ☕ Soporte / Donaciones
Si este proyecto te ha sido de utilidad, ¡puedes apoyarme invitándome a un café!
[https://ko-fi.com/cristobalmontenegro](https://ko-fi.com/cristobalmontenegro)

---

## 📄 License
Copyright (c) 2026 Cristobal Montenegro. Licensed under the MIT License. See [LICENSE](LICENSE) for more details.
