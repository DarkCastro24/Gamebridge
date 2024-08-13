<?php
/*
*	Clase para manejar las tablas pedidos y detalle_pedido de la base de datos. Es clase hija de Validator.
*/
class Pedidos extends Validator
{
    // Declaracion de atributos para pedidos
    private $id_pedido = null;
    private $id_detalle = null;
    private $cliente = null;
    private $producto = null;
    private $cantidad = null;
    private $precio = null;
    private $estado = null; 
    private $cantidadStock = null;

    // Declaracion de atributos para direcciones
    private $iddireccion=null;
    private $municipio=null;
    private $direccion=null;
    private $codigopostal=null;
    private $telefonofijo=null;
    private $cmbdireccion=null;

    /*
    *   Métodos para validar y asignar valores de los atributos.
    */

    public function setIdPedido($value)
    {
        // Validamos el tipo de dato del valor ingresado
        if ($this->validateNaturalNumber($value)) {
            $this->id_pedido = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setIdDetalle($value)
    {
        // Validamos el tipo de dato del valor ingresado
        if ($this->validateNaturalNumber($value)) {
            $this->id_detalle = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setCliente($value)
    {
        // Validamos el tipo de dato del valor ingresado
        if ($this->validateNaturalNumber($value)) {
            $this->cliente = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setProducto($value)
    {
        // Validamos el tipo de dato del valor ingresado
        if ($this->validateNaturalNumber($value)) {
            $this->producto = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setCantidad($value)
    {
        // Validamos el tipo de dato del valor ingresado
        if ($this->validateNaturalNumber($value)) {
            $this->cantidad = $value;
            return true;
        } else {
            return false;
        }
    }

    
    public function setCantidadStock($value)
    {
        // Validamos el tipo de dato del valor ingresado
        if ($this->validateNaturalNumber($value)) {
            $this->cantidadStock = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setPrecio($value)
    {
        // Validamos el tipo de dato del valor ingresado
        if ($this->validateMoney($value)) {
            $this->precio = $value;
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

    // Funciones para retornar el valor los atributos de la clase

    public function getIdPedido()
    {
        return $this->id_pedido;
    }

    // Método para verificar si existe un pedido en proceso para seguir comprando, de lo contrario se crea uno.
    public function startOrder()
    {
        $this->estado = 0;

        $sql = 'SELECT idfactura
                FROM facturas
                WHERE estado = ? AND cliente = ?';
        $params = array($this->estado, $_SESSION['id_cliente']);
        if ($data = Database::getRow($sql, $params)) {
            $this->id_pedido = $data['idfactura'];
            return true;
        } else {
            $sql = 'INSERT INTO facturas(idfactura,estado, cliente)
                    VALUES(default,?, ?)';
            $params = array($this->estado, $_SESSION['id_cliente']);
            // Se obtiene el ultimo valor insertado en la llave primaria de la tabla facturas.
            if ($this->id_pedido = Database::getLastRow($sql, $params)) {
                return true;
            } else {
                return false;
            }
        }
    }

    // Método para agregar un producto al carrito de compras.
    public function createDetail()
    {
        // Se realiza una subconsulta para obtener el precio del producto.
        $sql = 'INSERT INTO detallepedidos(pedido, producto,  preciounitario ,cantidad)
        VALUES(?, ?,(SELECT precio FROM productos WHERE idproducto = ?), ?)';
        $params = array($this->id_pedido,$this->producto, $this->producto, $this->cantidad);
        return Database::executeRow($sql, $params);
    }

    // Método para obtener los productos que se encuentran en el carrito de compras.
    public function readOrderDetail()
    {
        // Creamos la sentencia SQL que contiene la consulta que mandaremos a la base
        $sql = 'SELECT iddetallefactura, p.producto, 
        d.preciounitario, d.cantidad,p.imagen,c.categoria,m.marca
        FROM facturas f
        INNER JOIN detallepedidos d ON d.pedido = f.idfactura 
        INNER JOIN productos p ON p.idproducto = d.producto
		INNER JOIN categorias c ON c.idcategoria = p.categoria
		INNER JOIN marcas m ON m.idmarca = p.marca
        WHERE idfactura = ?';
        // Cargamos los parametros en un arreglo
        $params = array($this->id_pedido);
        return Database::getRows($sql,$params);
    }

    public function readHistorial($id_cliente)
    {
        // Creamos la sentencia SQL que contiene la consulta que mandaremos a la base
        $sql = 'SELECT f.idfactura, f.fecha,e.estadofactura as estado, COALESCE(calcularTotal(idfactura),CAST(0 as money)) as total
        FROM facturas f 
        INNER JOIN estadofactura e on f.estado = e.idestado
        WHERE f.cliente = ?
        ORDER BY idfactura DESC';
        // Cargamos los parametros en un arreglo
        $params = array($id_cliente);
        return Database::getRows($sql,$params);
    }

    public function readDetailHistorial($id_pedido)
    {
        // Creamos la sentencia SQL que contiene la consulta que mandaremos a la base
        $sql = 'SELECT iddetallefactura, pedido, p.producto,m.marca, preciounitario, d.cantidad, (preciounitario*d.cantidad) as subtotal ,p.imagen
        FROM detallepedidos d
        INNER JOIN productos p ON d.producto = p.idproducto
        INNER JOIN marcas m ON m.idmarca = p.marca
        WHERE pedido = ?
        ORDER BY (preciounitario*d.cantidad) desc';
        // Cargamos los parametros en un arreglo
        $params = array($id_pedido);
        return Database::getRows($sql,$params);
    }

    public function verificarPedido($idPedido)
    {
        $sql = 'SELECT f.idfactura
        FROM facturas f
        WHERE f.idfactura = ?';
        // Cargamos los parametros en un arreglo 
        $params = array($idPedido);
        return Database::getRows($sql, $params);
    }

    // Método para finalizar un pedido por parte del cliente.
    public function finishOrder()
    {
        // Se establece la zona horaria local para obtener la fecha del servidor.
        $this->estado = 2;
        $sql = 'UPDATE facturas
                SET estado = ?
                WHERE idfactura = ?';
        $params = array($this->estado, $_SESSION['id_pedido']);
        return Database::executeRow($sql, $params);
    }

    // Método para actualizar la cantidad de un producto agregado al carrito de compras.
    public function updateDetail()
    {
        $sql = 'UPDATE detallepedidos
                SET cantidad = ?
                WHERE iddetallefactura = ? AND pedido = ?';
        $params = array($this->cantidad, $this->id_detalle, $_SESSION['id_pedido']);
        return Database::executeRow($sql, $params);
    }

    // Método para eliminar un producto que se encuentra en el carrito de compras.
    public function deleteDetail()
    {
        $sql = 'DELETE FROM detallepedidos
                WHERE iddetallefactura = ? AND pedido = ?';
        $params = array($this->id_detalle, $_SESSION['id_pedido']);
        return Database::executeRow($sql, $params);
    }
        

    // METODOS PARA GESTION DEL 
    public function restarStock()
    {

        $newstock = $this->cantidadStock - $this->cantidad; {
            $sql = 'UPDATE productos set cantidad=? where idproducto=?';
            $params = array($newstock, $this->producto);
            return Database::executeRow($sql, $params);
        }
    }

    // Metodo para restaurar stock
    public function restoreStock()
    {

        $newstock = $this->cantidad + $this->cantidadStock; {
            $sql = 'UPDATE productos set cantidad=? where idproducto=?';
            $params = array($newstock, $this->producto);
            return Database::executeRow($sql, $params);
        }
    }

    //Metodo para actualizar stock
    public function updateStock($cantidad)
    {
        $sql = 'UPDATE productos set cantidad = ? where idproducto = ?';
        $params = array($cantidad, $this->producto);
        return Database::executeRow($sql,$params);
    }

    //Metodo para actualizar stock en el pedido
    public function updateOrderStock($cantidad)
    {
        $sql = 'UPDATE detallepedidos set cantidad = ? where iddetallefactura = ?  AND pedido = ?';
        $params = array($cantidad, $this->id_detalle,$_SESSION['idfactura']);
        return Database::executeRow($sql,$params);
    }
    
    
}
