<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['profile']) || !isset($_SESSION['institution']) || $_SESSION['profile'] !== 'Docente') {
    echo json_encode(['success' => false, 'error' => 'Acceso no autorizado']);
    exit;
}

include 'db_connection.php';

$institution = $_SESSION['institution'];
$course_filter = isset($_POST['course_filter']) ? $_POST['course_filter'] : '';
$search_username = isset($_POST['search_username']) ? trim($_POST['search_username']) : '';

try {
    $sql = "DELETE r FROM results r
            JOIN users u ON r.user_id = u.id
            WHERE u.profile = 'Alumno' AND u.institution = ?";
    $params = [$institution];
    $types = "s";

    if ($course_filter) {
        $sql .= " AND u.Course = ?";
        $params[] = $course_filter;
        $types .= "s";
    }
    if ($search_username) {
        $sql .= " AND u.username LIKE ?";
        $params[] = "%$search_username%";
        $types .= "s";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

$conn->close();
?>