<?php

include("conexion.php");

if(isset($_GET['id'])){

    $id = $_GET['id'];

    mysqli_query(
        $conn,
        "DELETE FROM citas WHERE id = $id"
    );

}

header("Location: citas.php");
exit();

?>