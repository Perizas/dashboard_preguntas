<!DOCTYPE html>
<html lang="es">
<head>
    <title>Descarga</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Logo encima del header con enlace a inicio -->
    <div class="logo-container">
        <a href="inicio.php">
            <img src="Imagenes/prueba.png" alt="Logo" class="logo-img">
        </a>
    </div>

    <!-- Header centrado -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="inicio.php">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="descarga.php">Descarga</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="preguntas.php">Preguntas</a>
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

    <!-- Contenedor de contenido con texto y botón -->
    <div class="content-container">
        <h1 class="mb-4">Descarga</h1>
        <p>Aquí encontrará disponible el archivo .rar para la descarga del programa Algebritos. Este archivo ha sido preparado para que pueda extraer la aplicación de manera sencilla y segura en su equipo.</p>
        <p>El objetivo de poner a su disposición este archivo es facilitar el acceso al software, garantizando que todos los usuarios interesados puedan obtenerlo sin complicaciones. Solo debe hacer clic en el enlace de descarga, guardar el archivo en su computador y, posteriormente, extraer el contenido del RAR utilizando un programa como WinRAR o 7-Zip, siguiendo los pasos que aparecerán en pantalla.</p>
        <p>De esta manera, podrá contar con Algebritos en su dispositivo y comenzar a disfrutar de todas sus funciones y beneficios educativos.</p>
        <div class="text-center">
            <a href="Imagenes/Algebritos.rar" class="btn btn-primary btn-lg mt-4" download>Descargar</a>
        </div>
    </div>

    <!-- Footer -->
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

    <!-- Bootstrap JS CDN (necesario para modales, aunque no los usemos aún) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Capturar clic en botones de edición (script existente)
        var editButtons = document.querySelectorAll('.edit-btn');
        editButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                var id = this.getAttribute('data-id');
                var level = this.getAttribute('data-level');
                var header = this.getAttribute('data-header');
                var option1 = this.getAttribute('data-option1');
                var option2 = this.getAttribute('data-option2');
                var option3 = this.getAttribute('data-option3');
                var option4 = this.getAttribute('data-option4');
                var correct = this.getAttribute('data-correct');

                document.getElementById('edit_id').value = id;
                document.getElementById('edit_level').value = level;
                document.getElementById('edit_header').value = header;
                document.getElementById('edit_option1').value = option1;
                document.getElementById('edit_option2').value = option2;
                document.getElementById('edit_option3').value = option3;
                document.getElementById('edit_option4').value = option4;
                document.getElementById('edit_correct_option').value = correct;
            });
        });

        // Función para confirmar cierre de sesión
        window.confirmLogout = function() {
            if (confirm('¿Estás seguro de que quieres cerrar sesión?')) {
                window.location.href = 'salir.php';
            }
        };
    });
</script>
</body>
</html>