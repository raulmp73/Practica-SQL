<?php

class AlumnoDAO
{
    private const SELECT_ALUMNOS = 'SELECT id, nombre, apellidos, telefono, email, sexo, fecha_nacimiento FROM alumno';
    private const CAMPOS = ['nombre', 'apellidos', 'password', 'telefono', 'email', 'sexo', 'fecha_nacimiento'];

    public function __construct(private PDO $pdo)
    {
    }

    public function todos(): array
    {
        return $this->pdo->query(self::SELECT_ALUMNOS . ' ORDER BY id')->fetchAll(PDO::FETCH_CLASS, User::class);
    }

    public function buscar(int $id, bool $bloquear = false): User
    {
        $sql = self::SELECT_ALUMNOS . ' WHERE id = ?';
        if ($bloquear) {
            $sql .= ' FOR UPDATE';
        }
        $consulta = $this->pdo->prepare($sql);
        $consulta->execute([$id]);
        $alumno = $consulta->fetchObject(User::class);
        if (!$alumno) {
            throw new OutOfBoundsException('Alumno no encontrado.');
        }
        return $alumno;
    }

    private function recogerCampos(array $formulario): array
    {
        $datos = [];
        foreach (self::CAMPOS as $campo) {
            $valor = $formulario[$campo] ?? '';
            if (!is_string($valor)) {
                throw new InvalidArgumentException("Valor incorrecto en $campo.");
            }
            if (trim($valor) !== '') {
                $datos[$campo] = $campo === 'password' ? password_hash($valor, PASSWORD_DEFAULT) : trim($valor);
            }
        }
        return $datos;
    }

    public function crear(array $formulario): User
    {
        $datos = $this->recogerCampos($formulario);
        foreach (['nombre', 'apellidos', 'password', 'fecha_nacimiento'] as $campo) {
            if (!isset($datos[$campo])) {
                throw new InvalidArgumentException("Falta el campo $campo.");
            }
        }
        $sql = 'INSERT INTO alumno (nombre, apellidos, password, fecha_nacimiento, telefono, email, sexo)
                VALUES (:nombre, :apellidos, :password, :fecha_nacimiento, :telefono, :email, :sexo)';
        $datos += ['telefono' => null, 'email' => null, 'sexo' => null];
        $this->pdo->prepare($sql)->execute($datos);
        return $this->buscar((int) $this->pdo->lastInsertId());
    }

    public function modificar(int $id, array $formulario): User
    {
        $alumno = $this->buscar($id, true);
        $datos = $this->recogerCampos($formulario);
        if (!$datos) {
            return $alumno;
        }
        $cambios = [];
        foreach (array_keys($datos) as $campo) {
            $cambios[] = "$campo = :$campo";
        }
        $sql = 'UPDATE alumno SET ' . implode(', ', $cambios) . ' WHERE id = :id';
        $datos['id'] = $id;
        $this->pdo->prepare($sql)->execute($datos);
        return $this->buscar($id);
    }

    public function borrar(int $id): User
    {
        $alumno = $this->buscar($id, true);
        $this->pdo->prepare('DELETE FROM alumno WHERE id = ?')->execute([$id]);
        return $alumno;
    }
}
