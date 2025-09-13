<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cuenta - Algebritos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="styles.css">
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
                <?php
                session_start();
                include 'db_connection.php';

                if (!isset($_SESSION['user_id']) || !isset($_SESSION['profile']) || !isset($_SESSION['institution'])) {
                    header("Location: Inicio_Sesion.php");
                    exit();
                }

                $user_id = $_SESSION['user_id'];

                $sql = "SELECT username, email, institution, profile, Course, password, created_at FROM users WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->num_rows > 0 ? $result->fetch_assoc() : null;
                $stmt->close();

                if (!$user) {
                    echo "<div class='alert alert-danger'>Error: Usuario no encontrado.</div>";
                    $conn->close();
                    exit();
                }

                $menu = [
                    ["name" => "Inicio", "link" => "inicio.php"],
                    ["name" => "Descarga", "link" => "descarga.php"],
                    ["name" => "Preguntas", "link" => "preguntas.php"],
                    ["name" => "Resultados", "link" => "puntajes.php"],
                    ["name" => "Cuenta", "link" => "cuenta.php", "active" => true],
                    ["name" => "Salir", "link" => "#", "onclick" => "confirmLogout()", "icon" => "<i class='bi bi-box-arrow-right'></i>"]
                ];
                ?>
                <ul class="navbar-nav">
                    <?php foreach ($menu as $item): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo isset($item['active']) && $item['active'] ? 'active' : ''; ?>" 
                               href="<?php echo $item['link']; ?>" 
                               <?php echo isset($item['onclick']) ? 'onclick="' . $item['onclick'] . '"' : ''; ?>>
                                <?php echo isset($item['icon']) ? $item['icon'] . ' ' : ''; ?><?php echo $item['name']; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="content-container">
        <h1 class="mb-4">Mi Cuenta</h1>
        <?php
        $error = '';
        $success = '';

        if (isset($_POST['change_password'])) {
            $current_password = $_POST['current_password'];
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];

            if (!password_verify($current_password, $user['password'])) {
                $error = "La contraseña actual es incorrecta.";
            } elseif ($new_password !== $confirm_password) {
                $error = "La nueva contraseña y la confirmación no coinciden.";
            } elseif (strlen($new_password) < 6) {
                $error = "La nueva contraseña debe tener al menos 6 caracteres.";
            } else {
                $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
                $sql = "UPDATE users SET password = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("si", $hashed_password, $user_id);
                if ($stmt->execute()) {
                    $success = "Contraseña actualizada exitosamente.";
                    $user['password'] = $hashed_password;
                } else {
                    $error = "Error al actualizar la contraseña: " . $stmt->error;
                }
                $stmt->close();
            }
        }

        if (isset($_POST['change_username'])) {
            $new_username = trim($_POST['new_username']);

            if (empty($new_username)) {
                $error = "El nombre de usuario no puede estar vacío.";
            } elseif (strlen($new_username) < 3) {
                $error = "El nombre de usuario debe tener al menos 3 caracteres.";
            } else {
                $sql = "SELECT id FROM users WHERE username = ? AND id != ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("si", $new_username, $user_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $error = "El nombre de usuario ya está en uso.";
                } else {
                    $sql = "UPDATE users SET username = ? WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("si", $new_username, $user_id);
                    if ($stmt->execute()) {
                        $_SESSION['username'] = $new_username;
                        $user['username'] = $new_username;
                        $success = "Nombre de usuario actualizado exitosamente.";
                    } else {
                        $error = "Error al actualizar el nombre de usuario: " . $stmt->error;
                    }
                }
                $stmt->close();
            }
        }

        if (isset($_POST['change_course'])) {
            $new_course = trim($_POST['new_course']);

            if (empty($new_course)) {
                $error = "El curso no puede estar vacío.";
            } elseif (strlen($new_course) > 10) {
                $error = "El curso no puede exceder los 10 caracteres.";
            } else {
                $sql = "UPDATE users SET Course = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("si", $new_course, $user_id);
                if ($stmt->execute()) {
                    $user['Course'] = $new_course;
                    $success = "Curso actualizado exitosamente.";
                } else {
                    $error = "Error al actualizar el curso: " . $stmt->error;
                }
                $stmt->close();
            }
        }
        ?>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Información de la Cuenta</h5>
                <p class="card-text"><strong>Usuario:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                <p class="card-text"><strong>Correo:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                <p class="card-text"><strong>Institución:</strong> <?php echo htmlspecialchars($user['institution']); ?></p>
                <p class="card-text"><strong>Perfil:</strong> <?php echo htmlspecialchars($user['profile']); ?></p>
                <p class="card-text"><strong>Curso:</strong> <?php echo htmlspecialchars($user['Course']); ?></p>
                <p class="card-text"><strong>Fecha de Creación:</strong> <?php echo htmlspecialchars($user['created_at']); ?></p>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Cambiar Contraseña</h5>
                <form method="POST" action="">
                    <div class="mb-3 position-relative">
                        <label class="form-label">Contraseña Actual:</label>
                        <input type="password" class="form-control" name="current_password" id="currentPassword" required>
                        <i class="bi bi-eye-slash position-absolute password-toggle" style="right: 10px; top: 70%; cursor: pointer;" onclick="togglePassword('currentPassword', this)"></i>
                    </div>
                    <div class="mb-3 position-relative">
                        <label class="form-label">Nueva Contraseña:</label>
                        <input type="password" class="form-control" name="new_password" id="newPassword" required>
                        <i class="bi bi-eye-slash position-absolute password-toggle" style="right: 10px; top: 70%; cursor: pointer;" onclick="togglePassword('newPassword', this)"></i>
                    </div>
                    <div class="mb-3 position-relative">
                        <label class="form-label">Confirmar Nueva Contraseña:</label>
                        <input type="password" class="form-control" name="confirm_password" id="confirmPassword" required>
                        <i class="bi bi-eye-slash position-absolute password-toggle" style="right: 10px; top: 70%; cursor: pointer;" onclick="togglePassword('confirmPassword', this)"></i>
                    </div>
                    <button type="submit" name="change_password" class="btn btn-primary">Actualizar Contraseña</button>
                </form>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Cambiar Nombre de Usuario</h5>
                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">Nuevo Nombre de Usuario:</label>
                        <input type="text" class="form-control" name="new_username" required>
                    </div>
                    <button type="submit" name="change_username" class="btn btn-primary">Actualizar Nombre de Usuario</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Cambiar Curso</h5>
                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">Nuevo Curso (ej. 9a, 1c):</label>
                        <input type="text" class="form-control" name="new_course" required>
                    </div>
                    <button type="submit" name="change_course" class="btn btn-primary">Actualizar Curso</button>
                </form>
            </div>
        </div>
    </div>

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
        function confirmLogout() {
            if (confirm('¿Estás seguro de que quieres cerrar sesión?')) {
                window.location.href = 'salir.php';
            }
        }

        function togglePassword(inputId, icon) {
            const input = document.getElementById(inputId);
            input.type = input.type === 'password' ? 'text' : 'password';
            icon.classList.toggle('bi-eye-slash');
            icon.classList.toggle('bi-eye');
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>