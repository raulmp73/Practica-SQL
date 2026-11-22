<?php

class Conexion
{
    public static function abrir(): PDO
    {
        return new PDO('mysql:host=127.0.0.1;dbname=colegio;charset=utf8mb4', 'root', '', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }
}
