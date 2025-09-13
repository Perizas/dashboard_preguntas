<?php
session_start();

if (!isset($_SESSION['profile']) || $_SESSION['profile'] !== 'Docente' || !isset($_SESSION['institution'])) {
    echo json_encode(['success' => false, 'error' => 'Acceso denegado: Solo los docentes pueden eliminar resultados.']);
    exit;
}

include 'db_connection.php';

$result_id = isset($_POST['result_id']) ? (int)$_POST['result_id'] : 0;
$institution = $_SESSION['institution'];

if ($result_id <= 0) {
    echo json_encode(['success' => false, 'error' => 'ID de resultado inválido.']);
    exit;
}

$sql = "SELECT r.id
        FROM results r
        JOIN users u ON r.user_id = u.id
        WHERE r.id = ? AND u.profile = 'Alumno' AND u.institution = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $result_id, $institution);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'error' => 'Resultado no encontrado o no pertenece a un alumno de tu institución.']);
    $stmt->close();
    exit;
}

$sql = "DELETE FROM results WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $result_id);
$stmt->execute();
$stmt->close();

echo json_encode(['success' => true]);
?>