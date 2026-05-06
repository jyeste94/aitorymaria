<?php
// Evitar errores de CORS si el frontend y backend se prueban en distintos dominios localmente
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");

try {
    require_once 'db.php';
} catch (\PDOException $e) {
    echo json_encode([
        'status' => 'error', 
        'message' => 'Error de conexión a la base de datos.'
    ]);
    exit;
}

// ==========================================
// 2. RECOGIDA Y LIMPIEZA DE DATOS (POST)
// ==========================================
// Recibimos los datos del formulario de forma segura
$attendance = $_POST['attendance'] ?? '';
$name       = trim($_POST['name'] ?? '');
$password   = $_POST['password'] ?? '';
$phone      = trim($_POST['phone'] ?? null);
$email      = trim($_POST['email'] ?? null);
$companions = isset($_POST['companions']) ? (int)$_POST['companions'] : 0;
$meat       = isset($_POST['meat']) ? (int)$_POST['meat'] : 0;
$fish       = isset($_POST['fish']) ? (int)$_POST['fish'] : 0;
$songs      = trim($_POST['songs'] ?? null);
$comments   = trim($_POST['comments'] ?? null);

// Validaciones básicas de seguridad
if (empty($attendance) || empty($name)) {
    echo json_encode([
        'status' => 'error', 
        'message' => 'Error: Los campos "Nombre" y "¿Vas a venir?" son obligatorios.'
    ]);
    exit;
}

// ==========================================
// 3. CIFRADO DE CONTRASEÑA
// ==========================================
// Nunca guardamos contraseñas en texto plano por ley de protección de datos
$password_hash = null;
if (!empty($password)) {
    $password_hash = password_hash($password, PASSWORD_BCRYPT);
}

// ==========================================
// 4. INSERCIÓN EN BASE DE DATOS PREPARADA
// ==========================================
// Se utilizan "Prepared Statements" (sentencias con signos "?") que son impermeables a SQL INJECTION.
$sql = "INSERT INTO rsvp_guests 
        (attendance, name, password_hash, phone, email, companions, meat, fish, songs, comments) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
$stmt = $pdo->prepare($sql);

try {
    // Ejecutamos pasando el array de variables directamente
    $stmt->execute([
        $attendance, 
        $name, 
        $password_hash, 
        $phone, 
        $email, 
        $companions, 
        $meat, 
        $fish, 
        $songs, 
        $comments
    ]);
    
    // Si todo va bien, contestamos con éxito al Javascript de la web
    echo json_encode([
        'status' => 'success', 
        'message' => '¡Reserva confirmada con éxito! Te esperamos de corazón.'
    ]);
    
} catch (Exception $e) {
    // Error al guardar (puede ser email duplicado, tabla inexistente, etc)
    echo json_encode([
        'status' => 'error', 
        'message' => 'Hubo un error al guardar tu confirmación. Inténtalo de nuevo.'
    ]);
}
?>
