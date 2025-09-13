<?php
session_start(); // Iniciar la sesión
session_unset(); // Eliminar todas las variables de sesión
session_destroy(); // Destruir la sesión
header("Location: Index.php"); // Redirigir a la página de inicio de sesión
exit();
?>