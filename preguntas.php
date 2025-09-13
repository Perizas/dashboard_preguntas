<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Preguntas - Agregar Pregunta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="logo-container">
        <a href="inicio.php">
            <img src="Imagenes/prueba.png" alt="Logo" class="logo-img">
        </a>
    </div>

    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="inicio.php">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="descarga.php">Descarga</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="index.php">Preguntas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="puntajes.php">Resultados</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="cuenta.php">Cuenta</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="confirmLogout()"><i class="bi bi-box-arrow-right"></i> Salir</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="content-container">
        <?php
        include 'db_connection.php';
        session_start();

        // Función para normalizar cadenas (quitar acentos para comparación)
        function normalize_string($string) {
            $unwanted_array = array(
                'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
                'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U',
                'ñ' => 'n', 'Ñ' => 'N'
            );
            return strtr($string, $unwanted_array);
        }

        // Verificar si el usuario está autenticado
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['profile']) || !isset($_SESSION['institution'])) {
            echo "<div class='alert alert-danger'>Error: Sesión no iniciada correctamente. Redirigiendo al inicio de sesión...</div>";
            header("Location: Inicio_Sesion.php");
            exit();
        }

        $profile = $_SESSION['profile'];
        $institution = trim($_SESSION['institution']);

        // Depuración: Mostrar la institución de la sesión
        echo "<div class='alert alert-info'>Institución en la sesión: " . (empty($institution) ? 'VACÍA' : htmlspecialchars($institution)) . "</div>";

        // Validar perfil
        if ($profile !== 'Docente') {
            echo "<div class='alert alert-danger'>No tienes permiso para acceder a esta sección.</div>";
            echo "</div></body></html>";
            exit();
        }

        // Lista de instituciones válidas
        $valid_institutions = ['Instituto Técnico Distrital Julio Flórez', 'Prueba'];

        // Normalizar institución para comparación
        $normalized_institution = normalize_string($institution);
        $normalized_valid_institutions = array_map('normalize_string', $valid_institutions);

        // Validar institución de la sesión
        $institution_index = array_search($normalized_institution, $normalized_valid_institutions);
        if ($institution_index === false) {
            echo "<div class='alert alert-danger'>Error: Institución no válida o no definida en la sesión: " . htmlspecialchars($institution) . "</div>";
            echo "<div class='alert alert-info'>Instituciones válidas: " . implode(', ', $valid_institutions) . "</div>";
            echo "</div></body></html>";
            exit();
        }

        // Usar la institución válida con acentos correctos
        $institution = $valid_institutions[$institution_index];
        $_SESSION['institution'] = $institution; // Actualizar sesión con el nombre correcto

        // Depuración: Confirmar institución corregida
        echo "<div class='alert alert-info'>Institución corregida para usar: " . htmlspecialchars($institution) . "</div>";

        // Obtener cursos únicos para la institución del docente
        $courses = [];
        $sql = "SELECT DISTINCT Course FROM users WHERE profile = 'Alumno' AND institution = ? AND Course != 'N/A' AND Course IS NOT NULL ORDER BY Course";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $institution);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $courses[] = $row['Course'];
        }
        $stmt->close();

        // Depuración: Mostrar cursos encontrados
        echo "<div class='alert alert-info'>Cursos encontrados para " . htmlspecialchars($institution) . ": " . (empty($courses) ? 'Ninguno' : implode(', ', $courses)) . "</div>";

        // Manejar el formulario cuando se envía (agregar)
        if (isset($_POST['submit'])) {
            $level = $_POST['level'];
            $header = trim($_POST['header']);
            $option1 = trim($_POST['option1']);
            $option2 = trim($_POST['option2']);
            $option3 = trim($_POST['option3']);
            $option4 = trim($_POST['option4']);
            $correct_option = $_POST['correct_option'];
            $course = trim($_POST['course']);
            $question_profile = 'Alumno';

            // Fuerza institución del servidor
            $form_institution = $institution;

            // Depuración: Mostrar valor usado para institution
            echo "<div class='alert alert-info'>Institución usada para guardar: " . htmlspecialchars($form_institution) . "</div>";

            // Validación
            if ($correct_option < 0 || $correct_option > 3) {
                echo "<div class='alert alert-danger'>Error: La opción correcta debe ser entre 0 y 3.</div>";
            } elseif (empty($course) || !in_array($course, $courses)) {
                echo "<div class='alert alert-danger'>Error: Curso no válido.</div>";
            } elseif (empty($form_institution) || !in_array($form_institution, $valid_institutions)) {
                echo "<div class='alert alert-danger'>Error: Institución no válida.</div>";
            } else {
                // Insertar nueva pregunta
                $sql = "INSERT INTO questions (level, header, option1, option2, option3, option4, correct_option, institution, course, profile)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("isssssisss", $level, $header, $option1, $option2, $option3, $option4, $correct_option, $form_institution, $course, $question_profile);

                if ($stmt->execute()) {
                    echo "<div class='alert alert-success'>Pregunta agregada exitosamente.</div>";
                } else {
                    echo "<div class='alert alert-danger'>Error al agregar pregunta: " . $stmt->error . "</div>";
                }
                $stmt->close();
            }
        }

        // Manejar la edición cuando se envía
        if (isset($_POST['edit_submit']) && isset($_POST['edit_id'])) {
            $id = $_POST['edit_id'];
            $level = $_POST['edit_level'];
            $header = trim($_POST['edit_header']);
            $option1 = trim($_POST['edit_option1']);
            $option2 = trim($_POST['edit_option2']);
            $option3 = trim($_POST['edit_option3']);
            $option4 = trim($_POST['edit_option4']);
            $correct_option = $_POST['edit_correct_option'];
            $course = trim($_POST['edit_course']);
            $question_profile = 'Alumno';

            // Fuerza institución del servidor
            $form_institution = $institution;

            // Depuración: Mostrar valor usado para edit_institution
            echo "<div class='alert alert-info'>Institución usada para editar: " . htmlspecialchars($form_institution) . "</div>";

            // Validación
            if ($correct_option < 0 || $correct_option > 3) {
                echo "<div class='alert alert-danger'>Error: La opción correcta debe ser entre 0 y 3.</div>";
            } elseif (empty($course) || !in_array($course, $courses)) {
                echo "<div class='alert alert-danger'>Error: Curso no válido.</div>";
            } elseif (empty($form_institution) || !in_array($form_institution, $valid_institutions)) {
                echo "<div class='alert alert-danger'>Error: Institución no válida.</div>";
            } else {
                $sql = "UPDATE questions SET level = ?, header = ?, option1 = ?, option2 = ?, option3 = ?, option4 = ?, correct_option = ?, course = ?, institution = ?, profile = ? WHERE id = ? AND institution = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("isssssisssis", $level, $header, $option1, $option2, $option3, $option4, $correct_option, $course, $form_institution, $question_profile, $id, $form_institution);

                if ($stmt->execute()) {
                    echo "<div class='alert alert-success'>Pregunta actualizada exitosamente.</div>";
                } else {
                    echo "<div class='alert alert-danger'>Error al actualizar pregunta: " . $stmt->error . "</div>";
                }
                $stmt->close();
            }
        }

        // Manejar la eliminación individual
        if (isset($_POST['delete_id'])) {
            $id = $_POST['delete_id'];

            // Eliminar primero las respuestas asociadas
            $sql_answers = "DELETE FROM answers WHERE question_id = ?";
            $stmt_answers = $conn->prepare($sql_answers);
            $stmt_answers->bind_param("i", $id);
            $stmt_answers->execute();
            $stmt_answers->close();

            // Luego eliminar la pregunta
            $sql = "DELETE FROM questions WHERE id = ? AND institution = ? AND profile = 'Alumno'";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("is", $id, $institution);

            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>Pregunta eliminada exitosamente.</div>";
            } else {
                echo "<div class='alert alert-danger'>Error al eliminar pregunta: " . $stmt->error . "</div>";
            }
            $stmt->close();
        }

        // Manejar la eliminación de todo
        if (isset($_POST['delete_all'])) {
            // Eliminar primero todas las respuestas asociadas
            $sql_answers = "DELETE FROM answers WHERE question_id IN (SELECT id FROM questions WHERE institution = ? AND profile = 'Alumno')";
            $stmt_answers = $conn->prepare($sql_answers);
            $stmt_answers->bind_param("s", $institution);
            $stmt_answers->execute();
            $stmt_answers->close();

            // Luego eliminar todas las preguntas
            $sql = "DELETE FROM questions WHERE institution = ? AND profile = 'Alumno'";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $institution);
            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>Todas las preguntas han sido eliminadas exitosamente.</div>";
            } else {
                echo "<div class='alert alert-danger'>Error al eliminar preguntas: " . $stmt->error . "</div>";
            }
            $stmt->close();
        }

        // Consultar preguntas para mostrar en el modal
        $questions = [];
        $sql = "SELECT id, level, header, option1, option2, option3, option4, correct_option, course, institution, profile 
                FROM questions 
                WHERE institution = ? AND profile = 'Alumno' 
                ORDER BY level";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $institution);
        if (!$stmt->execute()) {
            echo "<div class='alert alert-danger'>Error en la consulta de preguntas: " . $stmt->error . "</div>";
        }
        $result = $stmt->get_result();

        // Depuración: Mostrar número de preguntas encontradas
        echo "<div class='alert alert-info'>Número de preguntas encontradas para " . htmlspecialchars($institution) . ": " . $result->num_rows . "</div>";

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $questions[] = $row;
            }
        }
        $stmt->close();

        $conn->close();
        ?>

        <h1 class="mb-4">Agregar Nueva Pregunta</h1>
        <?php if (empty($courses)): ?>
            <div class="alert alert-warning">No hay cursos disponibles para alumnos en tu institución (<?php echo htmlspecialchars($institution); ?>). Agrega alumnos primero.</div>
        <?php else: ?>
        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label">Institución:</label>
                <select class="form-select" name="institution_display" id="institution_display" disabled required>
                    <option value="">Selecciona una institución</option>
                    <?php foreach ($valid_institutions as $inst): ?>
                        <option value="<?php echo htmlspecialchars($inst); ?>" <?php echo ($inst === $institution) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($inst); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="hidden" name="institution" id="institution_hidden" value="<?php echo htmlspecialchars($institution); ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Nivel (1-3):</label>
                <input type="number" class="form-control" name="level" min="1" max="3" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Curso:</label>
                <select class="form-select" name="course" required>
                    <option value="" disabled selected>Selecciona un curso</option>
                    <?php foreach ($courses as $course): ?>
                        <option value="<?php echo htmlspecialchars($course); ?>"><?php echo htmlspecialchars($course); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Encabezado/Pregunta:</label>
                <input type="text" class="form-control" name="header" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Opción 1:</label>
                <input type="text" class="form-control" name="option1" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Opción 2:</label>
                <input type="text" class="form-control" name="option2" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Opción 3:</label>
                <input type="text" class="form-control" name="option3" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Opción 4:</label>
                <input type="text" class="form-control" name="option4" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Opción Correcta (0-3):</label>
                <input type="number" class="form-control" name="correct_option" min="0" max="3" required>
            </div>
            <input type="hidden" name="profile" value="Alumno">
            <button type="submit" name="submit" class="btn btn-primary">Agregar Pregunta</button>
        </form>
        <?php endif; ?>

        <!-- Botón para eliminar todo -->
        <form method="POST" action="" style="display:inline-block; margin-top: 20px;">
            <input type="hidden" name="delete_all" value="1">
            <button type="button" class="btn btn-danger" onclick="if(confirm('¿Estás seguro de eliminar TODAS las preguntas? Esta acción no se puede deshacer.')) this.parentElement.submit();">
                Eliminar Todo
            </button>
        </form>

        <!-- Botón para abrir el modal de preguntas -->
        <button type="button" class="btn btn-secondary mt-3" data-bs-toggle="modal" data-bs-target="#questionsModal">
            Ver Preguntas Registradas
        </button>

        <!-- Modal para mostrar las preguntas -->
        <div class="modal fade" id="questionsModal" tabindex="-1" aria-labelledby="questionsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="questionsModalLabel">Preguntas Registradas</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Filtro por curso -->
                        <div class="mb-3">
                            <label for="courseFilter" class="form-label">Filtrar por Curso:</label>
                            <select class="form-select" id="courseFilter">
                                <option value="">Todos los cursos</option>
                                <?php foreach ($courses as $course): ?>
                                    <option value="<?php echo htmlspecialchars($course); ?>"><?php echo htmlspecialchars($course); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php if (empty($questions)): ?>
                            <p class="text-center">No hay preguntas registradas para <?php echo htmlspecialchars($institution); ?>.</p>
                        <?php else: ?>
                            <table class="table table-striped table-hover" id="questionsTable">
                                <thead>
                                    <tr>
                                        <th>Nivel</th>
                                        <th>Curso</th>
                                        <th>Institución</th>
                                        <th>Profile</th>
                                        <th>Pregunta</th>
                                        <th>Opciones</th>
                                        <th>Respuesta Correcta</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($questions as $index => $question): ?>
                                        <tr data-course="<?php echo htmlspecialchars($question['course']); ?>">
                                            <td><?php echo $question['level']; ?></td>
                                            <td><?php echo htmlspecialchars($question['course']); ?></td>
                                            <td><?php echo htmlspecialchars($question['institution']); ?></td>
                                            <td><?php echo htmlspecialchars($question['profile']); ?></td>
                                            <td><?php echo htmlspecialchars($question['header']); ?></td>
                                            <td>
                                                <ul class="mb-0">
                                                    <li>Opción 1: <?php echo htmlspecialchars($question['option1']); ?></li>
                                                    <li>Opción 2: <?php echo htmlspecialchars($question['option2']); ?></li>
                                                    <li>Opción 3: <?php echo htmlspecialchars($question['option3']); ?></li>
                                                    <li>Opción 4: <?php echo htmlspecialchars($question['option4']); ?></li>
                                                </ul>
                                            </td>
                                            <td>
                                                <?php
                                                $options = [$question['option1'], $question['option2'], $question['option3'], $question['option4']];
                                                $correctIndex = $question['correct_option'];
                                                echo 'Opción ' . ($correctIndex + 1) . ': ' . htmlspecialchars($options[$correctIndex]);
                                                ?>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-warning btn-sm edit-btn" data-bs-toggle="modal" data-bs-target="#editModal"
                                                        data-id="<?php echo $question['id']; ?>" 
                                                        data-level="<?php echo $question['level']; ?>" 
                                                        data-course="<?php echo htmlspecialchars($question['course']); ?>" 
                                                        data-institution="<?php echo htmlspecialchars($question['institution']); ?>" 
                                                        data-profile="<?php echo htmlspecialchars($question['profile']); ?>" 
                                                        data-header="<?php echo htmlspecialchars($question['header']); ?>" 
                                                        data-option1="<?php echo htmlspecialchars($question['option1']); ?>" 
                                                        data-option2="<?php echo htmlspecialchars($question['option2']); ?>" 
                                                        data-option3="<?php echo htmlspecialchars($question['option3']); ?>" 
                                                        data-option4="<?php echo htmlspecialchars($question['option4']); ?>" 
                                                        data-correct="<?php echo $question['correct_option']; ?>">
                                                    Editar
                                                </button>
                                                <form method="POST" action="" style="display:inline;">
                                                    <input type="hidden" name="delete_id" value="<?php echo $question['id']; ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm delete-btn">
                                                        Eliminar
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para editar pregunta -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Editar Pregunta</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <?php if (empty($courses)): ?>
                            <div class="alert alert-warning">No hay cursos disponibles para alumnos en tu institución (<?php echo htmlspecialchars($institution); ?>). Agrega alumnos primero.</div>
                        <?php else: ?>
                        <form method="POST" action="">
                            <input type="hidden" name="edit_id" id="edit_id">
                            <div class="mb-3">
                                <label class="form-label">Institución:</label>
                                <select class="form-select" name="edit_institution_display" id="edit_institution_display" disabled required>
                                    <option value="">Selecciona una institución</option>
                                    <?php foreach ($valid_institutions as $inst): ?>
                                        <option value="<?php echo htmlspecialchars($inst); ?>" <?php echo ($inst === $institution) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($inst); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="hidden" name="edit_institution" id="edit_institution_hidden" value="<?php echo htmlspecialchars($institution); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nivel (1-3):</label>
                                <input type="number" class="form-control" name="edit_level" id="edit_level" min="1" max="3" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Curso:</label>
                                <select class="form-select" name="edit_course" id="edit_course" required>
                                    <option value="" disabled>Selecciona un curso</option>
                                    <?php foreach ($courses as $course): ?>
                                        <option value="<?php echo htmlspecialchars($course); ?>"><?php echo htmlspecialchars($course); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Profile (Bloqueado):</label>
                                <input type="text" class="form-control" id="edit_profile_display" value="Alumno" disabled>
                                <input type="hidden" name="edit_profile" id="edit_profile" value="Alumno">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Encabezado/Pregunta:</label>
                                <input type="text" class="form-control" name="edit_header" id="edit_header" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Opción 1:</label>
                                <input type="text" class="form-control" name="edit_option1" id="edit_option1" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Opción 2:</label>
                                <input type="text" class="form-control" name="edit_option2" id="edit_option2" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Opción 3:</label>
                                <input type="text" class="form-control" name="edit_option3" id="edit_option3" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Opción 4:</label>
                                <input type="text" class="form-control" name="edit_option4" id="edit_option4" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Opción Correcta (0-3):</label>
                                <input type="number" class="form-control" name="edit_correct_option" id="edit_correct_option" min="0" max="3" required>
                            </div>
                            <button type="submit" name="edit_submit" class="btn btn-primary">Guardar Cambios</button>
                        </form>
                        <?php endif; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Ocultar todos los mensajes de alerta después de 10 segundos
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(function() {
                        alert.remove();
                    }, 500);
                }, 10000);
            });

            // Código para botones de edición
            var editButtons = document.querySelectorAll('.edit-btn');
            editButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    var id = this.getAttribute('data-id');
                    var level = this.getAttribute('data-level');
                    var course = this.getAttribute('data-course');
                    var profile = this.getAttribute('data-profile');
                    var header = this.getAttribute('data-header');
                    var option1 = this.getAttribute('data-option1');
                    var option2 = this.getAttribute('data-option2');
                    var option3 = this.getAttribute('data-option3');
                    var option4 = this.getAttribute('data-option4');
                    var correct = this.getAttribute('data-correct');

                    document.getElementById('edit_id').value = id;
                    document.getElementById('edit_level').value = level;
                    document.getElementById('edit_course').value = course;
                    document.getElementById('edit_profile_display').value = profile;
                    document.getElementById('edit_profile').value = profile;
                    document.getElementById('edit_header').value = header;
                    document.getElementById('edit_option1').value = option1;
                    document.getElementById('edit_option2').value = option2;
                    document.getElementById('edit_option3').value = option3;
                    document.getElementById('edit_option4').value = option4;
                    document.getElementById('edit_correct_option').value = correct;

                    // Fuerza institución del modal a la de sesión
                    const editInstDisplay = document.getElementById('edit_institution_display');
                    const editInstHidden = document.getElementById('edit_institution_hidden');
                    if (editInstDisplay && editInstHidden) {
                        editInstDisplay.value = editInstHidden.value;
                        editInstDisplay.setAttribute('disabled', 'disabled');
                    }
                });
            });

            // Código para cerrar sesión
            window.confirmLogout = function() {
                if (confirm('¿Estás seguro de que quieres cerrar sesión?')) {
                    window.location.href = 'salir.php';
                }
            };

            // Código para filtro por curso
            var courseFilter = document.getElementById('courseFilter');
            if (courseFilter) {
                courseFilter.addEventListener('change', function() {
                    var selectedCourse = this.value;
                    var rows = document.querySelectorAll('#questionsTable tbody tr');
                    rows.forEach(function(row) {
                        var course = row.getAttribute('data-course');
                        if (selectedCourse === '' || course === selectedCourse) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            }

            // Forzar institución en el formulario de agregar
            const instDisplay = document.getElementById('institution_display');
            const instHidden = document.getElementById('institution_hidden');
            if (instDisplay && instHidden) {
                instDisplay.value = instHidden.value;
                instDisplay.setAttribute('disabled', 'disabled');
            }
        });
    </script>

    <footer class="footer">
        <div class="footer-content">
            <img src="Imagenes/prueba.png" alt="Logo" class="footer-logo">
            <p>Información de contacto: Camilo Arvilla, Tel:319 7011921, Correo: algebritos@outlook.com y algebritos2@gmail.com</p>
            <div class="social-icons">
                <a href="https://www.facebook.com/?stype=lo&flo=1&deoia=1&jlou=Afd9wdnNhNCJ6bpzxQ_e45zrT0ZsHWYAJXsTfNsM2IA2mESBVCjci5QUAtyruldjPaPWl3xcXLuA6r0H6pNyqIt9WybDJH42leVAfK80OjAIkA&smuh=1225&lh=Ac-lCTAMxdarlv7K2hM"><img src="Imagenes/f.png" alt="Facebook" class="social-icon"></a>
                <a href="https://www.instagram.com/algebritos/"><img src="Imagenes/i.png" alt="Instagram" class="social-icon"></a>
                <a href="https://x.com/algebritos"><img src="Imagenes/x.png" alt="X" class="social-icon"></a>
                <a href="https://www.threads.com/login"><img src="Imagenes/t.png" alt="Threads" class="social-icon"></a>
            </div>
            <p>2025 Camilo Arvilla. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>




