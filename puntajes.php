<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['profile']) || !isset($_SESSION['institution'])) {
    header("Location: Inicio_Sesion.php");
    exit;
}

include 'db_connection.php';

$institution = $_SESSION['institution'];
$selected_course = isset($_POST['course_filter']) ? $_POST['course_filter'] : '';
$search_username = isset($_POST['search_username']) ? trim($_POST['search_username']) : '';

// Obtener cursos únicos para Docentes
$courses = [];
if ($_SESSION['profile'] === 'Docente') {
    $sql = "SELECT DISTINCT Course FROM users WHERE profile = 'Alumno' AND institution = ? AND Course != 'N/A' ORDER BY Course";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $institution);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row['Course'];
    }
    $stmt->close();
}

// Obtener resultados
if ($_SESSION['profile'] === 'Docente') {
    $sql = "SELECT r.id, u.username, u.email, u.Course, r.level, r.score, r.created_at
            FROM results r
            JOIN users u ON r.user_id = u.id
            WHERE u.profile = 'Alumno' AND u.institution = ?";
    $params = [$institution];
    $types = "s";

    if ($selected_course) {
        $sql .= " AND u.Course = ?";
        $params[] = $selected_course;
        $types .= "s";
    }
    if ($search_username) {
        $sql .= " AND u.username LIKE ?";
        $params[] = "%$search_username%";
        $types .= "s";
    }
    $sql .= " ORDER BY r.created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
} else {
    $sql = "SELECT r.id, u.username, u.email, u.Course, r.level, r.score, r.created_at
            FROM results r
            JOIN users u ON r.user_id = u.id
            WHERE r.user_id = ? AND u.institution = ?
            ORDER BY r.created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $_SESSION['user_id'], $institution);
}
$stmt->execute();
$result = $stmt->get_result();
$results = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$results_by_level = ['1' => [], '2' => [], '3' => [], '4' => []];
foreach ($results as $row) {
    if (isset($results_by_level[$row['level']])) {
        $results_by_level[$row['level']][] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resultados - Algebritos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <link rel="stylesheet" href="styles.css">
    <style>
        .chart-container { max-width: 400px; margin: auto; }
        .table-container { max-height: 400px; overflow-y: auto; border: 1px solid #dee2e6; }
    </style>
</head>
<body>
    <div class="logo-container">
        <a href="inicio.php"><img src="Imagenes/prueba.png" alt="Logo" class="logo-img"></a>
    </div>

    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="inicio.php">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="descarga.php">Descarga</a></li>
                    <li class="nav-item"><a class="nav-link" href="preguntas.php">Preguntas</a></li>
                    <li class="nav-item"><a class="nav-link active" aria-current="page" href="puntajes.php">Resultados</a></li>
                    <li class="nav-item"><a class="nav-link" href="cuenta.php">Cuenta</a></li>
                    <li class="nav-item"><a class="nav-link" href="#" onclick="confirmLogout()"><i class="bi bi-box-arrow-right"></i> Salir</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="content-container">
        <h1 class="mb-4"><?php echo $_SESSION['profile'] === 'Docente' ? 'Resultados de Alumnos' : 'Mis Resultados'; ?> - Algebritos</h1>
        <p>Bienvenido, <?php echo htmlspecialchars($_SESSION['username']); ?> (<?php echo htmlspecialchars($_SESSION['institution']); ?>)</p>

        <?php if ($_SESSION['profile'] === 'Docente'): ?>
            <div class="mb-3">
                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label for="course_filter" class="form-label">Filtrar por Curso:</label>
                            <select class="form-select" name="course_filter" id="course_filter" onchange="resetSearchAndSubmit()">
                                <option value="">Todos los cursos</option>
                                <?php foreach ($courses as $course): ?>
                                    <option value="<?php echo htmlspecialchars($course); ?>" <?php echo $selected_course === $course ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($course); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="search_username" class="form-label">Buscar por Nombre de Usuario:</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="search_username" id="search_username" value="<?php echo htmlspecialchars($search_username); ?>" placeholder="Escribe un nombre de usuario">
                                <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 d-flex">
                            <button type="button" class="btn btn-danger" onclick="deleteAllResults()">Eliminar Todos los Resultados</button>
                        </div>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <div id="messageContainer"></div>

        <ul class="nav nav-tabs mb-3" id="levelTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="level1-tab" data-bs-toggle="tab" data-bs-target="#level1" type="button" role="tab">Nivel 1</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="level2-tab" data-bs-toggle="tab" data-bs-target="#level2" type="button" role="tab">Nivel 2</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="level3-tab" data-bs-toggle="tab" data-bs-target="#level3" type="button" role="tab">Nivel 3</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="level4-tab" data-bs-toggle="tab" data-bs-target="#level4" type="button" role="tab">Examen Final</button>
            </li>
        </ul>

        <div class="tab-content" id="levelTabContent">
            <?php foreach ([1, 2, 3, 4] as $level): ?>
                <div class="tab-pane fade <?php echo $level == 1 ? 'show active' : ''; ?>" id="level<?php echo $level; ?>" role="tabpanel">
                    <div class="table-container">
                        <?php if (count($results_by_level[$level]) > 0): ?>
                            <table class="table table-striped table-bordered" id="resultsTable<?php echo $level; ?>">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Email</th>
                                        <th>Usuario</th>
                                        <th>Curso</th>
                                        <th>Puntuación</th>
                                        <th>Fecha</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($results_by_level[$level] as $index => $row): ?>
                                        <tr id="row-<?php echo htmlspecialchars($row['id']); ?>">
                                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                                            <td><?php echo htmlspecialchars($row['Course']); ?></td>
                                            <td><?php echo htmlspecialchars($row['score']); ?><?php echo $level == 4 ? '/10' : '/5'; ?></td>
                                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                            <td>
                                                <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#chartModal<?php echo $level . '-' . $index; ?>">
                                                    <i class="bi bi-pie-chart"></i> Ver Gráfico
                                                </button>
                                                <?php if ($_SESSION['profile'] === 'Docente'): ?>
                                                    <button class="btn btn-danger btn-sm ms-2" onclick="deleteResult(<?php echo htmlspecialchars($row['id']); ?>)">
                                                        <i class="bi bi-trash"></i> Eliminar
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="alert alert-info">No hay resultados para el <?php echo $level == 4 ? 'Examen Final' : 'Nivel ' . $level; ?>.</div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php foreach ([1, 2, 3, 4] as $level): ?>
        <?php foreach ($results_by_level[$level] as $index => $row): ?>
            <div class="modal fade" id="chartModal<?php echo $level . '-' . $index; ?>" tabindex="-1" aria-labelledby="chartModalLabel<?php echo $level . '-' . $index; ?>" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="chartModalLabel<?php echo $level . '-' . $index; ?>">Gráfico de Resultado (ID: <?php echo htmlspecialchars($row['id']); ?>)</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="chart-container">
                                <canvas id="individualPieChart<?php echo $level . '-' . $index; ?>" width="400" height="400"></canvas>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endforeach; ?>

    <footer class="footer">
        <div class="footer-content">
            <img src="Imagenes/prueba.png" alt="Logo" class="footer-logo">
            <p>Información de contacto: Camilo Esteban Arvilla Méndez, Tel: 319 7011921, correo: algebritos@outlook.com, algebritos@gmail.com</p>
            <div class="social-icons">
                <a href="https://www.facebook.com/?stype=lo&flo=1&deoia=1&jlou=Afd9wdnNhNCJ6bpzxQ_e45zrT0ZsHWYAJXsTfNsM2IA2mESBVCjci5QUAtyruldjPaPWl3xcXLuA6r0H6pNyqIt9WybDJH42leVAfK80OjAIkA&smuh=1225&lh=Ac-lCTAMxdarlv7K2hM"><img src="Imagenes/f.png" alt="Facebook" class="social-icon"></a>
                <a href="https://www.instagram.com/algebritos/"><img src="Imagenes/i.png" alt="Instagram" class="social-icon"></a>
                <a href="https://x.com/algebritos"><img src="Imagenes/x.png" alt="X" class="social-icon"></a>
                <a href="https://www.threads.com/login"><img src="Imagenes/t.png" alt="Threads" class="social-icon"></a>
            </div>
            <p>2025 Camilo Arvilla. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            console.log('DOM cargado, inicializando scripts');

            window.confirmLogout = function() {
                if (confirm('¿Estás seguro de que quieres cerrar sesión?')) {
                    window.location.href = 'salir.php';
                }
            };

            // Resetear el campo de búsqueda y enviar el formulario al cambiar el curso
            window.resetSearchAndSubmit = function() {
                document.getElementById('search_username').value = '';
                document.forms[0].submit();
            };

            <?php foreach ([1, 2, 3, 4] as $level): ?>
                <?php foreach ($results_by_level[$level] as $index => $row): ?>
                    <?php
                        $score = (int)$row['score'];
                        $total_questions = $level == 4 ? 10 : 5;
                        $aciertos = $score;
                        $desaciertos = $total_questions - $score;
                    ?>
                    <?php if ($aciertos + $desaciertos > 0): ?>
                        console.log('Inicializando gráfico individual para ID <?php echo $row['id']; ?> (Nivel <?php echo $level; ?>)');
                        new Chart(document.getElementById('individualPieChart<?php echo $level . '-' . $index; ?>'), {
                            type: 'pie',
                            data: {
                                labels: ['Aciertos', 'Desaciertos'],
                                datasets: [{
                                    data: [<?php echo $aciertos; ?>, <?php echo $desaciertos; ?>],
                                    backgroundColor: ['#36A2EB', '#FF6384'],
                                    hoverOffset: 4
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: { position: 'top' },
                                    title: { display: true, text: 'Aciertos vs Desaciertos (ID: <?php echo htmlspecialchars($row['id']); ?>)' }
                                }
                            }
                        });
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endforeach; ?>

            window.deleteResult = function(resultId) {
                if (confirm('¿Estás seguro de que quieres eliminar este resultado?')) {
                    fetch('eliminar_resultado.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'result_id=' + encodeURIComponent(resultId)
                    })
                    .then(response => response.json())
                    .then(data => {
                        const messageContainer = document.getElementById('messageContainer');
                        if (data.success) {
                            messageContainer.innerHTML = '<div class="alert alert-success">Resultado eliminado exitosamente.</div>';
                            document.getElementById('row-' + resultId).remove();
                            setTimeout(() => messageContainer.innerHTML = '', 3000);
                            let allEmpty = true;
                            ['1', '2', '3', '4'].forEach(level => {
                                if (document.querySelectorAll(`#resultsTable${level} tbody tr`).length > 0) {
                                    allEmpty = false;
                                }
                            });
                            if (allEmpty) {
                                document.getElementById('levelTabContent').innerHTML = `
                                    <div class="tab-pane fade show active" id="level1" role="tabpanel">
                                        <div class="table-container">
                                            <div class="alert alert-info">No hay resultados para el Nivel 1.</div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="level2" role="tabpanel">
                                        <div class="table-container">
                                            <div class="alert alert-info">No hay resultados para el Nivel 2.</div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="level3" role="tabpanel">
                                        <div class="table-container">
                                            <div class="alert alert-info">No hay resultados para el Nivel 3.</div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="level4" role="tabpanel">
                                        <div class="table-container">
                                            <div class="alert alert-info">No hay resultados para el Examen Final.</div>
                                        </div>
                                    </div>`;
                            }
                        } else {
                            messageContainer.innerHTML = '<div class="alert alert-danger">Error al eliminar: ' + data.error + '</div>';
                            setTimeout(() => messageContainer.innerHTML = '', 3000);
                        }
                    })
                    .catch(error => {
                        console.error('Error en fetch:', error);
                        document.getElementById('messageContainer').innerHTML = '<div class="alert alert-danger">Error al procesar la solicitud.</div>';
                        setTimeout(() => messageContainer.innerHTML = '', 3000);
                    });
                }
            };

            window.deleteAllResults = function() {
                if (confirm('¿Estás seguro de que quieres eliminar TODOS los resultados de los alumnos? Esta acción no se puede deshacer.')) {
                    fetch('eliminar_todos_resultados.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: '<?php echo $_SESSION['profile'] === 'Docente' && ($selected_course || $search_username) ? 'course_filter=' + encodeURIComponent($selected_course) . '&search_username=' + encodeURIComponent($search_username) : ''; ?>'
                    })
                    .then(response => response.json())
                    .then(data => {
                        const messageContainer = document.getElementById('messageContainer');
                        if (data.success) {
                            messageContainer.innerHTML = '<div class="alert alert-success">Todos los resultados de alumnos fueron eliminados exitosamente.</div>';
                            document.getElementById('levelTabContent').innerHTML = `
                                <div class="tab-pane fade show active" id="level1" role="tabpanel">
                                    <div class="table-container">
                                        <div class="alert alert-info">No hay resultados para el Nivel 1.</div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="level2" role="tabpanel">
                                    <div class="table-container">
                                        <div class="alert alert-info">No hay resultados para el Nivel 2.</div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="level3" role="tabpanel">
                                    <div class="table-container">
                                        <div class="alert alert-info">No hay resultados para el Nivel 3.</div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="level4" role="tabpanel">
                                    <div class="table-container">
                                        <div class="alert alert-info">No hay resultados para el Examen Final.</div>
                                    </div>
                                </div>`;
                            setTimeout(() => messageContainer.innerHTML = '', 3000);
                        } else {
                            messageContainer.innerHTML = '<div class="alert alert-danger">Error al eliminar: ' + data.error + '</div>';
                            setTimeout(() => messageContainer.innerHTML = '', 3000);
                        }
                    })
                    .catch(error => {
                        console.error('Error en fetch:', error);
                        document.getElementById('messageContainer').innerHTML = '<div class="alert alert-danger">Error al procesar la solicitud.</div>';
                        setTimeout(() => messageContainer.innerHTML = '', 3000);
                    });
                }
            };
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>