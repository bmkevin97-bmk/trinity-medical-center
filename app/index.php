<?php
include("auth.php");
include("conexion.php");
date_default_timezone_set('America/Merida');

function filas($c,$sql){ try{ $r=mysqli_query($c,$sql); return $r?mysqli_fetch_all($r,MYSQLI_ASSOC):[]; }catch(Throwable $e){ return []; } }
function uno($c,$sql){ $f=filas($c,$sql); return $f?(int)array_values($f[0])[0]:0; }
function h($t){ return htmlspecialchars((string)$t); }

$hora=(int)date('G');
$saludo=$hora<12?'BUENOS DÍAS':($hora<19?'BUENAS TARDES':'BUENAS NOCHES');

$pac=uno($conn,"SELECT COUNT(*) FROM pacientes");
$med=uno($conn,"SELECT COUNT(*) FROM medicos");
$esp=uno($conn,"SELECT COUNT(DISTINCT especialidad) FROM medicos");
$hoy=uno($conn,"SELECT COUNT(*) FROM citas WHERE DATE(fecha)='".date('Y-m-d')."'");

/* Camas */
$areas=filas($conn,"SELECT area, COUNT(*) t, SUM(estado='Ocupada') o FROM camas GROUP BY area");
$camT=array_sum(array_column($areas,'t')); $camO=(int)array_sum(array_column($areas,'o'));
$camD=$camT-$camO; $pctO=$camT?round($camO/$camT*100):0;

/* Farmacia */
$meds=uno($conn,"SELECT COUNT(*) FROM medicamentos");
$bajo=uno($conn,"SELECT COUNT(*) FROM medicamentos WHERE stock <= stock_minimo");
$menos=filas($conn,"SELECT nombre, stock, stock_minimo FROM medicamentos ORDER BY stock - stock_minimo ASC LIMIT 4");

/* Citas de los últimos 7 días */
$dn=['Dom','Lun','Mar','Mié','Jue','Vie','Sáb']; $sem=[]; $lab=[];
for($i=6;$i>=0;$i--){ $t=strtotime("-$i days"); $sem[date('Y-m-d',$t)]=0; $lab[]=$dn[date('w',$t)]; }
foreach(filas($conn,"SELECT DATE(fecha) d, COUNT(*) c FROM citas WHERE fecha >= '".date('Y-m-d',strtotime('-6 days'))."' GROUP BY d") as $f){
    if(isset($sem[$f['d']])) $sem[$f['d']]=(int)$f['c'];
}

/* Especialidades */
$dep=filas($conn,"SELECT m.especialidad k, COUNT(c.id) c FROM medicos m LEFT JOIN citas c ON c.medico_id=m.id WHERE m.especialidad<>'' GROUP BY m.especialidad ORDER BY c DESC LIMIT 6");
$maxDep=max(1,(int)max(array_merge([0],array_column($dep,'c'))));

/* Citas recientes */
$citas=filas($conn,"SELECT p.nombre pac, m.nombre med, m.especialidad esp, c.fecha FROM citas c INNER JOIN pacientes p ON c.paciente_id=p.id INNER JOIN medicos m ON c.medico_id=m.id ORDER BY c.fecha DESC LIMIT 6");

$J=JSON_HEX_TAG|JSON_HEX_AMP|JSON_UNESCAPED_UNICODE;
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Trinity Medical Center</title>
<link rel="stylesheet" href="panel.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
</head>
<body class="page-panel">
<div class="app">

<aside class="side">
  <div class="brand"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><path d="M12 8v8M8 12h8"/></svg>
    <div class="lbl"><b>Trinity</b><small>Medical Center</small></div></div>
  <nav>
    <a href="index.php" class="on" title="Dashboard"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/></svg><span class="lbl">Dashboard</span></a>
    <a href="pacientes.php" title="Pacientes"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg><span class="lbl">Pacientes</span></a>
    <a href="medicos.php" title="Médicos"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4.8 2.3A.3.3 0 1 0 5 2H4a2 2 0 0 0-2 2v5a6 6 0 0 0 6 6 6 6 0 0 0 6-6V4a2 2 0 0 0-2-2h-1a.2.2 0 1 0 .3.3"/><path d="M8 15v1a6 6 0 0 0 6 6 6 6 0 0 0 6-6v-4"/><circle cx="20" cy="10" r="2"/></svg><span class="lbl">Médicos</span></a>
    <a href="citas.php" title="Citas"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="3"/><path d="M16 2v4M8 2v4M3 10h18"/></svg><span class="lbl">Citas</span></a>
    <a href="historial.php" title="Historial Clínico"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="8" y="2" width="8" height="4" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M9 12h6M9 16h6"/></svg><span class="lbl">Historial Clínico</span></a>
    <a href="camas.php" title="Camas"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 4v16M2 8h18a2 2 0 0 1 2 2v10M2 17h20M6 8v9"/></svg><span class="lbl">Camas</span></a>
    <a href="farmacia.php" title="Farmacia"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m10.5 20.5 10-10a4.95 4.95 0 1 0-7-7l-10 10a4.95 4.95 0 1 0 7 7Z"/><path d="m8.5 8.5 7 7"/></svg><span class="lbl">Farmacia</span></a>
  </nav>
  <div class="user">
    <div class="info lbl"><b><?php echo h($_SESSION['usuario']); ?></b>Administrador</div>
    <a href="logout.php" class="out btn-logout" title="Cerrar sesión"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/></svg><span class="lbl">Cerrar sesión</span></a>
  </div>
</aside>

<main class="main"><div class="g">

  <section class="card hero s8">
    <div>
      <span class="eyebrow"><?php echo $saludo; ?></span>
      <h1>Trinity Medical Center</h1>
      <p>Innovación, Especialidad y Medicina de Precisión.</p>
      <p style="margin-top:10px"><?php echo $hoy; ?> citas hoy · <?php echo $camD; ?> camas disponibles · <?php echo $bajo; ?> medicamentos con stock bajo</p>
      <a href="citas.php" class="btn p">Ver citas</a><a href="camas.php" class="btn o">Ver camas</a>
    </div>
    <img src="imagenes/MTC.jpeg" alt="Trinity Medical Center">
  </section>

  <section class="card s4">
    <h3>Farmacia</h3>
    <div class="row"><span><?php echo $meds; ?> medicamentos</span><b class="<?php echo $bajo?'red':''; ?>"><?php echo $bajo; ?> con stock bajo</b></div><br>
    <?php foreach($menos as $m){ $pc=$m['stock_minimo']>0?min(100,round($m['stock']/($m['stock_minimo']*2)*100)):100; $lo=$m['stock']<=$m['stock_minimo']; ?>
      <div class="row"><span><?php echo h($m['nombre']); ?></span><span><?php echo (int)$m['stock']; ?> / mín. <?php echo (int)$m['stock_minimo']; ?></span></div>
      <div class="bar"><i class="<?php echo $lo?'r':''; ?>" style="width:<?php echo $pc; ?>%"></i></div>
    <?php } if(!$menos) echo "<p class='empty'>Sin medicamentos registrados.</p>"; ?>
  </section>

  <div class="kpis s8">
    <div class="card kpi"><small>Total de pacientes</small><b><?php echo $pac; ?></b><span>registrados</span></div>
    <div class="card kpi"><small>Camas disponibles</small><b><?php echo $camD; ?></b><span>de <?php echo $camT; ?> camas</span></div>
    <div class="card kpi"><small>Médicos</small><b><?php echo $med; ?></b><span><?php echo $esp; ?> especialidades</span></div>
    <div class="card kpi"><small>Citas hoy</small><b class="<?php echo $hoy?'':''; ?>"><?php echo $hoy; ?></b><span>programadas</span></div>
  </div>

  <section class="card s4 rs2">
    <h3>Especialidades</h3>
    <?php foreach($dep as $d){ ?>
      <div class="row"><span><?php echo h($d['k']); ?></span><b><?php echo (int)$d['c']; ?> citas</b></div>
      <div class="bar"><i style="width:<?php echo round($d['c']/$maxDep*100); ?>%"></i></div>
    <?php } if(!$dep) echo "<p class='empty'>Sin especialidades registradas.</p>"; ?>
  </section>

  <section class="card s4">
    <h3>Tendencia de citas · 7 días</h3>
    <div class="cv"><canvas id="cSem"></canvas></div>
  </section>

  <section class="card s4">
    <h3>Ocupación de camas</h3>
    <?php if($camT){ ?>
    <div class="donut">
      <div class="cv"><canvas id="cCam"></canvas><div class="mid"><?php echo $pctO; ?>%</div></div>
      <div style="flex:1">
        <div class="row"><span>Ocupadas</span><b><?php echo $camO; ?></b></div><br>
        <div class="row"><span>Disponibles</span><b><?php echo $camD; ?></b></div>
      </div>
    </div>
    <div class="chips"><?php foreach($areas as $a){ ?><div><b><?php echo (int)$a['o'].'/'.(int)$a['t']; ?></b><?php echo h($a['area']); ?></div><?php } ?></div>
    <?php } else echo "<p class='empty'>Aún no hay camas registradas. Agrégalas en la sección Camas.</p>"; ?>
  </section>

  <section class="card s12">
    <h3>Citas recientes</h3>
    <table class="t">
      <tr><th>Fecha</th><th>Hora</th><th>Paciente</th><th>Médico</th><th>Especialidad</th></tr>
      <?php foreach($citas as $c){ $t=strtotime($c['fecha']); ?>
        <tr><td><?php echo date('d/m/Y',$t); ?></td><td><?php echo date('H:i',$t); ?></td>
        <td><?php echo h($c['pac']); ?></td><td><?php echo h($c['med']); ?></td><td><?php echo h($c['esp']); ?></td></tr>
      <?php } if(!$citas) echo "<tr><td colspan='5' class='empty'>No hay citas registradas</td></tr>"; ?>
    </table>
  </section>

</div></main>
</div>

<script>
Chart.defaults.font.family='Poppins, sans-serif';
new Chart('cSem',{type:'line',data:{labels:<?php echo json_encode($lab,$J); ?>,datasets:[{data:<?php echo json_encode(array_values($sem)); ?>,
 borderColor:'#0f766e',borderWidth:2,tension:.4,fill:true,pointRadius:3,
 backgroundColor:(c)=>{const a=c.chart.chartArea; if(!a) return 'rgba(15,118,110,.15)'; const g=c.chart.ctx.createLinearGradient(0,a.top,0,a.bottom); g.addColorStop(0,'rgba(15,118,110,.35)'); g.addColorStop(1,'rgba(15,118,110,0)'); return g;}}]},
 options:{maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{x:{grid:{display:false}},y:{beginAtZero:true,ticks:{precision:0}}}}});
<?php if($camT){ ?>
new Chart('cCam',{type:'doughnut',data:{labels:['Ocupadas','Disponibles'],datasets:[{data:[<?php echo $camO.','.$camD; ?>],backgroundColor:['#0f766e','#dbe6e6'],borderWidth:0}]},
 options:{maintainAspectRatio:false,cutout:'68%',plugins:{legend:{display:false}}}});
<?php } ?>
</script>
<script src="sin-historial.js"></script>
</body>
</html>