<?php
/*
*	Clase para manejar la tabla usuarios de la base de datos. Es clase hija de Validator.
*/
class Usuarios extends Validator
{
    // Declaración de atributos (propiedades).
    private $id = null;
    private $correo = null;
    private $alias = null;
    private $clave = null;
    private $tipo = null;
    private $estado = null;
    private $accion = null;
    private $dui = null;
    private $telefono = null;

    // Métodos para asignar el valor a los atributos
    public function setId($value)
    {
        // Validamos el tipo de dato del valor ingresado
        if ($this->validateNaturalNumber($value)) {
            $this->id = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setTelefono($value)
    {
        // Validamos el tipo de dato del valor ingresado
        if ($this->validatePhone($value)) {
            $this->telefono = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setDui($value)
    {
        // Validamos el tipo de dato del valor ingresado
        if ($this->validateDUI($value)) {
            $this->dui = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setCorreo($value)
    {
        // Validamos el tipo de dato del valor ingresado
        if ($this->validateEmail($value)) {
            $this->correo = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setAlias($value)
    {
        // Validamos el tipo de dato del valor ingresado
        if ($this->validateAlphanumeric($value, 1, 50)) {
            $this->alias = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setAccion($value)
    {
        $this->accion = $value;
        return true;
    }

    public function setClave($value)
    {
        // Validamos el tipo de dato del valor ingresado
        if ($this->validatePassword($value)) {
            $this->clave = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setEstado($value)
    {
        // Validamos el tipo de dato del valor ingresado
        if ($this->validateNaturalNumber($value)) {
            $this->estado = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setTipo($value)
    {
        // Validamos el tipo de dato del valor ingresado
        if ($this->validateNaturalNumber($value)) {
            $this->tipo = $value;
            return true;
        } else {
            return false;
        }
    }

    // Funciones para retornar el valor los atributos de la clase
    public function getId()
    {
        return $this->id;
    }
    public function getCorreo()
    {
        return $this->correo;
    }

    public function getAlias()
    {
        return $this->alias;
    }

    public function getClave()
    {
        return $this->clave;
    }

    public function getTelefono()
    {
        return $this->telefono;
    }

    public function getDui()
    {
        return $this->dui;
    }

    public function getTipo()
    {
        return $this->tipo;
    }
    public function getAccion()
    {
        return $this->accion;
    }
    public function getEstado()
    {
        return $this->estado;
    }

    // Funciones verificar si el usuario ingresado existe
    public function checkUser($alias)
    {
        // Creamos la sentencia SQL que contiene la consulta que mandaremos a la base
        $sql = 'SELECT idusuario,t.tipousuario as tipo,usuario,correo_electronico,telefono FROM usuarios u
        INNER JOIN tipousuarios t on t.idtipo = u.tipo WHERE usuario = ?';
        $params = array($alias);
        if ($data = Database::getRow($sql, $params)) {
            $this->id = $data['idusuario'];
            $this->tipo = $data['tipo'];
            $this->correo = $data['correo_electronico'];
            $this->telefono = $data['telefono'];
            $this->alias = $alias;
            $_SESSION['usuario'] = $alias;
            return true;
        } else {
            return false;
        }
    }

    // Funciones verificar si el usuario ingresado existe
    public function getUser($correo)
    {
        // Creamos la sentencia SQL que contiene la consulta que mandaremos a la base
        $sql = 'SELECT usuario FROM usuarios WHERE correo_electronico = ?';
        $params = array($correo);
        if ($data = Database::getRow($sql, $params)) {
            $_SESSION['usuario'] = $data['usuario'];                                                                                            
            return true;
        } else {
            return false;
        }
    }

    // Funciones verificar si la contraseña es correcta
    public function checkPassword($password)
    {
        // Creamos la sentencia SQL que contiene la consulta que mandaremos a la base
        $sql = 'SELECT clave FROM usuarios WHERE idusuario = ?';
        $params = array($this->id);
        $data = Database::getRow($sql, $params);
        $_SESSION['clave'] = $password;
        if (password_verify($password, $data['clave'])) {
            return true;
        } else {
            return false;
        }
    }

    // Funcion para verificar si el usuario esta activo requiere del parametro del nombre de usuario
    public function checkState($usuario)
    {
        // Declaracion de la sentencia SQL 
        $sql = 'SELECT idusuario from usuarios where usuario = ? and estado = true';
        $params = array($usuario);
        // Se compara si los datos ingresados coinciden con el resultado obtenido de la base de datos
        if ($data = Database::getRow($sql, $params)) {
            return true;
        } else {
            return false;
        }
    }

    // Funciones cambiar la clave del usuario
    public function changePassword()
    {
        // Encriptacion de la contraseña
        $hash = password_hash($this->clave, PASSWORD_DEFAULT);
        // Creamos la sentencia SQL que contiene la consulta que mandaremos a la base
        $sql = 'UPDATE usuarios SET clave = ? WHERE idusuario = ?';
        $params = array($hash, $_SESSION['idusuario']);
        return Database::executeRow($sql, $params);
    }

    // Funcion para realizar busqueda filtrada en el sistema
    public function searchRows($value)
    {
        // Creamos la sentencia SQL que contiene la consulta que mandaremos a la base
        $sql = 'SELECT u.idusuario, estado, u.usuario, u.correo_electronico
                FROM usuarios u
                WHERE usuario ILIKE ?
                ORDER BY estado desc';
        $params = array("%$value%");
        return Database::getRows($sql, $params);
    }

    // Funcion para registrar un usuario en la base de datos
    public function createRow()
    {
        // Se encripta la clave por medio del algoritmo bcrypt que genera un string de 60 caracteres.
        $hash = password_hash($this->clave, PASSWORD_DEFAULT);
        // Creamos la sentencia SQL que contiene la consulta que mandaremos a la base
        $sql = 'INSERT INTO usuarios VALUES (?,?,?,?,?,default,default,?,?)';
        $params = array($this->id,$this->tipo,$this->alias, $hash, $this->correo,$this->telefono,$this->dui);
        return Database::executeRow($sql, $params);
    }

    // Funcion para cargar todos los usuarios de la base de datos
    public function readAll()
    {
        // Creamos la sentencia SQL que contiene la consulta que mandaremos a la base
        $sql = 'SELECT u.idusuario, estado,tipo,u.usuario, u.correo_electronico,dui,telefono
        FROM usuarios u
        ORDER BY estado desc';
        $params = null;
        return Database::getRows($sql, $params);
    }

    // Funcion para cargar todos los usuarios de la base de datos
    public function readUserType()
    {
        // Creamos la sentencia SQL que contiene la consulta que mandaremos a la base
        $sql = 'SELECT idtipo,tipousuario FROM tipousuarios';
        $params = null;
        return Database::getRows($sql, $params);
    }

    // Funcion para cargar los datos de un usuario en especifico
    public function readOne()
    {
        // Creamos la sentencia SQL que contiene la consulta que mandaremos a la base
        $sql = 'SELECT idusuario, tipo , estado, usuario,clave,correo_electronico,dui,telefono
                FROM usuarios  where idusuario = ? ';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

    // Funcion para actualizar un usuario en la base de datos
    public function updateRow()
    {
        // Creamos la sentencia SQL que contiene la consulta que mandaremos a la base
        $sql = 'UPDATE usuarios 
            SET correo_electronico = ?, usuario = ?, telefono = ?
            WHERE idusuario = ?';
        $params = array($this->correo, $this->alias, $this->telefono ,$this->id);
        return Database::executeRow($sql, $params);
    }

    // Funcion para eliminar un usuario en la base de datos
    public function deleteRow()
    {
        // Creamos la sentencia SQL que contiene la consulta que mandaremos a la base
        $sql = 'UPDATE usuarios set estado = ? where idusuario = ?';
        $params = array($this->accion,$this->id);
        return Database::executeRow($sql, $params);
    }
}
