<?php
include 'db_connection.php';
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email']; // Cambia a 'username' si prefieres usar username
    $password = $_POST['password'];

    if (!empty($email) && !empty($password)) {
        $sql = "SELECT id, password FROM users WHERE email = ?"; // O usa WHERE username = ?
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                echo json_encode(['success' => true, 'user_id' => $row['id'], 'mensaje' => 'Login exitoso']);
            } else {
                echo json_encode(['success' => false, 'mensaje' => 'Contraseña incorrecta']);
            }
        } else {
            echo json_encode(['success' => false, 'mensaje' => 'Correo no encontrado']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'mensaje' => 'Datos requeridos']);
    }
}
$conn->close();
?>