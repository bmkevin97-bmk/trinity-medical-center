<?php
include("auth.php");
include("conexion.php");

$areas = ['UCI','General','Privada'];

if(isset($_POST['guardar'])){
    $numero = trim($_POST['numero']); $area = $_POST['area'];
    if(in_array($area, $areas, true)){
        $s = mysqli_prepare($conn, "INSERT INTO camas(numero, area) VALUES(?, ?)");
        mysqli_stmt_bind_param($s, "ss", $numero, $area);
        mysqli_stmt_execute($s);
    }
    header("Location: camas.php"); exit();
}
if(isset($_POST['alternar'])){
    $id = (int)$_POST['id'];
    mysqli_query($conn, "UPDATE camas SET estado = IF(estado='Ocupada','Disponible','Ocupada') WHERE id = $id");
    header("Location: camas.php"); exit();
}
if(isset($_POST['eliminar'])){
    $id = (int)$_POST['id'];
    mysqli_query($conn, "DELETE FROM camas WHERE id = $id");
    header("Location: camas.php"); exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Camas</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php $activa = 'camas'; include("menu.php"); ?>
<div class="container">

  <div class="top-bar">
    <a href="index.php" class="btn-volver">← Panel Principal</a>
    <h1>Gestión de Camas</h1>
  </div>

  <div class="panel">
    <div class="form-card">
        <h2>Registrar Cama</h2>
        <form method="POST">
            <label>Número de cama</label>
            <input type="text" name="numero" maxlength="10" required>

            <label>Área</label>
            <select name="area" required>
                <option value="">Seleccione...</option>
                <?php foreach($areas as $a){ echo "<option value='$a'>$a</option>"; } ?>
            </select>

            <button type="submit" name="guardar">Guardar Cama</button>
        </form>
    </div>

    <div class="table-card">
        <h2>Camas Registradas</h2>
        <div class="table-scroll">
        <table>
            <tr><th>ID</th><th>Cama</th><th>Área</th><th>Estado</th><th>Acciones</th></tr>
            <?php
            $r = mysqli_query($conn, "SELECT * FROM camas ORDER BY area, numero");
            while($f = mysqli_fetch_assoc($r)){
                $ocu = $f['estado'] === 'Ocupada';
                echo "<tr>
                    <td>".(int)$f['id']."</td>
                    <td>".htmlspecialchars($f['numero'])."</td>
                    <td>".htmlspecialchars($f['area'])."</td>
                    <td><span class='badge ".($ocu?'bad':'ok')."'>".htmlspecialchars($f['estado'])."</span></td>
                    <td>
                      <form method='POST' class='inline-form'>
                        <input type='hidden' name='id' value='".(int)$f['id']."'>
                        <button type='submit' name='alternar'>Cambiar estado</button>
                      </form>
                      <form method='POST' class='inline-form' onsubmit=\"return confirm('¿Eliminar esta cama?')\">
                        <input type='hidden' name='id' value='".(int)$f['id']."'>
                        <button type='submit' name='eliminar' class='btn-danger'>Eliminar</button>
                      </form>
                    </td>
                </tr>";
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