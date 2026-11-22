<?php

require_once __DIR__ . '/comun.php';

atender('GET', 'Consulta realizada', function (AlumnoDAO $dao) {
    if (isset($_GET['id'])) {
        return $dao->buscar(leerId());
    }
    return $dao->todos();
});
