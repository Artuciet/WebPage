<?php
// index.php

// 1. Capturar el tema seleccionado si se envía desde un formulario/enlace
if (isset($_GET['theme'])) {
    $selectedTheme = $_GET['theme'];
    // Validar el tema para evitar inyecciones o valores no deseados
    if (in_array($selectedTheme, ['auto', 'dark', 'light'])) {
        // Guardar el tema en una cookie
        // setcookie(nombre, valor, expiracion, ruta, dominio, seguro, httponly)
        // La cookie expira en 30 días (time() + 60*60*24*30)
        setcookie('bsTheme', $selectedTheme, time() + (86400 * 30), "/"); // 86400 = 1 día
        $_COOKIE['bsTheme'] = $selectedTheme; // Actualizar $_COOKIE para usarlo inmediatamente en esta misma carga de página
    }
}

// 2. Determinar el tema a aplicar
// Primero, intentar obtener el tema de la cookie
// Si no hay cookie, el tema por defecto será 'light'
$currentTheme = $_COOKIE['bsTheme'] ?? 'light'; 

// Si el valor de la cookie no es válido (ej. alguien lo manipuló), revertir al predeterminado
if (!in_array($currentTheme, ['auto', 'dark', 'light'])) {
    $currentTheme = 'light';
}
?>

<!DOCTYPE html>
<!-- Comienzo del HTML5 para visualizacion del codigo en pestaña -->
<html lang="en" data-bs-theme="<?= htmlspecialchars($currentTheme) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagina Web</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <div class="container-fluid">
      <div class="row align-items-center justify-content-end p-2">
        <div class="col-auto">
          <!-- Botones de selección de Cambio de tema y métodos de pago -->
          <div class="btn-group me-2">
            <!-- Botones de selección de Cambio de tema -->
              <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Theme</button>
              <ul class="dropdown-menu" aria-labelledby="dropdownMenuButtonThemes">
                  <li><a class="dropdown-item" href="?theme=auto">Default Mode</a></li>
                  <li><a class="dropdown-item" href="?theme=dark">Dark Mode</a></li>
                  <li><a class="dropdown-item" href="?theme=light">Light Mode</a></li>
              </ul>
            </div>
            <!-- Botones de selección de cuentas bancarias en USD digitales -->
            <div class="btn-group">
              <button class="btn btn-secondary btn-sm" type="button">Payment Methods</button>
              <button type="button" class="btn btn-sm btn-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
              </button>
              <ul class="dropdown-menu">
                  <li class="dropdown-item">Binance</li>
                  <li class="dropdown-item">PayPal</li>
                  <li class="dropdown-item">AirTM</li>
                  <li class="dropdown-item">Zinli</li>
                  <li>
                    <button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#bankAccountsModal">Bank Accounts</button>
                  </li>
                </ul>
            </div>
        </div>
      </div>
      <!-- Navbar -->
      <div class="row mt-3 justify-content-end">
        <div class="col-auto">
          <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">Home</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Profile</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="pills-contact-tab" data-bs-toggle="pill" data-bs-target="#pills-contact" type="button" role="tab" aria-controls="pills-contact" aria-selected="false">Contact</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="pills-search-tab" data-bs-toggle="pill" data-bs-target="#pills-search" type="button" role="tab" aria-controls="pills-search" aria-selected="false">Buscar Vuelos/Hoteles</button>
          </li>
        </ul>
        </div>
      </div>
      <div class="row mt-3 justify-content-end">
        <div class="tab-content" id="pills-tabContent">
          <!-- Contenido del Home o Main -->
          <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab" tabindex="0">
            <div class="container text-center mt-3">
              <div class="container text-center mt-3">
                <div class="row justify-content-center">
                  <div class="col-md-6">
                    <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel" data-bs-touch="false" data-bs-interval="5000">
                      <div class="carousel-indicators">
                        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
                        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
                      </div>
                      <div class="carousel-inner rounded shadow">
                        <div class="carousel-item active">
                          <img src="Res/GymmeterLogo.png" class="d-block w-100" style="max-height: 400px; object-fit: contain;" alt="Gymmeter">
                        </div>
                        <div class="carousel-item">
                          <img src="Res/ReconFaceLogo.PNG" class="d-block w-100" style="max-height: 400px; object-fit: contain;" alt="ReconFace">
                        </div>
                        <div class="carousel-item">
                          <img src="Res/GymAppLogo.png" class="d-block w-100" style="max-height: 400px; object-fit: contain;" alt="GymApp">
                        </div>
                      </div>
                      <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                      </button>
                      <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- Contenido del Perfil -->
          <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab" tabindex="0">
            texto 2
          </div>
          <!-- Contenido del Contacto -->
          <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab" tabindex="0">
            <button class="btn btn-primary bg-secondary mb-2">
              <a href="https://eight-zinc-fab.notion.site/Adri-n-Arturo-Hern-ndez-Garc-a-c678597155b349d8a82e7921b0e90a1d?source=copy_link" class="text-bg-secondary d-flex align-items-center">
                Perfil Profesional (Notion)
              </a>
            </button>
            <div class="mb-2">
              <button class="btn btn-primary bg-success">
                <a href="https://wa.link/ii2hfw" class="text-bg-success d-flex align-items-center">
                  <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/6b/WhatsApp.svg/1200px-WhatsApp.svg.png" alt="Logo WhatsApp" style="width: 24px; height: 24px; margin-right: 8px;">
                  WhatsApp
                </a>
              </button>
            </div>
            <!-- Boton de contacto por Telegram -->
            <div class="mb-2">
              <button class="btn btn-primary bg-primary">
                <a href="https://t.me/Artuciet" class="text-bg-primary d-flex align-items-center">
                  <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/82/Telegram_logo.svg/1200px-Telegram_logo.svg.png" alt="Logo Telegram" style="width: 24px; height: 24px; margin-right: 8px;">
                  Telegram
                </a>
              </button>
            </div>
            <!-- Boton de contacto por Gmail -->
            <div class="mb-2">
              <button class="btn btn-primary bg-danger">
                <a href="mailto:vt.adrian.ahg@gmail.com" class="text-bg-danger d-flex align-items-center">
                  <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/45/Gmail_icon_%282020%29.svg/1200px-Gmail_icon_%282020%29.svg.png" alt="Logo Gmail" style="width: 24px; height: 24px; margin-right: 8px;">
                  Gmail: vt.adrian.ahg@gmail.com
                </a>
              </button>
            </div>
            <!-- Boton de contacto por Outlook -->
            <div class="mb-2">
              <button class="btn btn-primary bg-info">
                <a href="mailto:tu_email_outlook@outlook.com" class="text-bg-info d-flex align-items-center">
                  <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/e1/Microsoft_Outlook_new_logo.svg/1200px-Microsoft_Outlook_new_logo.svg.png" alt="Logo Outlook" style="width: 24px; height: 24px; margin-right: 8px;">
                  Email: Outlook
                </a>
              </button>
            </div>
            <!-- Botones de Facebook -->
            <div class="mb-2">
              <button class="btn btn-primary bg-primary">
                <a href="https://www.facebook.com/vt.adrian.ahg/" class="text-bg-primary d-flex align-items-center">
                  <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/51/Facebook_f_logo_%282019%29.svg/1200px-Facebook_f_logo_%282019%29.svg.png" alt="Logo Facebook" style="width: 24px; height: 24px; margin-right: 8px;">
                  FaceBook
                </a>
              </button>
            </div>
            <!-- Botones de Instagram -->
            <div class="mb-2">
              <button class="btn btn-primary" style="background-image: linear-gradient(to right top, #f09433, #e6683c, #dc2743, #cc2366, #bc1888, #b81c8b, #b51f8d, #b12390, #b12895, #b12e9b, #b033a0, #af39a6); border: none;">
                <a href="https://www.instagram.com/add_vacations/" class="text-white d-flex align-items-center">
                  <img src="https://upload.wikimedia.org/wikipedia/commons/a/a5/Instagram_icon.png" alt="Logo Instagram" style="width: 24px; height: 24px; margin-right: 8px;">
                  Instagram
                </a>
              </button>
            </div>
          </div>
          <!-- Contenido de Búsqueda -->
          <div class="tab-pane fade" id="pills-search" role="tabpanel" aria-labelledby="pills-search-tab" tabindex="0">
            <div class="container mt-5">
              <h2 class="mb-4 text-center">🔍 Encuentra tu mejor opción de viaje</h2>
              <form method="GET" action="index.php" class="row g-3" id="searchForm">
                <div class="col-md-4">
                  <label for="fecha" class="form-label">Fecha de viaje</label>
                  <input type="date" class="form-control" id="fecha" name="fecha" required
                         value="<?= isset($_GET['fecha']) ? htmlspecialchars($_GET['fecha']) : '' ?>">
                </div>
                <div class="col-md-4">
                  <label for="tipo" class="form-label">Tipo de búsqueda</label>
                  <select class="form-select" id="tipo" name="tipo">
                    <option value="vuelos" <?= (isset($_GET['tipo']) && $_GET['tipo'] == 'vuelos') ? 'selected' : '' ?>>Vuelos</option>
                    <option value="hoteles" <?= (isset($_GET['tipo']) && $_GET['tipo'] == 'hoteles') ? 'selected' : '' ?>>Hoteles</option>
                  </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                  <button type="submit" class="btn btn-primary w-100">Buscar</button>
                </div>
              </form>
              <div id="searchResults" class="mt-5">
                <?php
                if (isset($_GET['fecha']) && isset($_GET['tipo'])) {
                    include 'search.php';
                } else {
                    echo "<div class='alert alert-info text-center mt-4'>Selecciona una fecha y un tipo de búsqueda para ver los resultados.</div>";
                }
                ?>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal de Cuentas Bancarias -->
      <div class="modal fade" id="bankAccountsModal" tabindex="-1" aria-labelledby="bankAccountsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="bankAccountsModalLabel">Bank Accounts</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">Mercantil</h5>
                  <p class="card-text">Account Number: XXXX-XXXX-XXXX-XXXX-XXXX</p>
                  <p class="card-text">Bank Name: Mercantil. C.A</p>
                  <a href="#" class="btn btn-primary">More Details</a>
                </div>
              </div>
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">Bancamiga.</h5>
                  <p class="card-text">Account Number: XXXX-XXXX-XXXX-XXXX-XXXX</p>
                  <p class="card-text">Bank Name: Bancamiga. C.A</p>
                  <a href="#" class="btn btn-primary">More Details</a>
                </div>
              </div>
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">Banco de Venezuela.</h5>
                  <p class="card-text">Account Number: XXXX-XXXX-XXXX-XXXX-XXXX</p>
                  <p class="card-text">Bank Name: Banco de Venezuela. C.A</p>
                  <a href="#" class="btn btn-primary">More Details</a>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary">Save Changes</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Footer -->
    <footer class="mt-auto bg-secondary py-3">
      <div class="container text-center">
        Todos los derechos reservados.
      </div>
    </footer>

    <script>
    // Script para activar el tab de búsqueda si hay parámetros de búsqueda en la URL
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('fecha') || urlParams.has('tipo')) {
            const searchTabButton = document.getElementById('pills-search-tab');
            const searchTabContent = document.getElementById('pills-search');
            
            if (searchTabButton && searchTabContent) {
                // Desactivar el tab actualmente activo
                document.querySelector('#pills-tab .nav-link.active')?.classList.remove('active');
                document.querySelector('#pills-tabContent .tab-pane.show.active')?.classList.remove('show', 'active');
                
                // Activar el tab de búsqueda
                searchTabButton.classList.add('active');
                searchTabContent.classList.add('show', 'active');
            }
        }
    });
    </script>
</body>
</html>