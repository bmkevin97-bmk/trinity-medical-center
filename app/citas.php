<?php
session_start();

if(!isset($_SESSION['usuario'])){
    header("Location: login.php");
    exit();
}
include("conexion.php");

if(isset($_POST['guardar'])){

    $paciente = $_POST['paciente'];
    $medico = $_POST['medico'];
    $fecha = $_POST['fecha'];

    $sql = "INSERT INTO citas(paciente_id, medico_id, fecha)
            VALUES('$paciente','$medico','$fecha')";

    mysqli_query($conn, $sql);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda Hospitalaria</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
       <?php $activa = 'citas'; include("menu.php"); ?>

      <div class="top-bar">

       <a href="index.php" class="btn-volver">
         ← Panel Principal
      </a>

      <h1>Gestión de Citas</h1>

     </div>

     <div class="panel">

        <div class="form-card">

            <h2>Programar Cita</h2>

            <form method="POST">

                <label>Paciente</label>

                <select name="paciente" required>
                    <?php

                    $pacientes = mysqli_query($conn, "SELECT * FROM pacientes");

                    while($p = mysqli_fetch_assoc($pacientes)){
                        echo "<option value='".$p['id']."'>".$p['nombre']."</option>";
                    }

                    ?>
                </select>

                <label>Médico</label>

                <select name="medico" required>
                    <?php

                    $medicos = mysqli_query($conn, "SELECT * FROM medicos");

                    while($m = mysqli_fetch_assoc($medicos)){
                        echo "<option value='".$m['id']."'>".$m['nombre']."</option>";
                    }

                    ?>
                </select>

                <label>Fecha y Hora</label>

                <input type="datetime-local" name="fecha" required>

                <button type="submit" name="guardar">
                    Agendar Cita
                </button>

            </form>

        </div>

        <div class="table-card">

            <h2>Citas Programadas</h2>

<table>

    <tr>
        <th>Paciente</th>
        <th>Médico</th>
        <th>Fecha</th>
        <th>Acciones</th>
    </tr>

    <?php

    $sql = "
    SELECT
        citas.id,
        pacientes.nombre AS paciente,
        medicos.nombre AS medico,
        citas.fecha
    FROM citas
    INNER JOIN pacientes
        ON citas.paciente_id = pacientes.id
    INNER JOIN medicos
        ON citas.medico_id = medicos.id
    ORDER BY citas.fecha ASC
    ";

    $resultado = mysqli_query($conn, $sql);

    while($fila = mysqli_fetch_assoc($resultado)){

        echo "
        <tr>
            <td>".$fila['paciente']."</td>
            <td>".$fila['medico']."</td>
            <td>".$fila['fecha']."</td>
            <td>
                <a
                    href='eliminar_cita.php?id=".$fila['id']."'
                    class='btn-eliminar'
                             onclick='return confirm(\"¿Deseas cancelar esta cita?\")'
                             >
                          Cancelar
                        </a>
                     </td>
                 </tr>
               ";
             }

          ?>

           </table>

        </div>

    </div>

</div>
       <script src="sin-historial.js"></script>
</body>
</html>