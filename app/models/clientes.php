<?php

class Clientes extends Validator
{
    // Declaración de atributos (propiedades).
    private $id = null;
    private $nombres = null;
    private $apellidos = null;
    private $estado = null;
    private $dui = null;
    private $correo = null;
    private $clave = null;
    private $accion = null;

    //METODOS PARA ASIGNAR EL VALOR A LOS ATRIBUTOS
    public function setId($value)
    {
        if ($this->validateNaturalNumber($value)) {
            $this->id = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setEstado($value)
    {
        if ($this->validateNaturalNumber($value)) {
            $this->estado = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setNombres($value)
    {
        if ($this->validateAlphanumeric($value, 1, 40)) {
            $this->nombres = $value;
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

    public function setApellidos($value)
    {
        if ($this->validateAlphanumeric($value, 1, 40)) {
            $this->apellidos = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setDui($value)
    {
        if ($this->validateDUI($value)) {
            $this->dui = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setCorreo($value)
    {
        if ($this->validateEmail($value)) {
            $this->correo = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setClave($value)
    {
        if ($this->validatePassword($value)) {
            $this->clave = $value;
            return true;
        } else {
            return false;
        }
    }

    // METODOS GET PARA OBTENER EL VALOR DE LAS VARIABLES 
    public function getId()
    {
        return $this->id;
    }

    public function getNombre()
    {
        return $this->nombres;
    }

    public function getApellido()
    {
        return $this->apellidos;
    }

    public function getDui()
    {
        return $this->dui;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function getCorreo()
    {
        return $this->correo;
    }

    public function getClave()
    {
        return $this->clave;
    }

    //Metodo search de clientes
    public function searchRows($value)
    {
        $sql = 'SELECT idCliente , estado ,nombres,apellidos,dui,correo_electronico
        FROM clientes c
        WHERE correo_electronico ILIKE ? OR dui ILIKE ?
        ORDER BY nombres';
        $params = array("%$value%", "%$value%");
        return Database::getRows($sql, $params);
    }

    //Metodo para registrar a un cliente
    public function createRow()
    {
        // Se encripta la clave por medio del algoritmo bcrypt que genera un string de 60 caracteres.
        $hash = password_hash($this->clave, PASSWORD_DEFAULT);
        $sql = 'INSERT INTO clientes(idcliente, estado, nombres, apellidos, dui, correo_electronico, clave, fecharegistro) 
        VALUES (default, default, ?, ?, ?, ?, ?, default)';
        $params = array($this->nombres, $this->apellidos, $this->dui, $this->correo, $hash);
        return Database::executeRow($sql, $params);
    }

    //Metodo para cargar los registros de cliente
    public function readAll()
    {
        $sql = "SELECT idCliente, estado,nombres,apellidos,dui,correo_electronico
        FROM clientes c
        ORDER BY nombres";
        $params = null;
        return Database::getRows($sql, $params);
    }

    //Metodo para cargar los datos de un cliente
    public function readOne()
    {
        $sql = 'SELECT idCliente, nombres, apellidos,dui,correo_electronico,clave
        FROM clientes c
        WHERE idCliente = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

    //Metodo para actualizar los datos de un cliente
    public function updateRow()
    {
        $sql = 'UPDATE clientes
        SET nombres = ?,apellidos = ?,correo_electronico = ?
        WHERE idCliente = ?';
        $params = array($this->nombres, $this->apellidos, $this->correo, $this->id);
        return Database::executeRow($sql, $params);
    }

    //Metodo para eliminar un cliente
    public function deleteRow()
    {
        $sql = 'UPDATE clientes set estado = ? where idCliente = ?';
        $params = array($this->accion,$this->id);
        return Database::executeRow($sql, $params);
    }

    //Metodo para obtener el correo del cliente para el log in
    public function checkUser($correo)
    {
        $sql = 'SELECT idcliente, estado, nombres FROM clientes WHERE correo_electronico = ?';
        $params = array($correo);
        if ($data = Database::getRow($sql, $params)) {
            $this->id = $data['idcliente'];
            $this->estado = $data['estado'];
            $this->correo = $correo;
            return true;
        } else {
            return false;
        }
    }

    // Funcion para verificar si el usuario esta activo requiere del parametro del nombre de usuario
    public function checkState($usuario)
    {
        // Declaracion de la sentencia SQL 
        $sql = 'SELECT idcliente from clientes where correo_electronico = ? and estado = true';
        $params = array($usuario);
        // Se compara si los datos ingresados coinciden con el resultado obtenido de la base de datos
        if ($data = Database::getRow($sql, $params)) {
            return true;
        } else {
            return false;
        }
    }

    //Metodo para obtener la contraseña del cliente para el log in
    public function checkPassword($password)
    {
        $sql = 'SELECT clave FROM clientes WHERE idcliente = ?';
        $params = array($this->id);
        $data = Database::getRow($sql, $params);
        $this->clave = $password;
        if (password_verify($password, $data['clave'])) {
            return true;
        } else {
            return false;
        }
    }

    //Metodo para cargar las facturas de un cliente
    public function cargarFacturas()
    {
        $sql = "SELECT concat(c.Categoria,' ',p.Producto) as producto,cl.idcliente, d.PrecioUnitario,d.Cantidad
		from DetallePedidos d
        inner join Facturas f on d.pedido = f.IdFactura
		inner join Clientes cl on f.cliente = cl.idcliente
        inner join Productos p on d.Producto = p.IdProducto
        inner join Categorias c on p.Categoria = c.idCategoria
        where cl.idcliente = ? and f.estado=2 and f.fecha = current_date";
        $params = array($this->id);
        return Database::getRows($sql, $params);
    }
}
