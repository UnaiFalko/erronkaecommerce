<?php
// Punto de entrada de la aplicación: carga la configuración y procesa la URL.
require_once __DIR__ . '/../app/core/bootstrap.php';
Router::dispatch();
