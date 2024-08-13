
<?php
/*
*	Clase para manejar la tabla productos de la base de datos. Es clase hija de Validator.
*/
class Productos extends Validator
{
    // Declaración de atributos (propiedades).
    private $id = null;
    private $categoria = null;
    private $estado = null;
    private $marca = null;
    private $producto = null;
    private $precio = null;
    private $descripcion = null;
    private $imagen = null;
    private $accion = null;
    private $ruta = '../../../resources/img/productos/';

    /*
    *   Métodos para asignar valores a los atributos.
    */
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

    public function setCategoria($value)
    {
        if ($this->validateNaturalNumber($value)) {
            $this->categoria = $value;
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

    public function setMarca($value)
    {
        if ($this->validateNaturalNumber($value)) {
            $this->marca = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setProducto($value)
    {
        if ($this->validateAlphanumeric($value, 1, 50)) {
            $this->producto = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setPrecio($value)
    {
        if ($this->validateMoney($value)) {
            $this->precio = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setDescripcion($value)
    {
        if ($this->validateAlphanumeric($value, 1, 150)) {
            $this->descripcion = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setImagen($file)
    {
        if ($this->validateImageFile($file, 500, 500)) {
            $this->imagen = $this->getImageName();
            return true;
        } else {
            return false;
        }
    }

    /*
    *   Métodos para obtener valores de los atributos.
    */
    public function getId()
    {
        return $this->id;
    }

    public function getCategoria()
    {
        return $this->categoria;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function getMarca()
    {
        return $this->marca;
    }

    public function getProducto()
    {
        return $this->producto;
    }

    public function getPrecio()
    {
        return $this->precio;
    }

    public function getImagen()
    {
        return $this->imagen;
    }

    public function getDescripcion()
    {
        return $this->descripcion;
    }

    public function getRuta()
    {
        return $this->ruta;
    }

    // Funcion para realizar la busqueda filtrada  
    public function searchRows($value)
    {
        // Creamos la sentencia SQL que contiene la consulta que mandaremos a la base
        $sql = 'SELECT idProducto,c.categoria,estado,m.marca,producto,precio,p.descripcion,p.imagen 
        FROM productos p
        INNER JOIN categorias c ON c.idCategoria = p.Categoria
        INNER JOIN marcas m ON m.idMarca = p.marca
        WHERE producto ILIKE ? OR c.categoria ILIKE ?
        ORDER BY estado DESC';
        $params = array("%$value%", "%$value%");
        return Database::getRows($sql, $params);
    }

    // Funcion para ingresar un producto en la base de datos 
    public function createRow()
    {
        // Creamos la sentencia SQL que contiene la consulta que mandaremos a la base
        $sql = 'INSERT INTO productos(idproducto, categoria, estado, marca, producto, precio, descripcion, imagen,cantidad)
            VALUES (default, ?, true, ?, ?, ?, ?, ?,10);';
        // Envio de parametros
        $params = array($this->categoria, $this->marca, $this->producto, $this->precio, $this->descripcion,  $this->imagen);
        return Database::executeRow($sql, $params);
    }

    // Funcion para cargar todos los datos de la tabla productos
    public function readAll()
    {
        // Creamos la sentencia SQL que contiene la consulta que mandaremos a la base
        $sql = 'SELECT idProducto as id,c.categoria,estado,m.marca,producto,precio,p.descripcion,p.imagen 
        FROM productos p
        INNER JOIN categorias c ON c.idCategoria = p.Categoria
        INNER JOIN marcas m ON m.idMarca = p.marca
        ORDER BY estado DESC';
        // Envio de parametros
        $params = null;
        return Database::getRows($sql, $params);
    }

    public function readCategoria()
    {
        // Creamos la sentencia SQL que contiene la consulta que mandaremos a la base
        $sql = 'SELECT idcategoria,categoria FROM categorias ORDER BY categoria';
        // Envio de parametros
        $params = null;
        return Database::getRows($sql, $params);
    }

    public function readMarca()
    {
        // Creamos la sentencia SQL que contiene la consulta que mandaremos a la base
        $sql = 'SELECT idmarca,marca FROM marcas ORDER BY marca';
        // Envio de parametros
        $params = null;
        return Database::getRows($sql, $params);
    }
    
    // Funcion para cargar los datos de un producto en especifico
    public function readOne()
    {
        // Sentencia SQL
        $sql = 'SELECT idProducto as id,c.categoria as categoria,estado,m.marca as marca,producto,p.cantidad,precio,p.descripcion, p.imagen as imagen
        FROM productos p
        INNER JOIN categorias c ON c.idCategoria = p.Categoria
        INNER JOIN marcas m ON m.idMarca = p.marca
        WHERE idProducto = ?';
        // Envio de parametros
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

    // Funcion para actualizar un registro 
    public function updateRow($current_image)
    {
        // Verificamos si existe una imagen en la base de datos
        if ($this->imagen) {
            $this->deleteFile($this->getRuta(), $current_image);
        } else {
            $this->imagen = $current_image;
        }
        // Sentencia SQL
        $sql = 'UPDATE productos
            SET  producto = ? , precio = ?, descripcion = ?, imagen = ?, marca = ?, categoria = ? 
            WHERE idProducto = ?';
        // Envio de parametros
        $params = array($this->producto, $this->precio, $this->descripcion, $this->imagen,$this->marca,$this->categoria, $this->id);
        return Database::executeRow($sql, $params);
    }

    // Funcion para eliminar un registro de la base
    public function deleteRow()
    {
        // Creamos la sentencia SQL que contiene la consulta que mandaremos a la base
        $sql = 'UPDATE productos SET estado = ? WHERE idProducto = ?';
        // Envio de parametros
        $params = array($this->accion , $this->id);
        return Database::executeRow($sql, $params);
    }
 
    /* FUNCIONES PARA REPORTES */

    // Funcion para reporte de los clientes con mas productos adquiridos dentro del sistema
    public function comprasClientes()
    {
        // Creamos la sentencia SQL que contiene la consulta que mandaremos a la base
        $sql = "SELECT c.correo_electronico, c.nombres || ' ' || c.apellidos as nombre,c.dui,SUM(de.cantidad) cantidad
        from facturas f
        INNER JOIN clientes c ON c.idCliente = f.cliente
		INNER JOIN detallepedidos de ON de.pedido = f.idFactura
        where f.estado = 2
        group by c.correo_electronico,c.nombres,c.apellidos,c.dui
        order by cantidad DESC";
        // Envio de parametros
        $params = null;
        return Database::getRows($sql, $params);
    }

    // Funcion para grafica de venta de productos por categoria
    public function categoriasVentas()
    {
        // Creamos la sentencia SQL que contiene la consulta que mandaremos a la base
        $sql = 'SELECT c.categoria, SUM(d.cantidad) cantidad
		FROM facturas f
		INNER JOIN detallepedidos d ON d.pedido = f.idfactura 
		INNER JOIN productos p ON p.idProducto = d.producto
		INNER JOIN categorias c ON c.idcategoria = p.categoria
		WHERE f.estado = 2
		GROUP BY c.categoria order by cantidad DESC';
        // Envio de parametros
        $params = null;
        return Database::getRows($sql, $params);
    }

    
}
