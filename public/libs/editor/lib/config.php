<?php if (!defined('DATATABLES')) exit(); // Ensure being used in DataTables env.

// Enable error reporting for debugging (only in debug mode)
if (config('app.debug')) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Database user / pass
 */
$sql_details = array(
	"type" => "Mysql",         // Database type: "Mysql", "Postgres", "Sqlserver", "Sqlite" or "Oracle"
	"user" => config('app.db_connection_details.DB_USERNAME'),        // Database user name
	"pass" => config('app.db_connection_details.DB_PASSWORD'),     // Database password
	"host" => config('app.db_connection_details.DB_HOST'),          // Database host
	"port" => config('app.db_connection_details.DB_PORT'),         // Database connection port (can be left empty for default)
	"db"   => config('app.db_connection_details.DB_DATABASE'),         // Database name
	"dsn"  => "charset=utf8",  // PHP DSN extra information. Set as `charset=utf8mb4` if you are using MySQL
	"pdoAttr" => array()       // PHP PDO attributes array. See the PHP documentation for all options
);


// This is included for the development and deploy environment used on the DataTables
// server. You can delete this block - it just includes my own user/pass without making
// them public!
if ( is_file($_SERVER['DOCUMENT_ROOT']."/datatables/pdo.php") ) {
	include( $_SERVER['DOCUMENT_ROOT']."/datatables/pdo.php" );
}
// /End development include

