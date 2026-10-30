<?php
include("auth.php");
include("conexion.php");

$error = "";

if(isset($_POST['guardar'])){

    $nombre          = trim($_POST['nombre']);
    $especialidad    = trim($_POST['especialidad']);
    $horario_entrada = $_POST['horario_entrada'];
    $horario_salida  = $_POST['horario_salida'];
    $telefono        = trim($_POST['telefono']);

    if($horario_entrada === $horario_salida){

        $error = "El horario de entrada y el de salida no pueden ser iguales";

    }else{

        $stmt = mysqli_prepare($conn,
            "INSERT INTO medicos
                (nombre, especialidad, horario_entrada, horario_salida, telefono)
             VALUES (?, ?, ?, ?, ?)"
        );

        mysqli_stmt_bind_param($stmt, "sssss",
            $nombre, $especialidad, $horario_entrada, $horario_salida, $telefono
        );

        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Evita que al recargar la página se vuelva a guardar el registro
        header("Location: medicos.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Médicos</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
<?php $activa = 'medicos'; include("menu.php"); ?>

<div class="container">

     <div class="top-bar">

      <a href="index.php" class="btn-volver">
        ← Panel Principal
      </a>

      <h1>Gestión de Médicos</h1>

     </div>
    <div class="panel">

        <div class="form-card">

            <h2>Registrar Médico</h2>

            <?php if($error){ ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php } ?>

            <form method="POST">

                <label>Nombre Completo</label>
                <input type="text" name="nombre" required>

                <label>Especialidad</label>
                <input type="text" name="especialidad" required>

                <label>Horario de Entrada</label>
                <input type="time" name="horario_entrada" required>

                <label>Horario de Salida</label>
                <input type="time" name="horario_salida" required>

                <label>Número Telefónico</label>
                <input type="text" name="telefono" maxlength="20" required>

                <button type="submit" name="guardar">
                    Guardar Médico
                </button>

            </form>

        </div>

        <div class="table-card">

            <h2>Plantilla Médica</h2>

            <div class="table-scroll">

            <table>

                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Especialidad</th>
                    <th>Entrada</th>
                    <th>Salida</th>
                    <th>Teléfono</th>
                </tr>

                <?php

                $resultado = mysqli_query($conn, "SELECT * FROM medicos");

                while($fila = mysqli_fetch_assoc($resultado)){

                    $entrada = $fila['horario_entrada'] ? substr($fila['horario_entrada'], 0, 5) : '';
                    $salida  = $fila['horario_salida']  ? substr($fila['horario_salida'], 0, 5)  : '';

                    echo "
                    <tr>
                        <td>".htmlspecialchars($fila['id'])."</td>
                        <td>".htmlspecialchars($fila['nombre'])."</td>
                        <td>".htmlspecialchars($fila['especialidad'])."</td>
                        <td>".htmlspecialchars($entrada)."</td>
                        <td>".htmlspecialchars($salida)."</td>
                        <td>".htmlspecialchars($fila['telefono'] ?? '')."</td>
                    </tr>
                    ";
                }

                ?>

            </table>

            </div>

        </div>

    </div>

</div>

<script src="sin-historial.js"></script>

</body>
</html>