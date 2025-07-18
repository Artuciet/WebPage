<?php
// search.php

// No incluir HTML, head, body, etc. aquí.
// Solo la lógica de PHP y el HTML de los resultados.

$conexion = new mysqli("localhost", "root", "", "libreria_db");
if ($conexion->connect_error) {
    echo "<div class='alert alert-danger'>Error de conexión a la base de datos: " . $conexion->connect_error . "</div>";
    exit();
}

$fechaSeleccionada = $_GET['fecha'] ?? null;
$tipo = $_GET['tipo'] ?? 'vuelos';

if (!$fechaSeleccionada) {
    return; // Salir si no hay fecha seleccionada
}

$fechaInicio = date('Y-m-d', strtotime($fechaSeleccionada . ' -4 days'));
$fechaFin = date('Y-m-d', strtotime($fechaSeleccionada . ' +4 days'));

$sql = "";
$tituloSeccion = "";

// Lógica condicional para hoteles o vuelos
if ($tipo === 'vuelos') {
    $tituloSeccion = "✈️ Vuelos disponibles entre {$fechaInicio} y {$fechaFin}";
    $sql = "SELECT nombre_avion, fecha, precio
            FROM vuelos
            WHERE fecha BETWEEN '$fechaInicio' AND '$fechaFin'
            ORDER BY fecha ASC";

} elseif ($tipo === 'hoteles') {
    $tituloSeccion = "🏨 Hoteles disponibles entre {$fechaInicio} y {$fechaFin}";
    // Suponiendo que tienes una tabla 'hoteles' con campos:
    // id_hotel, nombre_hotel, fecha_disponible, precio_noche, ubicacion (opcional)
    $sql = "SELECT nombre_hotel, fecha, precio
            FROM hoteles
            WHERE fecha BETWEEN '$fechaInicio' AND '$fechaFin'
            ORDER BY fecha ASC";

} else {
    echo "<div class='alert alert-warning'>Tipo de búsqueda no válido.</div>";
    $conexion->close();
    return;
}

$resultado = $conexion->query($sql);

?>

<div class="container py-5">
    <h2 class="mb-4 text-center"><?= $tituloSeccion ?></h2>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php
        if ($resultado->num_rows > 0) {
            while ($row = $resultado->fetch_assoc()) {
                ?>
                <div class="col">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body d-flex flex-column">
                            <?php if ($tipo === 'vuelos'): ?>
                                <h5 class="card-title text-primary"><?= htmlspecialchars($row['nombre_avion']) ?></h5>
                                <p class="card-text text-muted mb-1">Fecha: <strong><?= htmlspecialchars($row['fecha']) ?></strong></p>
                                <?php
                                $horaSalida = rand(5, 22) . ":" . str_pad(rand(0, 59), 2, "0", STR_PAD_LEFT);
                                $duracion = rand(2, 6) . "h " . rand(0, 59) . "m";
                                $horaLlegada = date('H:i', strtotime($horaSalida . " +$duracion"));
                                ?>
                                <p class="card-text mb-1">Duración estimada: <strong><?= $duracion ?></strong></p>
                                <p class="card-text mb-1">Salida: <strong><?= $horaSalida ?></strong></p>
                                <p class="card-text mb-3">Llegada: <strong><?= $horaLlegada ?></strong></p>
                            <?php elseif ($tipo === 'hoteles'): ?>
                                <h5 class="card-title text-success"><?= htmlspecialchars($row['nombre_hotel']) ?></h5>
                                <p class="card-text text-muted mb-1">Fecha disponible: <strong><?= htmlspecialchars($row['fecha']) ?></strong></p>
                                <?php
                                // Puedes usar una ubicación real de tu DB si la tienes, o generar una
                                $ubicacionAleatoria = ["Ciudad de México", "Cancún", "Guadalajara", "Monterrey", "Playa del Carmen"][array_rand(["Ciudad de México", "Cancún", "Guadalajara", "Monterrey", "Playa del Carmen"])];
                                ?>
                                <p class="card-text mb-3">Ubicación: <strong><?= $ubicacionAleatoria ?></strong></p>
                            <?php endif; ?>

                            <div class="mt-auto d-flex justify-content-between align-items-center pt-3 border-top">
                                <h4 class="text-dark mb-0"><strong>$<?= number_format($row['precio'] ?? $row['precio_noche'], 2) ?></strong></h4>
                                <button class="btn btn-primary">Seleccionar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<div class='col-12 text-center'><div class='alert alert-info'>No hay " . ($tipo === 'vuelos' ? "vuelos" : "hoteles") . " disponibles en ese rango de fechas.</div></div>";
        }
        ?>
    </div>
</div>

<?php
$conexion->close();
?>