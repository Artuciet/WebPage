<?php
// Configuración de la conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "libreria_db";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener input del usuario (ejemplo: "Maracaibo-Caracas/20-07-2025")
$input = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';
$datosBusqueda = procesarInput($input);

// Función para procesar el input
function procesarInput($input) {
    $partes = explode("/", $input);
    if (count($partes) != 2) {
        return false;
    }
    
    $rutas = explode("-", $partes[0]);
    if (count($rutas) != 2) {
        return false;
    }
    
    return [
        'origen' => trim($rutas[0]),
        'destino' => trim($rutas[1]),
        'fecha' => DateTime::createFromFormat('d-m-Y', trim($partes[1]))
    ];
}

if (!$datosBusqueda) {
    die("Formato de búsqueda incorrecto. Use: Origen-Destino/dd-mm-aaaa");
}

// Preparar rango de fechas (4 días antes y después)
$fechaCentral = $datosBusqueda['fecha'];
$fechaInicio = clone $fechaCentral;
$fechaInicio->modify('-4 days');
$fechaFin = clone $fechaCentral;
$fechaFin->modify('+4 days');

// Consulta SQL para obtener los vuelos en el rango de fechas
$sql = "SELECT fecha, MIN(precio) as precio_min, MAX(precio) as precio_max, 
               AVG(precio) as precio_promedio
        FROM vuelos 
        WHERE origen = ? AND destino = ? 
          AND fecha BETWEEN ? AND ?
        GROUP BY fecha
        ORDER BY fecha";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", 
    $datosBusqueda['origen'], 
    $datosBusqueda['destino'],
    $fechaInicio->format('Y-m-d'),
    $fechaFin->format('Y-m-d')
);
$stmt->execute();
$result = $stmt->get_result();

// Crear matriz de resultados
$matrizPrecios = [];
while ($row = $result->fetch_assoc()) {
    $matrizPrecios[$row['fecha']] = [
        'min' => $row['precio_min'],
        'max' => $row['precio_max'],
        'promedio' => $row['precio_promedio']
    ];
}

// Cerrar conexión
$stmt->close();
$conn->close();

// Función para resaltar el día más económico
function encontrarMejorPrecio($matriz) {
    $mejorPrecio = null;
    $mejorFecha = null;
    
    foreach ($matriz as $fecha => $precios) {
        if ($mejorPrecio === null || $precios['min'] < $mejorPrecio) {
            $mejorPrecio = $precios['min'];
            $mejorFecha = $fecha;
        }
    }
    
    return ['fecha' => $mejorFecha, 'precio' => $mejorPrecio];
}

$mejorOpcion = encontrarMejorPrecio($matrizPrecios);
?>

<!DOCTYPE html>
<html lang="es">
<div class="container-fluid">
    <h4 class="text-center mb-4">Análisis de Precios para vuelos <?= htmlspecialchars($datosBusqueda['origen']) ?> a <?= htmlspecialchars($datosBusqueda['destino']) ?></h4>
    
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>Fecha</th>
                    <th>Precio Mínimo</th>
                    <th>Precio Máximo</th>
                    <th>Precio Promedio</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($matrizPrecios as $fecha => $precios): ?>
                    <tr class="<?= ($fecha == $mejorOpcion['fecha']) ? 'table-success' : '' ?> <?= ($fecha == $fechaCentral->format('Y-m-d')) ? 'table-warning' : '' ?>">
                        <td><?= date('d-m-Y', strtotime($fecha)) ?></td>
                        <td>$<?= number_format($precios['min'], 2) ?></td>
                        <td>$<?= number_format($precios['max'], 2) ?></td>
                        <td>$<?= number_format($precios['promedio'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <?php if ($mejorOpcion['fecha']): ?>
        <div class="alert alert-info mt-3">
            <p class="mb-1"><strong>Mejor opción:</strong> <?= date('d-m-Y', strtotime($mejorOpcion['fecha'])) ?> 
            con un precio de <strong>$<?= number_format($mejorOpcion['precio'], 2) ?></strong></p>
            <?php if ($mejorOpcion['fecha'] != $fechaCentral->format('Y-m-d')): ?>
                <p class="mb-0">Ahorras $<?= number_format($matrizPrecios[$fechaCentral->format('Y-m-d')]['min'] - $mejorOpcion['precio'], 2) ?> 
                comparado con el día <?= $fechaCentral->format('d-m-Y') ?></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
</html>