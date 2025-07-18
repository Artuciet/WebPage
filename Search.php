<?php
// search.php

// Este archivo es incluido por index.php y se encarga de procesar la búsqueda
// y mostrar los resultados.
// No debe contener etiquetas <html>, <head>, <body>, etc. ya que es parte de un documento HTML existente.
// Solo incluye la lógica de PHP para la consulta a la base de datos y el HTML para los resultados.

// --- Configuración de la Conexión a la Base de Datos ---
// Se crea una nueva instancia de mysqli para conectar a la base de datos.
// Los parámetros son: servidor (localhost), usuario (root), contraseña (vacía), nombre de la BD (libreria_db).
$conexion = new mysqli("localhost", "root", "", "libreria_db");

// Verifica si hubo un error en la conexión a la base de datos.
if ($conexion->connect_error) {
    // Si hay un error, muestra un mensaje de alerta de Bootstrap y termina la ejecución.
    echo "<div class='alert alert-danger'>Error de conexión a la base de datos: " . $conexion->connect_error . "</div>";
    exit(); // Detiene el script si la conexión falla.
}

// --- Obtención y Validación de Parámetros de Búsqueda ---
// Obtiene la fecha seleccionada del parámetro 'fecha' en la URL (GET).
// Si no está presente, se asigna 'null'.
$fechaSeleccionada = $_GET['fecha'] ?? null;
// Obtiene el tipo de búsqueda ('vuelos' o 'hoteles') del parámetro 'tipo' en la URL (GET).
// Si no está presente, el valor por defecto es 'vuelos'.
$tipo = $_GET['tipo'] ?? 'vuelos';

// Si no se proporcionó una fecha seleccionada, la función termina.
// Esto evita ejecutar consultas sin un parámetro esencial.
if (!$fechaSeleccionada) {
    return; // Salir de la inclusión del archivo.
}

// --- Cálculo del Rango de Fechas ---
// La fecha seleccionada exacta para la búsqueda principal.
$fechaExacta = date('Y-m-d', strtotime($fechaSeleccionada));

// Rango de fechas para los modales (+/- 3 días).
$fechaModalInicio = date('Y-m-d', strtotime($fechaSeleccionada . ' -3 days'));
$fechaModalFin = date('Y-m-d', strtotime($fechaSeleccionada . ' +3 days'));

// Arrays para almacenar resultados de la fecha exacta y de fechas cercanas
$resultadosFechaExacta = [];
$resultadosFechasCercanas = [];

// --- Lógica Condicional para Vuelos o Hoteles (Consulta Principal) ---
$sqlPrincipal = "";
$tituloSeccion = "";

if ($tipo === 'vuelos') {
    $tituloSeccion = "✈️ Vuelos disponibles para el {$fechaExacta}";
    // Consulta SQL para vuelos: selecciona nombre_avion, fecha y precio de la tabla 'vuelos'.
    // Filtra por la fecha EXACTA.
    // Ordena los resultados por precio de forma ASCENDENTE.
    $sqlPrincipal = "SELECT nombre_avion, fecha, precio
                     FROM vuelos
                     WHERE fecha = '$fechaExacta'
                     ORDER BY precio ASC";

    // Consulta para vuelos en fechas cercanas (para el modal)
    $sqlModal = "SELECT nombre_avion, fecha, precio
                 FROM vuelos
                 WHERE fecha BETWEEN '$fechaModalInicio' AND '$fechaModalFin'
                 AND fecha != '$fechaExacta'
                 ORDER BY fecha ASC, precio ASC";

} elseif ($tipo === 'hoteles') {
    $tituloSeccion = "🏨 Hoteles disponibles para el {$fechaExacta}";
    // Consulta SQL para hoteles: selecciona nombre_hotel, fecha y precio de la tabla 'hoteles'.
    // Filtra por la fecha EXACTA.
    // Ordena los resultados por precio de forma ASCENDENTE.
    $sqlPrincipal = "SELECT nombre_hotel, fecha, precio
                     FROM hoteles
                     WHERE fecha = '$fechaExacta'
                     ORDER BY precio ASC"; // Asumo 'precio' para hoteles también.

    // Consulta para hoteles en fechas cercanas (para el modal)
    $sqlModal = "SELECT nombre_hotel, fecha, precio
                 FROM hoteles
                 WHERE fecha BETWEEN '$fechaModalInicio' AND '$fechaModalFin'
                 AND fecha != '$fechaExacta'
                 ORDER BY fecha ASC, precio ASC";

} else {
    // Si el tipo de búsqueda no es 'vuelos' ni 'hoteles', muestra un mensaje de advertencia.
    echo "<div class='alert alert-warning'>Tipo de búsqueda no válido.</div>";
    $conexion->close(); // Cierra la conexión a la base de datos antes de terminar.
    return; // Salir de la inclusión del archivo.
}

// --- Ejecución de la Consulta Principal ---
$resultadoPrincipal = $conexion->query($sqlPrincipal);
if ($resultadoPrincipal) {
    while ($row = $resultadoPrincipal->fetch_assoc()) {
        $resultadosFechaExacta[] = $row;
    }
}

// --- Ejecución de la Consulta para Fechas Cercanas (Modal) ---
$resultadoModal = $conexion->query($sqlModal);
if ($resultadoModal) {
    while ($row = $resultadoModal->fetch_assoc()) {
        $resultadosFechasCercanas[] = $row;
    }
}
?>

<div class="container py-5">
    <h2 class="mb-4 text-center"><?= $tituloSeccion ?></h2>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php
        // Verifica si la consulta principal arrojó algún resultado para la fecha exacta.
        if (count($resultadosFechaExacta) > 0) {
            // Itera sobre cada fila de resultados obtenida para la fecha exacta.
            foreach ($resultadosFechaExacta as $row) {
                ?>
                <div class="col">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body d-flex flex-column">
                            <?php if ($tipo === 'vuelos'): ?>
                                <h5 class="card-title text-primary"><?= htmlspecialchars($row['nombre_avion']) ?></h5>
                                <p class="card-text text-muted mb-1">Fecha: <strong><?= htmlspecialchars($row['fecha']) ?></strong></p>
                                <?php
                                // Generación de datos aleatorios para la hora de salida, duración y hora de llegada.
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
                                // Generación de una ubicación aleatoria (idealmente, esto vendría de la DB).
                                $ubicacionAleatoria = ["Ciudad de México", "Cancún", "Guadalajara", "Monterrey", "Playa del Carmen"][array_rand(["Ciudad de México", "Cancún", "Guadalajara", "Monterrey", "Playa del Carmen"])];
                                ?>
                                <p class="card-text mb-3">Ubicación: <strong><?= $ubicacionAleatoria ?></strong></p>
                            <?php endif; ?>

                            <div class="mt-auto d-flex justify-content-between align-items-center pt-3 border-top">
                                <h4 class="text-dark mb-0"><strong>$<?= number_format($row['precio'], 2) ?></strong></h4>
                                <button class="btn btn-primary">Seleccionar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            // Si no se encontraron resultados para la fecha exacta, muestra un mensaje informativo.
            echo "<div class='col-12 text-center'><div class='alert alert-info'>No hay " . ($tipo === 'vuelos' ? "vuelos" : "hoteles") . " disponibles para el {$fechaExacta}.</div></div>";
        }
        ?>
    </div>

    <?php
    // Si hay resultados para fechas cercanas, muestra un botón para abrir el modal.
    if (count($resultadosFechasCercanas) > 0) {
        ?>
        <div class="text-center mt-4">
            <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#fechasCercanasModal">
                Ver más <?= ($tipo === 'vuelos' ? "vuelos" : "hoteles") ?> en fechas cercanas
            </button>
        </div>

        <div class="modal fade" id="fechasCercanasModal" tabindex="-1" aria-labelledby="fechasCercanasModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="fechasCercanasModalLabel">
                            <?= ($tipo === 'vuelos' ? "Vuelos" : "Hoteles") ?> disponibles entre <?= $fechaModalInicio ?> y <?= $fechaModalFin ?>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row row-cols-1 g-3">
                            <?php
                            foreach ($resultadosFechasCercanas as $row) {
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
                                                $ubicacionAleatoria = ["Ciudad de México", "Cancún", "Guadalajara", "Monterrey", "Playa del Carmen"][array_rand(["Ciudad de México", "Cancún", "Guadalajara", "Monterrey", "Playa del Carmen"])];
                                                ?>
                                                <p class="card-text mb-3">Ubicación: <strong><?= $ubicacionAleatoria ?></strong></p>
                                            <?php endif; ?>

                                            <div class="mt-auto d-flex justify-content-between align-items-center pt-3 border-top">
                                                <h4 class="text-dark mb-0"><strong>$<?= number_format($row['precio'], 2) ?></strong></h4>
                                                <button class="btn btn-primary">Seleccionar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
        <?php
    } elseif (count($resultadosFechaExacta) > 0) { // Solo si hay resultados en la fecha exacta pero no en las cercanas
         echo "<div class='col-12 text-center mt-4'><div class='alert alert-info'>No hay " . ($tipo === 'vuelos' ? "vuelos" : "hoteles") . " adicionales en los días cercanos.</div></div>";
    }
    ?>
</div>

<?php
$conexion->close();
?>