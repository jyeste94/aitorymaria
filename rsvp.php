<?php
// Evitar errores de CORS si el frontend y backend se prueban en distintos dominios localmente
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");

// ==========================================
// 1. CONFIGURACIÓN DE LA BASE DE DATOS (FAKE)
// ==========================================
// Sustituye estos datos por los reales de tu proveedor (Hostinger, Ionos, etc)
$host = '127.0.0.1';        // Habitualmente 'localhost' o dirección IP
$db   = 'boda_aitorymaria'; // El nombre de la base de datos
$user = 'usuario_boda';     // El usuario de la base de datos
$pass = 'contraseña_segura'; // La contraseña del usuario
$charset = 'utf8mb4';

// Configuración de conexión segura (PDO)
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lanzar excepciones en errores
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false, // Prevenir inyecciones SQL severas
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Si falla la conexión, devolver error JSON estructurado
    echo json_encode([
        'status' => 'error', 
        'message' => 'Error de conexión a la base de datos. Por favor, compruebe la configuración.'
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
