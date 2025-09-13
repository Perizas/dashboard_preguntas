<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio de Sesión - Algebritos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="logo-container">
        <a href="Inicio_Sesion.php"><img src="Imagenes/prueba.png" alt="Logo" class="logo-img"></a>
    </div>

    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link active" href="descarga2.php">Descarga</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="content-container">
        <h1 class="mb-4">Inicio de Sesión</h1>
        <?php
        include 'db_connection.php';
        session_start();

        // LOGIN
        if (isset($_POST['login'])) {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $sql = "SELECT * FROM users WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['profile'] = $user['profile'];
                    $_SESSION['institution'] = $user['institution'];
                    header("Location: inicio.php");
                    exit();
                } else {
                    echo "<div class='alert alert-danger'>Contraseña incorrecta.</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>Correo no encontrado.</div>";
            }
            $stmt->close();
        }

        // REGISTRO
        if (isset($_POST['register'])) {
            $username = trim($_POST['reg_username']);
            $email = trim($_POST['reg_email']);
            $institution = trim($_POST['reg_institution']);
            $profile = $_POST['reg_profile'];
            $course = trim($_POST['reg_course']);
            $password = password_hash($_POST['reg_password'], PASSWORD_BCRYPT);

            // Si es Docente, forzar que el curso sea "Docente"
            if ($profile === "Docente") {
                $course = "Docente";
            }

            // Validar institution
            $valid_institutions = ['Instituto Técnico Distrital Julio Flórez', 'Prueba'];
            if (empty($institution) || !in_array($institution, $valid_institutions)) {
                echo "<div class='alert alert-danger'>Por favor, selecciona una institución válida.</div>";
            }
            // Validar Course solo si es Alumno
            elseif ($profile === "Alumno" && (empty($course) || strlen($course) > 10)) {
                echo "<div class='alert alert-danger'>El curso no puede estar vacío ni exceder los 10 caracteres.</div>";
            }
            // Validar Profile
            elseif (!in_array($profile, ['Docente', 'Alumno'])) {
                echo "<div class='alert alert-danger'>Por favor, selecciona un perfil válido.</div>";
            } else {
                $sql = "INSERT INTO users (username, email, institution, profile, course, password) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssss", $username, $email, $institution, $profile, $course, $password);

                if ($stmt->execute()) {
                    echo "<div class='alert alert-success'>Registro exitoso. Puedes iniciar sesión.</div>";
                } else {
                    echo "<div class='alert alert-danger'>Error al registrar: " . htmlspecialchars($conn->error) . "</div>";
                }
                $stmt->close();
            }
        }
        ?>

        <!-- LOGIN FORM -->
        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label">Correo:</label>
                <input type="email" class="form-control" name="email" required>
            </div>
            <div class="mb-3 position-relative">
                <label class="form-label">Contraseña:</label>
                <input type="password" class="form-control" name="password" id="loginPassword" required>
                <i class="bi bi-eye-slash position-absolute password-toggle" style="right: 10px; top: 70%; cursor: pointer;" onclick="togglePassword('loginPassword', this)"></i>
            </div>
            <button type="submit" name="login" class="btn btn-primary">Continuar</button>
            <button type="button" class="btn btn-secondary ms-2" data-bs-toggle="modal" data-bs-target="#registerModal">Registrarse</button>
        </form>
    </div>

    <!-- MODAL REGISTRO -->
    <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registerModalLabel">Registro</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Nombre Completo:</label>
                            <input type="text" class="form-control" name="reg_username" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Correo:</label>
                            <input type="email" class="form-control" name="reg_email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Institución:</label>
                            <select class="form-select" name="reg_institution" required>
                                <option value="" disabled selected>Selecciona una institución</option>
                                <option value="Instituto Técnico Distrital Julio Flórez">Instituto Técnico Distrital Julio Flórez</option>
                                <option value="Prueba">Prueba</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Perfil:</label>
                            <select class="form-select" name="reg_profile" id="reg_profile" required onchange="toggleCourse()">
                                <option value="" disabled selected>Selecciona un perfil</option>
                                <option value="Docente">Docente</option>
                                <option value="Alumno">Alumno</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Curso (ej. 9a, 10c):</label>
                            <input type="text" class="form-control" name="reg_course" id="reg_course" required>
                        </div>
                        <div class="mb-3 position-relative">
                            <label class="form-label">Contraseña:</label>
                            <input type="password" class="form-control" name="reg_password" id="registerPassword" required>
                            <i class="bi bi-eye-slash position-absolute password-toggle" style="right: 10px; top: 70%; cursor: pointer;" onclick="togglePassword('registerPassword', this)"></i>
                        </div>
                        <button type="submit" name="register" class="btn btn-primary">Registrarse</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="footer-content">
            <img src="Imagenes/prueba.png" alt="Logo" class="footer-logo">
            <p>Información de contacto: Camilo Esteban Arvilla Méndez, Tel: 319 7011921, correo: algebritos@outlook.com, algebritos@gmail.com</p>
            <div class="social-icons">
                <a href="https://www.facebook.com/"><img src="Imagenes/f.png" alt="Facebook" class="social-icon"></a>
                <a href="https://www.instagram.com/algebritos/"><img src="Imagenes/i.png" alt="Instagram" class="social-icon"></a>
                <a href="https://x.com/algebritos"><img src="Imagenes/x.png" alt="X" class="social-icon"></a>
                <a href="https://www.threads.com/login"><img src="Imagenes/t.png" alt="Threads" class="social-icon"></a>
            </div>
            <p>2025 Camilo Arvilla. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        function togglePassword(inputId, icon) {
            const input = document.getElementById(inputId);
            input.type = input.type === 'password' ? 'text' : 'password';
            icon.classList.toggle('bi-eye-slash');
            icon.classList.toggle('bi-eye');
        }

        function toggleCourse() {
            const profile = document.getElementById("reg_profile").value;
            const courseInput = document.getElementById("reg_course");

            if (profile === "Docente") {
                courseInput.value = "Docente";
                courseInput.readOnly = true;
            } else {
                courseInput.value = "";
                courseInput.readOnly = false;
            }
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>




