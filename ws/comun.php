<?php

require_once __DIR__ . '/../clases/Conexion.php';
require_once __DIR__ . '/../clases/User.php';
require_once __DIR__ . '/../clases/AlumnoDAO.php';

function leerId(): int
{
    $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($id === false) {
        throw new InvalidArgumentException('El id debe ser un entero positivo.');
    }
    return $id;
}

function atender(string $metodo, string $mensaje, callable $operacion, bool $escritura = false): void
{
    header('Content-Type: application/json; charset=utf-8');
    $pdo = null;
    try {
        if ($_SERVER['REQUEST_METHOD'] !== $metodo) {
            header('Allow: ' . $metodo);
            throw new InvalidArgumentException("Utiliza el método $metodo.", 405);
        }
        $pdo = Conexion::abrir();
        if ($escritura) {
            $pdo->beginTransaction();
        }
        $datos = $operacion(new AlumnoDAO($pdo));
        if ($escritura) {
            $pdo->commit();
        }
        $respuesta = ['success' => true, 'message' => $mensaje, 'data' => $datos];
    } catch (Throwable $error) {
        if ($pdo && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        if ($error instanceof InvalidArgumentException) {
            http_response_code($error->getCode() === 405 ? 405 : 400);
            $mensaje = $error->getMessage();
        } elseif ($error instanceof OutOfBoundsException) {
            http_response_code(404);
            $mensaje = $error->getMessage();
        } else {
            http_response_code(500);
            error_log($error->getMessage());
            $mensaje = 'No se pudo completar la operación.';
        }
        $respuesta = ['success' => false, 'message' => $mensaje, 'data' => null];
    }
    echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
}
