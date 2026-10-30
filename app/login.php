<?php
session_start();
include("conexion.php");

$error = "";

if (isset($_POST['login'])) {

    $usuario  = $_POST['usuario'];
    $password = $_POST['password'];

    $stmt = mysqli_prepare($conn, "SELECT usuario FROM usuarios WHERE usuario = ? AND password = ?");
    mysqli_stmt_bind_param($stmt, "ss", $usuario, $password);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) == 1) {

        session_regenerate_id(true);
        $_SESSION['usuario'] = $usuario;

        header("Location: index.php");
        exit();

    } else {
        $error = "Usuario o contraseña incorrectos";
    }

    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Trinity Medical Center</title>
    <link rel="stylesheet" href="style.css">
</head>

<body class="login-page">

<div class="login-container">

    <div class="login-card">

        <svg class="login-icon" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Hospital">
            <circle cx="50" cy="50" r="46" fill="#e8f1fb" stroke="#1e5fa8" stroke-width="4"/>
            <rect x="42" y="22" width="16" height="56" rx="3" fill="#1e5fa8"/>
            <rect x="22" y="42" width="56" height="16" rx="3" fill="#1e5fa8"/>
        </svg>

        <h1>Trinity Medical Center</h1>
        <h2>Iniciar Sesión</h2>

        <?php if ($error) { ?>
            <div class="error">
                <?php echo $error; ?>
            </div>
        <?php } ?>

        <form method="POST">

            <label>Usuario</label>
            <input type="text" name="usuario" required>

            <label>Contraseña</label>
            <input type="password" name="password" required>

            <button type="submit" name="login">Entrar</button>

        </form>

    </div>

</div>

</body>
</html>