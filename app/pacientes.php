<?php
include("auth.php");
include("conexion.php");

$error = "";

if(isset($_POST['guardar'])){

    $nombre             = trim($_POST['nombre']);
    $edad               = (int) $_POST['edad'];
    $telefono           = trim($_POST['telefono']);
    $no_seg_social      = trim($_POST['no_seg_social']);
    $fecha_nacimiento   = $_POST['fecha_nacimiento'];
    $domicilio          = trim($_POST['domicilio']);
    $colonia_localidad  = trim($_POST['colonia_localidad']);
    $entidad_federativa = trim($_POST['entidad_federativa']);
    $consultorio_no     = trim($_POST['consultorio_no']);
    $horario            = $_POST['horario'];

    if(!in_array($horario, ['M', 'V'], true)){

        $error = "El horario debe ser M (Matutino) o V (Vespertino)";

    }else{

        $stmt = mysqli_prepare($conn,
            "INSERT INTO pacientes
                (nombre, edad, telefono, no_seg_social, fecha_nacimiento,
                 domicilio, colonia_localidad, entidad_federativa,
                 consultorio_no, horario)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        mysqli_stmt_bind_param($stmt, "sissssssss",
            $nombre, $edad, $telefono, $no_seg_social, $fecha_nacimiento,
            $domicilio, $colonia_localidad, $entidad_federativa,
            $consultorio_no, $horario
        );

        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Evita que al recargar la página se vuelva a guardar el registro
        header("Location: pacientes.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Pacientes</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
<?php $activa = 'pacientes'; include("menu.php"); ?>

<div class="container">

  <div class="top-bar">

    <a href="index.php" class="btn-volver">
        ← Panel Principal
    </a>

    <h1>Gestión de Pacientes</h1>

  </div>


    <div class="panel">

        <div class="form-card">

            <h2>Registrar Paciente</h2>

            <?php if($error){ ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php } ?>

            <form method="POST">

                <label>Nombre Completo</label>
                <input type="text" name="nombre" required>

                <label>Edad</label>
                <input type="number" name="edad" min="0" max="120" required>

                <label>Teléfono</label>
                <input type="text" name="telefono" required>

                <label>No. de Seg. Social</label>
                <input type="text" name="no_seg_social" maxlength="20" required>

                <label>Fecha de Nacimiento</label>
                <input type="date" name="fecha_nacimiento" required>

                <label>Domicilio</label>
                <input type="text" name="domicilio" maxlength="255" required>

                <label>Colonia / Localidad</label>
                <input type="text" name="colonia_localidad" maxlength="50" required>

                <label>Entidad Federativa</label>
                <input type="text" name="entidad_federativa" maxlength="255" required>

                <label>Consultorio No.</label>
                <input type="text" name="consultorio_no" maxlength="20" required>

                <label>Horario</label>
                <select name="horario" required>
                    <option value="">Seleccione...</option>
                    <option value="M">M - Matutino</option>
                    <option value="V">V - Vespertino</option>
                </select>

                <button type="submit" name="guardar">
                    Guardar Paciente
                </button>

            </form>

        </div>

        <div class="table-card">

            <h2>Pacientes Registrados</h2>

            <div class="table-scroll">

            <table class="tabla-pacientes">

                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Edad</th>
                    <th>Teléfono</th>
                    <th>No. Seg. Social</th>
                    <th>Fecha Nac.</th>
                    <th>Domicilio</th>
                    <th>Colonia / Localidad</th>
                    <th>Entidad Federativa</th>
                    <th>Consultorio</th>
                    <th>Horario</th>
                </tr>

                <?php

                $resultado = mysqli_query($conn, "SELECT * FROM pacientes");

                while($fila = mysqli_fetch_assoc($resultado)){
                    echo "
                    <tr>
                        <td>".htmlspecialchars($fila['id'])."</td>
                        <td>".htmlspecialchars($fila['nombre'])."</td>
                        <td>".htmlspecialchars($fila['edad'])."</td>
                        <td>".htmlspecialchars($fila['telefono'])."</td>
                        <td>".htmlspecialchars($fila['no_seg_social'] ?? '')."</td>
                        <td>".htmlspecialchars($fila['fecha_nacimiento'] ?? '')."</td>
                        <td>".htmlspecialchars($fila['domicilio'] ?? '')."</td>
                        <td>".htmlspecialchars($fila['colonia_localidad'] ?? '')."</td>
                        <td>".htmlspecialchars($fila['entidad_federativa'] ?? '')."</td>
                        <td>".htmlspecialchars($fila['consultorio_no'] ?? '')."</td>
                        <td>".htmlspecialchars($fila['horario'] ?? '')."</td>
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