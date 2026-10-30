<?php
include("auth.php");
include("conexion.php");

if(isset($_POST['guardar'])){
    $nombre = trim($_POST['nombre']); $stock = max(0,(int)$_POST['stock']); $min = max(0,(int)$_POST['stock_minimo']);
    $s = mysqli_prepare($conn, "INSERT INTO medicamentos(nombre, stock, stock_minimo) VALUES(?, ?, ?)");
    mysqli_stmt_bind_param($s, "sii", $nombre, $stock, $min);
    mysqli_stmt_execute($s);
    header("Location: farmacia.php"); exit();
}
if(isset($_POST['actualizar'])){
    $id = (int)$_POST['id']; $stock = max(0,(int)$_POST['stock']);
    $s = mysqli_prepare($conn, "UPDATE medicamentos SET stock = ? WHERE id = ?");
    mysqli_stmt_bind_param($s, "ii", $stock, $id);
    mysqli_stmt_execute($s);
    header("Location: farmacia.php"); exit();
}
if(isset($_POST['eliminar'])){
    $id = (int)$_POST['id'];
    mysqli_query($conn, "DELETE FROM medicamentos WHERE id = $id");
    header("Location: farmacia.php"); exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmacia</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php $activa = 'farmacia'; include("menu.php"); ?>
<div class="container">

  <div class="top-bar">
    <a href="index.php" class="btn-volver">← Panel Principal</a>
    <h1>Farmacia</h1>
  </div>

  <div class="panel">
    <div class="form-card">
        <h2>Registrar Medicamento</h2>
        <form method="POST">
            <label>Nombre</label>
            <input type="text" name="nombre" maxlength="100" required>

            <label>Stock actual</label>
            <input type="number" name="stock" min="0" required>

            <label>Stock mínimo (alerta)</label>
            <input type="number" name="stock_minimo" min="0" value="10" required>

            <button type="submit" name="guardar">Guardar Medicamento</button>
        </form>
    </div>

    <div class="table-card">
        <h2>Inventario</h2>
        <div class="table-scroll">
        <table>
            <tr><th>ID</th><th>Medicamento</th><th>Stock</th><th>Mínimo</th><th>Estado</th><th>Acciones</th></tr>
            <?php
            $r = mysqli_query($conn, "SELECT * FROM medicamentos ORDER BY stock - stock_minimo ASC");
            while($f = mysqli_fetch_assoc($r)){
                $bajo = $f['stock'] <= $f['stock_minimo'];
                echo "<tr>
                    <td>".(int)$f['id']."</td>
                    <td>".htmlspecialchars($f['nombre'])."</td>
                    <td>".(int)$f['stock']."</td>
                    <td>".(int)$f['stock_minimo']."</td>
                    <td><span class='badge ".($bajo?'bad':'ok')."'>".($bajo?'Stock bajo':'Suficiente')."</span></td>
                    <td>
                      <form method='POST' class='inline-form'>
                        <input type='hidden' name='id' value='".(int)$f['id']."'>
                        <input type='number' name='stock' min='0' value='".(int)$f['stock']."' required>
                        <button type='submit' name='actualizar'>Actualizar</button>
                      </form>
                      <form method='POST' class='inline-form' onsubmit=\"return confirm('¿Eliminar este medicamento?')\">
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