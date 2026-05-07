<?php
// ==========================================
// CONFIGURACIÓN DE SEGURIDAD (TOKEN DE ACCESO)
// ==========================================
// Cambia esto por una contraseña difícil. Para acceder deberás entrar a:
// tudominio.com/invitados.php?token=mi_boda_secreta_2026
$token_secreto = 'mi_boda_secreta_2026';

if (!isset($_GET['token']) || $_GET['token'] !== $token_secreto) {
    header("HTTP/1.1 403 Forbidden");
    die('
    <!DOCTYPE html>
    <html lang="es">
    <head><title>Acceso Denegado</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
    <body class="bg-light d-flex align-items-center justify-content-center" style="height: 100vh;">
        <div class="text-center">
            <h1 class="display-1 text-danger">403</h1>
            <p class="lead">Acceso denegado. <br>Por favor provee el token de acceso en la URL.</p>
        </div>
    </body>
    </html>');
}

try {
    require_once 'db.php';
} catch (\PDOException $e) {
    die("<div class='container mt-5'><div class='alert alert-danger'>Error de conexión a la base de datos. Revisa las credenciales en db.php</div></div>");
}

// Extraer los invitados ordenados por los mas recientes
$stmt = $pdo->query("SELECT * FROM rsvp_guests ORDER BY created_at DESC");
$guests = $stmt->fetchAll();

// ==========================================
// CHULETA ESTADÍSTICA
// ==========================================
$stats = [
    'attending' => 0,
    'declined' => 0,
    'total_people' => 0
];

foreach ($guests as $g) {
    if ($g['attendance'] === 'yes') {
        $stats['attending']++;
        $stats['total_people'] += 1 + (int)$g['companions'];
    } else {
        $stats['declined']++;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Invitados - Aitor & Maria</title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .bg-mint { background-color: #b5cfc7 !important; color: #333; }
        .card-stat { border-radius: 12px; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.05); transition: transform 0.2s;}
        .card-stat:hover { transform: translateY(-5px); }
        .stat-icon { font-size: 2rem; opacity: 0.3; position: absolute; right: 20px; bottom: 20px; }
        .table-container { background: white; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); overflow: hidden;}
        .badge-yes { background-color: #d4edda; color: #155724; border-radius: 20px; padding: 5px 10px; font-weight: normal; }
        .badge-no { background-color: #f8d7da; color: #721c24; border-radius: 20px; padding: 5px 10px; font-weight: normal; }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-dark bg-dark mb-4">
  <div class="container-fluid px-4">
    <span class="navbar-brand mb-0 h1"><i class="fas fa-rings me-2"></i> Gestión de Invitados | Aitor & Maria</span>
    <span class="text-white-50"><i class="fas fa-lock"></i> Acceso Seguro</span>
  </div>
</nav>

<div class="container-fluid px-4 pb-5">
    
    <!-- Resumen -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card card-stat bg-white h-100 p-3 position-relative">
                <h6 class="text-muted text-uppercase mb-1">Total Confirmados</h6>
                <h2 class="display-5 mb-0 fw-bold"><?php echo $stats['total_people']; ?></h2>
                <small class="text-success"><i class="fas fa-user-check"></i> Cabezas contando acompañantes</small>
                <i class="fas fa-users stat-icon text-success"></i>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-stat bg-white h-100 p-3 position-relative">
                <h6 class="text-muted text-uppercase mb-1">Han Declinado</h6>
                <h2 class="display-5 mb-0 fw-bold text-secondary"><?php echo $stats['declined']; ?></h2>
                <small class="text-muted">No podrán asistir</small>
                <i class="fas fa-user-times stat-icon"></i>
            </div>
        </div>
    </div>

    <!-- Tabla -->
    <div class="table-container p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Listado de Respuestas</h4>
            <button onclick="window.print()" class="btn btn-sm btn-outline-secondary"><i class="fas fa-print"></i> Imprimir a PDF</button>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Fecha</th>
                        <th scope="col">Asistencia</th>
                        <th scope="col">Invitado Principal</th>
                        <th scope="col">Contacto</th>
                        <th scope="col" class="text-center">Acompañantes</th>
                        <th scope="col">Canciones / Comentarios</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($guests)): ?>
                        <tr><td colspan="6" class="text-center p-4 text-muted">Aún no hay respuestas en la base de datos.</td></tr>
                    <?php else: ?>
                        <?php foreach($guests as $guest): ?>
                            <?php $isAttending = ($guest['attendance'] === 'yes'); ?>
                            <tr>
                                <td class="text-muted small">
                                    <?php echo date('d/m/Y H:i', strtotime($guest['created_at'])); ?>
                                </td>
                                <td>
                                    <?php if($isAttending): ?>
                                        <span class="badge-yes"><i class="fas fa-check-circle"></i> Sí</span>
                                    <?php else: ?>
                                        <span class="badge-no"><i class="fas fa-times-circle"></i> No</span>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-bold">
                                    <?php echo htmlspecialchars($guest['name']); ?>
                                </td>
                                <td>
                                    <?php if(!empty($guest['phone'])): ?>
                                        <div><small><i class="fas fa-phone text-muted"></i> <?php echo htmlspecialchars($guest['phone']); ?></small></div>
                                    <?php endif; ?>
                                    <?php if(!empty($guest['email'])): ?>
                                        <div><small><i class="fas fa-envelope text-muted"></i> <a href="mailto:<?php echo htmlspecialchars($guest['email']); ?>"><?php echo htmlspecialchars($guest['email']); ?></a></small></div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if($isAttending): ?>
                                        +<?php echo htmlspecialchars($guest['companions']); ?>
                                        <?php if(!empty($guest['companions_details'])): ?>
                                            <?php $details = json_decode($guest['companions_details'], true); ?>
                                            <?php if(is_array($details)): ?>
                                                <div class="small text-muted mt-1">
                                                    <?php foreach($details as $c): ?>
                                                        <div><?php echo htmlspecialchars($c['name']); ?><?php echo !empty($c['child']) ? ' 👶' : ''; ?></div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if(!empty($guest['songs'])): ?>
                                        <div class="mb-1"><small class="text-muted"><i class="fas fa-music"></i> <strong>Música:</strong> <em><?php echo nl2br(htmlspecialchars($guest['songs'])); ?></em></small></div>
                                    <?php endif; ?>
                                    <?php if(!empty($guest['comments'])): ?>
                                        <div><small class="text-muted"><i class="fas fa-comment"></i> <strong>Nota:</strong> <?php echo nl2br(htmlspecialchars($guest['comments'])); ?></small></div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
