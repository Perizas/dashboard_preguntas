<!DOCTYPE html>
<html lang="es">
<head>
    <title>Inicio</title>
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
            <a class="navbar-brand" href="#"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="inicio.php">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="descarga.php">Descarga</a>
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

    <!-- Contenedor de contenido con carrusel, descripción y personajes -->
    <div class="content-container">
        <!-- Carrusel de imágenes -->
        <div class="carousel-container">
            <button class="carousel-btn prev" onclick="changeSlide(-1)"><i class="bi bi-chevron-left"></i></button>
            <div class="carousel-slide active" style="background-image: url('Imagenes/Libro.jpg');"></div>
            <div class="carousel-slide" style="background-image: url('Imagenes/Biblioteca.jpg');"></div>
            <div class="carousel-slide" style="background-image: url('Imagenes/Salon.jpg');"></div>
            <div class="carousel-slide" style="background-image: url('Imagenes/Tablero.jpg');"></div>
            <button class="carousel-btn next" onclick="changeSlide(1)"><i class="bi bi-chevron-right"></i></button>
        </div>

        <!-- Información o descripción del juego -->
        <h1 class="mb-4">Descripción del Juego</h1>
        <p>Algebritos surge con la finalidad de dar solución a los planteamientos del presente proyecto, en este videojuego se busca que a través de explicaciones sencillas y planteamientos de minijuegos cómodos ayudar a los estudiantes de nivel colegio sobre los grados noveno a undécimo, con su conocimiento y manejo de álgebra básica.</p>
        <p>El título surge de mezclar el nombre de álgebra y cerebritos, indicando su objetivo de instruir a los jóvenes en el campo algebraico. En Algebritos se presentan personajes ficticios con un enfoque monstruoso y en un ámbito escolar, el objetivo será a lo largo de tres días acompañar a nuestros personajes Blumio y Luminara a prepararse para resolver un examen, en cada día se presentarán tres macro temas divididos en varios subtemas. Los cuales son:</p>
        <ul>
            <li><strong>Día 1: Fundamentos Algebraicos</strong>
                <ul>
                    <li>¿Qué es el Álgebra?</li>
                    <li>Expresión algebraica</li>
                    <li>Partes de una expresión algebraica</li>
                    <li>Polinomios</li>
                    <li>Grados</li>
                </ul>
            </li>
            <li><strong>Día 2: Casos Algebraicos</strong>
                <ul>
                    <li>Factorización:
                        <ul>
                            <li>Factor común</li>
                            <li>Factor común por Agrupación</li>
                            <li>Diferencia de Cuadrados Perfectos</li>
                            <li>Trinomio cuadrado perfecto</li>
                            <li>Trinomio de la forma x²n + bxn + c</li>
                            <li>Trinomio de la forma ax²n + bxn + c</li>
                        </ul>
                    </li>
                </ul>
            </li>
            <li><strong>Día 3: Operaciones Básicas</strong>
                <ul>
                    <li>Suma y Resta de expresiones algebraicas</li>
                    <li>Multiplicación y división de monomios y polinomios</li>
                    <li>Simplificación de expresiones</li>
                    <li>Operaciones combinadas</li>
                </ul>
            </li>
        </ul>
        <p>En cada día acompañaremos a nuestros personajes y un narrador en la aventura por aprender cada concepto, se presentará una explicación de cada concepto y al final se desarrollarán una serie de preguntas con el fin de poner en práctica lo aprendido en cada día, cada día cerrará con 5 de estas actividades donde acertar 3 o menos impedirá a los jugadores de seguir en la historia, mientras que de 4 a 5 se permitirá continuar, pero en estas actividades los jugadores tendrán la posibilidad de consultar nuevamente la información de las explicaciones a forma de ayuda. Finalmente, el examen final será realizado a modo de última tarea, pero mientras al finalizar cada día se presentarán actividades asociadas a lo explicado en el examen se presentará cualquier pregunta asociada a cualquier tema e igual solo se puede aprobar con 4 o 5 y sus preguntas saldrán de las preguntas generadas en cada día.</p>
        <p>Por otro lado, antes de cada ronda de preguntas los usuarios tendrán la posibilidad de consultar sus dudas o preguntas por medio de la IA de Google Gemini la cual se encontrará integrada dentro del videojuego, así como un Dashboard que servirá para administrar las preguntas por un docente y ver el progreso de los estudiantes.</p>

        <!-- Imágenes de Blumi y Lumi -->
        <div class="character-comparison">
            <div class="character-card">
                <img src="Imagenes/blumi.jpg" alt="Blumio" class="character-img">
                <div class="character-info">
                    <p>Blumio es un monstruito azul brillante con unas gafas enormes y redondas que casi se le escurren por la nariz. Es tímido al principio, pero detrás de su sonrisa nerviosa se esconde una mente brillante. Ama resolver acertijos, leer libros raros y construir cosas extrañas con chatarra. Si logras ganarte su confianza, tendrás al mejor compañero para aventuras… y para los exámenes sorpresa.</p>
                </div>
            </div>
            <div class="character-card">
                <img src="Imagenes/lumi.jpg" alt="Lumiara" class="character-img">
                <div class="character-info">
                    <p>Lumiara es una monstruita amarilla chispeante, con energía de sobra y una mirada decidida. Tiene un carácter fuerte y no le teme a nada… bueno, casi nada. Aunque a veces se frustra si las cosas no le salen a la primera, siempre se lanza de frente a cada desafío con una sonrisa desafiante. Carismática, algo impulsiva y con un corazón enorme, Lumiara es de las que inspiran a los demás sin siquiera notarlo.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS CDN (necesario para modales, aunque no los usemos aún) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- JavaScript para el carrusel automático y manual -->
    <script>
        let currentSlide = 0;
        const slides = document.querySelectorAll('.carousel-slide');
        const slideInterval = 20000; // 20 segundos

        function showSlide(index) {
            slides.forEach((slide, i) => {
                slide.classList.remove('active');
                if (i === index) {
                    slide.classList.add('active');
                }
            });
        }

        function changeSlide(direction) {
            currentSlide += direction;
            if (currentSlide < 0) currentSlide = slides.length - 1;
            if (currentSlide >= slides.length) currentSlide = 0;
            showSlide(currentSlide);
        }

        function nextSlide() {
            changeSlide(1);
        }

        // Iniciar el carrusel
        showSlide(currentSlide);
        setInterval(nextSlide, slideInterval);
    </script>

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
</html>
