<?php
// Configuración de la base de datos.
// Por defecto usa los valores de XAMPP; con Docker se reemplazan por variables de entorno.
$host     = getenv('DB_HOST') ?: 'localhost';
$usuario  = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
$bd       = getenv('DB_NAME') ?: 'hospital';

$conn = mysqli_connect($host, $usuario, $password, $bd);

if (!$conn) {
    die("Error de conexión: " . mysqli_connect_error());
}