<?php

require_once __DIR__ . '/comun.php';

atender('POST', 'Alumno creado', function (AlumnoDAO $dao) {
    return $dao->crear($_POST);
}, true);
