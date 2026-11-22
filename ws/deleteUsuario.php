<?php

require_once __DIR__ . '/comun.php';

atender('GET', 'Alumno eliminado', function (AlumnoDAO $dao) {
    return $dao->borrar(leerId());
}, true);
