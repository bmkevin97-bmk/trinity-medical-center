<?php
// Menú lateral compartido (se incluye en cada página justo después de la etiqueta body)
// Si alguien abre menu.php directamente en el navegador, lo manda a la página de inicio
if (realpath($_SERVER['SCRIPT_FILENAME']) === __FILE__) {
    header("Location: index.php");
    exit();
}

$activa = $activa ?? '';
$ic = function($p){ return '<svg viewBox="0 0 24 24" aria-hidden="true">'.$p.'</svg>'; };
$items = [
 ['index.php','dashboard','Dashboard','<rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/>'],
 ['pacientes.php','pacientes','Pacientes','<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>'],
 ['medicos.php','medicos','Médicos','<path d="M4.8 2.3A.3.3 0 1 0 5 2H4a2 2 0 0 0-2 2v5a6 6 0 0 0 6 6 6 6 0 0 0 6-6V4a2 2 0 0 0-2-2h-1a.2.2 0 1 0 .3.3"/><path d="M8 15v1a6 6 0 0 0 6 6 6 6 0 0 0 6-6v-4"/><circle cx="20" cy="10" r="2"/>'],
 ['citas.php','citas','Citas','<rect x="3" y="4" width="18" height="18" rx="3"/><path d="M16 2v4M8 2v4M3 10h18"/>'],
 ['historial.php','historial','Historial Clínico','<rect x="8" y="2" width="8" height="4" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M9 12h6M9 16h6"/>'],
 ['camas.php','camas','Camas','<path d="M2 4v16M2 8h18a2 2 0 0 1 2 2v10M2 17h20M6 8v9"/>'],
 ['farmacia.php','farmacia','Farmacia','<path d="m10.5 20.5 10-10a4.95 4.95 0 1 0-7-7l-10 10a4.95 4.95 0 1 0 7 7Z"/><path d="m8.5 8.5 7 7"/>'],
];
?>
<aside class="side">
  <div class="brand"><?php echo $ic('<rect x="3" y="3" width="18" height="18" rx="5"/><path d="M12 8v8M8 12h8"/>'); ?>
    <div class="lbl"><b>Trinity</b><small>Medical Center</small></div></div>
  <nav>
    <?php foreach($items as $i){ ?>
      <a href="<?php echo $i[0]; ?>" title="<?php echo $i[2]; ?>"<?php echo $activa===$i[1]?' class="on"':''; ?>><?php echo $ic($i[3]); ?><span class="lbl"><?php echo $i[2]; ?></span></a>
    <?php } ?>
  </nav>
  <div class="user">
    <div class="info lbl"><b><?php echo htmlspecialchars($_SESSION['usuario'] ?? ''); ?></b>Administrador</div>
    <a href="logout.php" class="out btn-logout" title="Cerrar sesión"><?php echo $ic('<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/>'); ?><span class="lbl">Cerrar sesión</span></a>
  </div>
</aside>