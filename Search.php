<?php
$conexion = new mysqli("localhost", "root", "", "libreria_db");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

/*
$fechaSeleccionada = $_GET['fecha'] ?? null;
$tipo = $_GET['tipo'] ?? 'vuelos';
if (!$fechaSeleccionada) {
    die("<div class='alert alert-danger'>Debes seleccionar una fecha para buscar vuelos.</div>");
}
*/

$fechaSeleccionada = $_GET['fecha'] ?? null;
$tipo = $_GET['tipo'] ?? 'vuelos';


$fechaInicio = date('Y-m-d', strtotime($fechaSeleccionada . ' -4 days'));
$fechaFin = date('Y-m-d', strtotime($fechaSeleccionada . ' +4 days'));

$sql = "SELECT nombre_avion, fecha, precio 
        FROM vuelos 
        WHERE fecha BETWEEN '$fechaInicio' AND '$fechaFin' 
        ORDER BY fecha ASC";

$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Comparador de Vuelos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-5">
    <h2 class="mb-4 text-center">✈️ Vuelos disponibles entre <?= $fechaInicio ?> y <?= $fechaFin ?></h2>

    <table class="table table-bordered table-hover align-middle">
      <thead class="table-dark">
        <tr>
          <th>Fecha</th>
          <th>Avión</th>
          <th>Duración estimada</th>
          <th>Hora de salida</th>
          <th>Hora de llegada</th>
          <th>Precio</th>
          <th>Acción</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if ($resultado->num_rows > 0) {
          while ($row = $resultado->fetch_assoc()) {
            $horaSalida = rand(5, 22) . ":" . str_pad(rand(0, 59), 2, "0", STR_PAD_LEFT);
            $duracion = rand(2, 6) . "h " . rand(0, 59) . "m";
            $horaLlegada = date('H:i', strtotime($horaSalida . " +$duracion"));
            echo "<tr>";
            echo "<td>{$row['fecha']}</td>";
            echo "<td>{$row['nombre_avion']}</td>";
            echo "<td>$duracion</td>";
            echo "<td>$horaSalida</td>";
            echo "<td>$horaLlegada</td>";
            echo "<td><strong>$" . number_format($row['precio'], 2) . "</strong></td>";
            echo "<td><button class='btn btn-primary btn-sm'>Seleccionar</button></td>";
            echo "</tr>";
          }
        } else {
          echo "<tr><td colspan='7' class='text-center text-muted'>No hay vuelos disponibles en ese rango de fechas.</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
</body>
</html>