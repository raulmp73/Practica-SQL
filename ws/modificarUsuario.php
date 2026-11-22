<?php

require_once __DIR__ . '/comun.php';

atender('POST', 'Alumno actualizado', function (AlumnoDAO $dao) {
    return $dao->modificar(leerId(), $_POST);
}, true);
