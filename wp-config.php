<?php
/**
 * Archivo de configuración de WordPress.
 *
 * Este archivo se utiliza para generar la configuración inicial de WordPress durante la instalación.
 * También se utiliza para definir las configuraciones de la base de datos y las claves de seguridad.
 * Puedes encontrar más información en https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** Configuración de la base de datos ** //

/** El nombre de la base de datos de WordPress */
define( 'DB_NAME', 'miguel_db' );

/** Tu nombre de usuario de MySQL */
define( 'DB_USER', 'admin' );

/** Tu contraseña de MySQL */
define( 'DB_PASSWORD', 'Duoc.2023' );

/** Host de la base de datos de MySQL */
define( 'DB_HOST', 'miguel-cluster-instance-1.ccboct1queaq.us-east-1.rds.amazonaws.com' );

/** Codificación de caracteres para la base de datos */
define( 'DB_CHARSET', 'utf8mb4' );

/** Colación de la base de datos. No cambies esto si no sabes lo que estás haciendo. */
define( 'DB_COLLATE', '' );

// ** Claves de seguridad únicas ** //

/** Clave secreta de autenticación */
define( 'AUTH_KEY',         'Duoc.2023' );
define( 'SECURE_AUTH_KEY',  'Duoc.2023' );
define( 'LOGGED_IN_KEY',    'Duoc.2023' );
define( 'NONCE_KEY',        'Duoc.2023' );
define( 'AUTH_SALT',        'Duoc.2023' );
define( 'SECURE_AUTH_SALT', 'Duoc.2023' );
define( 'LOGGED_IN_SALT',   'Duoc.2023' );
define( 'NONCE_SALT',       'Duoc.2023' );

// ** Prefijo de la tabla de la base de datos ** //
$table_prefix = 'wp_';

/** Configuración de idioma */
define( 'WPLANG', 'es_CL' );
// ** Modo de depuración ** //
/** Cambia a true para activar la depuración. Es importante desactivar esto en un entorno de producción. */
define( 'WP_DEBUG', false );

// ** Carpetas y URL de WordPress ** //
/** Ruta absoluta al directorio de instalación de WordPress */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', _DIR_ . '/' );
}

// ** Configuración adicional ** //

/** Deshabilitar la edición de archivos desde el administrador de WordPress */
define( 'DISALLOW_FILE_EDIT', true );

/** Deshabilitar la instalación de temas y plugins desde el administrador de WordPress */
define( 'DISALLOW_FILE_MODS', true );

/** Configuración de los límites de memoria de PHP */
define( 'WP_MEMORY_LIMIT', '256M' );
define( 'WP_MAX_MEMORY_LIMIT', '512M' );

/** Habilitar actualizaciones automáticas para los plugins y temas principales */
define( 'AUTOMATIC_UPDATER_DISABLED', false );
define( 'WP_AUTO_UPDATE_CORE', true );

// ** ¡Eso es todo, deja de editar! ** //
/** La configuración de WordPress debe ir antes de esto. De lo contrario, ocasionará errores. */
require_once ABSPATH . 'wp-settings.php';
