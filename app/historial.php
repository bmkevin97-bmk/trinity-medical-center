<?php
session_start();

if(!isset($_SESSION['usuario'])){
    header("Location: login.php");
    exit();
}
include("conexion.php");

if(isset($_POST['guardar'])){

    $paciente = $_POST['paciente'];
    $diagnostico = $_POST['diagnostico'];
    $tratamiento = $_POST['tratamiento'];
    $fecha = $_POST['fecha'];

    $sql = "INSERT INTO historial_clinico
    (paciente_id, diagnostico, tratamiento, fecha)
    VALUES
    ('$paciente','$diagnostico','$tratamiento','$fecha')";

    mysqli_query($conn,$sql);

}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Historial Clínico</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
        <?php $activa = 'citas'; include("menu.php"); ?>
<div class="top-bar">

    <a href="index.php" class="btn-volver">
        ← Panel Principal
    </a>

    <h1>Historial Clínico</h1>

</div>

<div class="panel">

    <div class="form-card">

        <h2>Nuevo Registro Clínico</h2>

        <form method="POST">

            <label>Paciente</label>

            <select name="paciente" required>

                <?php

                $pacientes = mysqli_query(
                    $conn,
                    "SELECT * FROM pacientes"
                );

                while($p = mysqli_fetch_assoc($pacientes)){

                    echo "<option value='".$p['id']."'>"
                    .$p['nombre'].
                    "</option>";

                }

                ?>

            </select>

            <label>Diagnóstico</label>

            <textarea
                name="diagnostico"
                rows="4"
                required>
            </textarea>

            <label>Tratamiento</label>

            <textarea
                name="tratamiento"
                rows="4"
                required>
            </textarea>

            <label>Fecha</label>

            <input type="date" name="fecha" required>

            <button type="submit" name="guardar">
                Guardar Historial
            </button>

        </form>

    </div>

    <div class="table-card">

        <h2>Historial Registrado</h2>

        <table>

            <tr>
                <th>Paciente</th>
                <th>Diagnóstico</th>
                <th>Tratamiento</th>
                <th>Fecha</th>
            </tr>

            <?php

            $sql = "
            SELECT
                pacientes.nombre AS paciente,
                historial_clinico.diagnostico,
                historial_clinico.tratamiento,
                historial_clinico.fecha
            FROM historial_clinico
            INNER JOIN pacientes
                ON historial_clinico.paciente_id = pacientes.id
            ORDER BY historial_clinico.id DESC
            ";

            $resultado = mysqli_query($conn,$sql);

            while($fila = mysqli_fetch_assoc($resultado)){

                echo "
                <tr>
                    <td>".$fila['paciente']."</td>
                    <td>".$fila['diagnostico']."</td>
                    <td>".$fila['tratamiento']."</td>
                    <td>".$fila['fecha']."</td>
                </tr>
                ";

            }

            ?>

        </table>

    </div>

</div>
       <script src="sin-historial.js"></script>
</body>
</html>