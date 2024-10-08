PGDMP     4    0                {         
   GameBridge    15.2    15.2 Z    �           0    0    ENCODING    ENCODING        SET client_encoding = 'UTF8';
                      false            �           0    0 
   STDSTRINGS 
   STDSTRINGS     (   SET standard_conforming_strings = 'on';
                      false            �           0    0 
   SEARCHPATH 
   SEARCHPATH     8   SELECT pg_catalog.set_config('search_path', '', false);
                      false            �           1262    16398 
   GameBridge    DATABASE     �   CREATE DATABASE "GameBridge" WITH TEMPLATE = template0 ENCODING = 'UTF8' LOCALE_PROVIDER = libc LOCALE = 'Spanish_El Salvador.1252';
    DROP DATABASE "GameBridge";
                postgres    false            �            1255    16399    diasclave(date)    FUNCTION     �   CREATE FUNCTION public.diasclave(fecha date) RETURNS integer
    LANGUAGE plpgsql
    AS $$
DECLARE
    dias int;
BEGIN
    SELECT(fecha - (SELECT CURRENT_DATE)) INTO dias;
    RETURN dias;
END
$$;
 ,   DROP FUNCTION public.diasclave(fecha date);
       public          postgres    false            �            1255    16400    diasclave2(date)    FUNCTION     �   CREATE FUNCTION public.diasclave2(fecha date) RETURNS integer
    LANGUAGE plpgsql
    AS $$
DECLARE
    dias int;
BEGIN
    SELECT(fecha - (SELECT CURRENT_DATE)) INTO dias;
    RETURN dias;
END
$$;
 -   DROP FUNCTION public.diasclave2(fecha date);
       public          postgres    false            �            1255    16401 =   funcionbusquedaproducto(character varying, character varying)    FUNCTION     &  CREATE FUNCTION public.funcionbusquedaproducto(character varying, character varying, OUT out_idproducto integer, OUT out_categoria character varying, OUT out_estado character varying, OUT out_marca character varying, OUT out_producto character varying, OUT out_precio numeric, OUT out_descripcion character varying, OUT out_cantidad integer, OUT out_imagen bytea) RETURNS SETOF record
    LANGUAGE plpgsql
    AS $_$
DECLARE
    reg RECORD;
BEGIN   
	FOR REG IN SELECT IdProducto,Categorias.Categoria, EstadoProductos.Estado, Marcas.Marca, Productos.Producto,
	Productos.Precio,Descripcion,Cantidad,Imagen
	from Productos 
	INNER JOIN Categorias on Productos.Categoria=Categorias.idCategoria
	INNER JOIN EstadoProductos on Productos.Estado=EstadoProductos.idEstado 
	INNER JOIN Marcas on Marcas.idMarca = Productos.marca 
	WHERE Productos.Producto LIKE '%'  || $1 || '%' AND EstadoProductos.Estado = 'Disponible' 
	AND Categorias.Categoria = $2
	LOOP
		out_IdProducto := reg.IdProducto;
        out_categoria := reg.categoria;
        out_estado := reg.estado;
        out_marca  := reg.marca;
        out_producto := reg.producto;
        out_precio   := reg.precio;
		out_descripcion := reg.descripcion;
		out_cantidad := reg.cantidad;
		out_imagen := reg.imagen;
       RETURN NEXT;
    END LOOP;
	RETURN;
END $_$;
 k  DROP FUNCTION public.funcionbusquedaproducto(character varying, character varying, OUT out_idproducto integer, OUT out_categoria character varying, OUT out_estado character varying, OUT out_marca character varying, OUT out_producto character varying, OUT out_precio numeric, OUT out_descripcion character varying, OUT out_cantidad integer, OUT out_imagen bytea);
       public          postgres    false            �            1255    16402 (   funcioncargarcatalogo(character varying)    FUNCTION        CREATE FUNCTION public.funcioncargarcatalogo(character varying, OUT out_idproducto integer, OUT out_categoria character varying, OUT out_estado character varying, OUT out_marca character varying, OUT out_producto character varying, OUT out_precio numeric, OUT out_descripcion character varying, OUT out_cantidad integer, OUT out_imagen bytea) RETURNS SETOF record
    LANGUAGE plpgsql
    AS $_$
DECLARE
    reg RECORD;
BEGIN   
	FOR REG IN SELECT IdProducto,Categorias.Categoria, EstadoProductos.Estado, Marcas.Marca, Productos.Producto,
	Productos.Precio,Descripcion,Cantidad,Imagen
	from Productos 
	INNER JOIN Categorias on Productos.Categoria=Categorias.idCategoria
	INNER JOIN EstadoProductos on Productos.Estado=EstadoProductos.idEstado 
	INNER JOIN Marcas on Marcas.idMarca = Productos.marca 
	WHERE Categorias.Categoria = $1 AND EstadoProductos.Estado = 'Disponible'
	ORDER BY Productos.Precio ASC
	LOOP
		out_IdProducto := reg.IdProducto;
        out_categoria := reg.categoria;
        out_estado := reg.estado;
        out_marca  := reg.marca;
        out_producto := reg.producto;
        out_precio   := reg.precio;
		out_descripcion := reg.descripcion;
		out_cantidad := reg.cantidad;
		out_imagen := reg.imagen;
       RETURN NEXT;
    END LOOP;
	RETURN;
END $_$;
 V  DROP FUNCTION public.funcioncargarcatalogo(character varying, OUT out_idproducto integer, OUT out_categoria character varying, OUT out_estado character varying, OUT out_marca character varying, OUT out_producto character varying, OUT out_precio numeric, OUT out_descripcion character varying, OUT out_cantidad integer, OUT out_imagen bytea);
       public          postgres    false            �            1255    16403    generarcodigo()    FUNCTION     �   CREATE FUNCTION public.generarcodigo() RETURNS integer
    LANGUAGE plpgsql
    AS $$
declare retorno int := (SELECT trunc(random() * 999999 + 100000) FROM generate_series(1,1)); 
	begin		
		return retorno;
	end
$$;
 &   DROP FUNCTION public.generarcodigo();
       public          postgres    false            �            1255    16404    otras(date)    FUNCTION     �   CREATE FUNCTION public.otras(fecha_vcto date) RETURNS integer
    LANGUAGE plpgsql
    AS $$
DECLARE
    diasfecha3 int;
BEGIN

    Select(select current_date-fecha_vcto) into diasfecha3;
    return diasfecha3;
END
$$;
 -   DROP FUNCTION public.otras(fecha_vcto date);
       public          postgres    false            �            1255    16405 /   procedimientodetalle(integer, integer, integer)    FUNCTION     }  CREATE FUNCTION public.procedimientodetalle(varcliente integer, varproducto integer, varcantidad integer) RETURNS SETOF record
    LANGUAGE plpgsql
    AS $$  
declare varprecioUnitario real := (SELECT (precio) FROM Productos WHERE idProducto = varProducto);
declare varSubtotal real := (varprecioUnitario * varCantidad);
declare varidPedido int := (SELECT (idPedido) FROM Pedidos where cliente = varCliente); 
declare totalPedido numeric; 
	begin		
		IF varidPedido is null THEN
		 	insert into Pedidos values (default,varCliente,1,3,1,default);
			varidPedido = (SELECT MAX(idPedido) from Pedidos);
		END IF;
		insert into detallePedidos values (default,varidPedido,varProducto,varprecioUnitario,varCantidad,varSubtotal);
		totalPedido := (select sum(subtotal) from detallePedidos where pedido = varidPedido); 
		update Pedidos set total = totalPedido where idpedido = varidPedido;
	end
$$;
 i   DROP FUNCTION public.procedimientodetalle(varcliente integer, varproducto integer, varcantidad integer);
       public          postgres    false            �            1259    16406 	   foo_a_seq    SEQUENCE     s   CREATE SEQUENCE public.foo_a_seq
    START WITH 14
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
     DROP SEQUENCE public.foo_a_seq;
       public          postgres    false            �            1259    16407 
   categorias    TABLE     D  CREATE TABLE public.categorias (
    idcategoria integer DEFAULT nextval('public.foo_a_seq'::regclass) NOT NULL,
    categoria character varying(30),
    descripcion character varying(150) DEFAULT 'Descripcion'::character varying NOT NULL,
    imagen character varying(100) DEFAULT '63a36048737e7.png'::character varying
);
    DROP TABLE public.categorias;
       public         heap    postgres    false    214            �            1259    16413    clientes    TABLE     �  CREATE TABLE public.clientes (
    idcliente integer NOT NULL,
    nombres character varying(40) NOT NULL,
    apellidos character varying(40) NOT NULL,
    dui character(10) NOT NULL,
    correo_electronico character varying(50) NOT NULL,
    clave character varying(200) NOT NULL,
    fecharegistro timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    estado boolean DEFAULT true NOT NULL
);
    DROP TABLE public.clientes;
       public         heap    postgres    false            �            1259    16418    clientes_idcliente_seq    SEQUENCE     �   CREATE SEQUENCE public.clientes_idcliente_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 -   DROP SEQUENCE public.clientes_idcliente_seq;
       public          postgres    false    216            �           0    0    clientes_idcliente_seq    SEQUENCE OWNED BY     Q   ALTER SEQUENCE public.clientes_idcliente_seq OWNED BY public.clientes.idcliente;
          public          postgres    false    217            �            1259    16419    detallepedidos    TABLE     �   CREATE TABLE public.detallepedidos (
    iddetallefactura integer NOT NULL,
    pedido integer NOT NULL,
    producto integer NOT NULL,
    preciounitario numeric(7,2) DEFAULT 0.0 NOT NULL,
    cantidad integer DEFAULT 0 NOT NULL
);
 "   DROP TABLE public.detallepedidos;
       public         heap    postgres    false            �            1259    16424 #   detallepedidos_iddetallefactura_seq    SEQUENCE     �   CREATE SEQUENCE public.detallepedidos_iddetallefactura_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 :   DROP SEQUENCE public.detallepedidos_iddetallefactura_seq;
       public          postgres    false    218            �           0    0 #   detallepedidos_iddetallefactura_seq    SEQUENCE OWNED BY     k   ALTER SEQUENCE public.detallepedidos_iddetallefactura_seq OWNED BY public.detallepedidos.iddetallefactura;
          public          postgres    false    219            �            1259    16425    direcciones    TABLE     �   CREATE TABLE public.direcciones (
    iddireccion integer NOT NULL,
    cliente integer NOT NULL,
    direccion character varying(150) NOT NULL,
    codigo_postal character(4) NOT NULL,
    telefono_fijo character(9)
);
    DROP TABLE public.direcciones;
       public         heap    postgres    false            �            1259    16428    direcciones_iddireccion_seq    SEQUENCE     �   CREATE SEQUENCE public.direcciones_iddireccion_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 2   DROP SEQUENCE public.direcciones_iddireccion_seq;
       public          postgres    false    220            �           0    0    direcciones_iddireccion_seq    SEQUENCE OWNED BY     [   ALTER SEQUENCE public.direcciones_iddireccion_seq OWNED BY public.direcciones.iddireccion;
          public          postgres    false    221            �            1259    16429    estadofactura    TABLE     w   CREATE TABLE public.estadofactura (
    idestado integer NOT NULL,
    estadofactura character varying(25) NOT NULL
);
 !   DROP TABLE public.estadofactura;
       public         heap    postgres    false            �            1259    16432    facturas    TABLE     �   CREATE TABLE public.facturas (
    idfactura integer NOT NULL,
    cliente integer NOT NULL,
    estado integer NOT NULL,
    fecha date DEFAULT CURRENT_DATE NOT NULL
);
    DROP TABLE public.facturas;
       public         heap    postgres    false            �            1259    16436    marcas    TABLE     ^   CREATE TABLE public.marcas (
    idmarca integer NOT NULL,
    marca character varying(40)
);
    DROP TABLE public.marcas;
       public         heap    postgres    false            �            1259    16439    pedidos_idpedido_seq    SEQUENCE     �   CREATE SEQUENCE public.pedidos_idpedido_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 +   DROP SEQUENCE public.pedidos_idpedido_seq;
       public          postgres    false    223            �           0    0    pedidos_idpedido_seq    SEQUENCE OWNED BY     O   ALTER SEQUENCE public.pedidos_idpedido_seq OWNED BY public.facturas.idfactura;
          public          postgres    false    225            �            1259    16440 	   productos    TABLE     �  CREATE TABLE public.productos (
    idproducto integer NOT NULL,
    categoria integer NOT NULL,
    marca integer NOT NULL,
    producto character varying(75) NOT NULL,
    precio numeric(7,2) DEFAULT 0.1 NOT NULL,
    descripcion character varying(200) NOT NULL,
    imagen character varying(100) DEFAULT 'disco-duro-seagete-1tb.png'::character varying,
    cantidad integer DEFAULT 0,
    estado boolean DEFAULT true NOT NULL
);
    DROP TABLE public.productos;
       public         heap    postgres    false            �            1259    16447    productos_idproducto_seq    SEQUENCE     �   CREATE SEQUENCE public.productos_idproducto_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 /   DROP SEQUENCE public.productos_idproducto_seq;
       public          postgres    false    226            �           0    0    productos_idproducto_seq    SEQUENCE OWNED BY     U   ALTER SEQUENCE public.productos_idproducto_seq OWNED BY public.productos.idproducto;
          public          postgres    false    227            �            1259    16448    tipousuarios    TABLE     r   CREATE TABLE public.tipousuarios (
    idtipo integer NOT NULL,
    tipousuario character varying(25) NOT NULL
);
     DROP TABLE public.tipousuarios;
       public         heap    postgres    false            �            1259    16451    usuarios    TABLE     �  CREATE TABLE public.usuarios (
    idusuario integer NOT NULL,
    tipo integer NOT NULL,
    usuario character varying(35) NOT NULL,
    clave character varying(60) DEFAULT 'primeruso'::character varying NOT NULL,
    correo_electronico character varying(60) NOT NULL,
    fecharegistro timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    estado boolean DEFAULT true,
    telefono character(9) DEFAULT (0 - 0) NOT NULL,
    dui character(10) NOT NULL
);
    DROP TABLE public.usuarios;
       public         heap    postgres    false            �            1259    16458    usuarios_idusuario_seq    SEQUENCE     �   CREATE SEQUENCE public.usuarios_idusuario_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 -   DROP SEQUENCE public.usuarios_idusuario_seq;
       public          postgres    false    229            �           0    0    usuarios_idusuario_seq    SEQUENCE OWNED BY     Q   ALTER SEQUENCE public.usuarios_idusuario_seq OWNED BY public.usuarios.idusuario;
          public          postgres    false    230            �            1259    16459    valoraciones    TABLE     (  CREATE TABLE public.valoraciones (
    id_valoracion integer NOT NULL,
    id_detalle integer NOT NULL,
    calificacion_producto integer,
    comentario_producto character varying(250),
    fecha_comentario timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    estado_comentario boolean
);
     DROP TABLE public.valoraciones;
       public         heap    postgres    false            �            1259    16463    valoraciones_id_valoracion_seq    SEQUENCE     �   CREATE SEQUENCE public.valoraciones_id_valoracion_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 5   DROP SEQUENCE public.valoraciones_id_valoracion_seq;
       public          postgres    false    231            �           0    0    valoraciones_id_valoracion_seq    SEQUENCE OWNED BY     a   ALTER SEQUENCE public.valoraciones_id_valoracion_seq OWNED BY public.valoraciones.id_valoracion;
          public          postgres    false    232            �           2604    16464    clientes idcliente    DEFAULT     x   ALTER TABLE ONLY public.clientes ALTER COLUMN idcliente SET DEFAULT nextval('public.clientes_idcliente_seq'::regclass);
 A   ALTER TABLE public.clientes ALTER COLUMN idcliente DROP DEFAULT;
       public          postgres    false    217    216            �           2604    16465    detallepedidos iddetallefactura    DEFAULT     �   ALTER TABLE ONLY public.detallepedidos ALTER COLUMN iddetallefactura SET DEFAULT nextval('public.detallepedidos_iddetallefactura_seq'::regclass);
 N   ALTER TABLE public.detallepedidos ALTER COLUMN iddetallefactura DROP DEFAULT;
       public          postgres    false    219    218            �           2604    16466    direcciones iddireccion    DEFAULT     �   ALTER TABLE ONLY public.direcciones ALTER COLUMN iddireccion SET DEFAULT nextval('public.direcciones_iddireccion_seq'::regclass);
 F   ALTER TABLE public.direcciones ALTER COLUMN iddireccion DROP DEFAULT;
       public          postgres    false    221    220            �           2604    16467    facturas idfactura    DEFAULT     v   ALTER TABLE ONLY public.facturas ALTER COLUMN idfactura SET DEFAULT nextval('public.pedidos_idpedido_seq'::regclass);
 A   ALTER TABLE public.facturas ALTER COLUMN idfactura DROP DEFAULT;
       public          postgres    false    225    223            �           2604    16468    productos idproducto    DEFAULT     |   ALTER TABLE ONLY public.productos ALTER COLUMN idproducto SET DEFAULT nextval('public.productos_idproducto_seq'::regclass);
 C   ALTER TABLE public.productos ALTER COLUMN idproducto DROP DEFAULT;
       public          postgres    false    227    226            �           2604    16469    usuarios idusuario    DEFAULT     x   ALTER TABLE ONLY public.usuarios ALTER COLUMN idusuario SET DEFAULT nextval('public.usuarios_idusuario_seq'::regclass);
 A   ALTER TABLE public.usuarios ALTER COLUMN idusuario DROP DEFAULT;
       public          postgres    false    230    229            �           2604    16470    valoraciones id_valoracion    DEFAULT     �   ALTER TABLE ONLY public.valoraciones ALTER COLUMN id_valoracion SET DEFAULT nextval('public.valoraciones_id_valoracion_seq'::regclass);
 I   ALTER TABLE public.valoraciones ALTER COLUMN id_valoracion DROP DEFAULT;
       public          postgres    false    232    231            p          0    16407 
   categorias 
   TABLE DATA           Q   COPY public.categorias (idcategoria, categoria, descripcion, imagen) FROM stdin;
    public          postgres    false    215   c�       q          0    16413    clientes 
   TABLE DATA           x   COPY public.clientes (idcliente, nombres, apellidos, dui, correo_electronico, clave, fecharegistro, estado) FROM stdin;
    public          postgres    false    216   $�       s          0    16419    detallepedidos 
   TABLE DATA           f   COPY public.detallepedidos (iddetallefactura, pedido, producto, preciounitario, cantidad) FROM stdin;
    public          postgres    false    218   "�       u          0    16425    direcciones 
   TABLE DATA           d   COPY public.direcciones (iddireccion, cliente, direccion, codigo_postal, telefono_fijo) FROM stdin;
    public          postgres    false    220   c�       w          0    16429    estadofactura 
   TABLE DATA           @   COPY public.estadofactura (idestado, estadofactura) FROM stdin;
    public          postgres    false    222   ��       x          0    16432    facturas 
   TABLE DATA           E   COPY public.facturas (idfactura, cliente, estado, fecha) FROM stdin;
    public          postgres    false    223   ��       y          0    16436    marcas 
   TABLE DATA           0   COPY public.marcas (idmarca, marca) FROM stdin;
    public          postgres    false    224   :�       {          0    16440 	   productos 
   TABLE DATA           z   COPY public.productos (idproducto, categoria, marca, producto, precio, descripcion, imagen, cantidad, estado) FROM stdin;
    public          postgres    false    226   ��       }          0    16448    tipousuarios 
   TABLE DATA           ;   COPY public.tipousuarios (idtipo, tipousuario) FROM stdin;
    public          postgres    false    228   ͅ       ~          0    16451    usuarios 
   TABLE DATA           }   COPY public.usuarios (idusuario, tipo, usuario, clave, correo_electronico, fecharegistro, estado, telefono, dui) FROM stdin;
    public          postgres    false    229   	�       �          0    16459    valoraciones 
   TABLE DATA           �   COPY public.valoraciones (id_valoracion, id_detalle, calificacion_producto, comentario_producto, fecha_comentario, estado_comentario) FROM stdin;
    public          postgres    false    231   ��       �           0    0    clientes_idcliente_seq    SEQUENCE SET     E   SELECT pg_catalog.setval('public.clientes_idcliente_seq', 53, true);
          public          postgres    false    217            �           0    0 #   detallepedidos_iddetallefactura_seq    SEQUENCE SET     R   SELECT pg_catalog.setval('public.detallepedidos_iddetallefactura_seq', 57, true);
          public          postgres    false    219            �           0    0    direcciones_iddireccion_seq    SEQUENCE SET     J   SELECT pg_catalog.setval('public.direcciones_iddireccion_seq', 19, true);
          public          postgres    false    221            �           0    0 	   foo_a_seq    SEQUENCE SET     8   SELECT pg_catalog.setval('public.foo_a_seq', 21, true);
          public          postgres    false    214            �           0    0    pedidos_idpedido_seq    SEQUENCE SET     C   SELECT pg_catalog.setval('public.pedidos_idpedido_seq', 48, true);
          public          postgres    false    225            �           0    0    productos_idproducto_seq    SEQUENCE SET     G   SELECT pg_catalog.setval('public.productos_idproducto_seq', 71, true);
          public          postgres    false    227            �           0    0    usuarios_idusuario_seq    SEQUENCE SET     E   SELECT pg_catalog.setval('public.usuarios_idusuario_seq', 17, true);
          public          postgres    false    230            �           0    0    valoraciones_id_valoracion_seq    SEQUENCE SET     M   SELECT pg_catalog.setval('public.valoraciones_id_valoracion_seq', 19, true);
          public          postgres    false    232            �           2606    16472    categorias categorias_pkey 
   CONSTRAINT     a   ALTER TABLE ONLY public.categorias
    ADD CONSTRAINT categorias_pkey PRIMARY KEY (idcategoria);
 D   ALTER TABLE ONLY public.categorias DROP CONSTRAINT categorias_pkey;
       public            postgres    false    215            �           2606    16474 (   clientes clientes_correo_electronico_key 
   CONSTRAINT     q   ALTER TABLE ONLY public.clientes
    ADD CONSTRAINT clientes_correo_electronico_key UNIQUE (correo_electronico);
 R   ALTER TABLE ONLY public.clientes DROP CONSTRAINT clientes_correo_electronico_key;
       public            postgres    false    216            �           2606    16476    clientes clientes_dui_key 
   CONSTRAINT     S   ALTER TABLE ONLY public.clientes
    ADD CONSTRAINT clientes_dui_key UNIQUE (dui);
 C   ALTER TABLE ONLY public.clientes DROP CONSTRAINT clientes_dui_key;
       public            postgres    false    216            �           2606    16478    clientes clientes_pkey 
   CONSTRAINT     [   ALTER TABLE ONLY public.clientes
    ADD CONSTRAINT clientes_pkey PRIMARY KEY (idcliente);
 @   ALTER TABLE ONLY public.clientes DROP CONSTRAINT clientes_pkey;
       public            postgres    false    216            �           2606    16480 "   detallepedidos detallepedidos_pkey 
   CONSTRAINT     n   ALTER TABLE ONLY public.detallepedidos
    ADD CONSTRAINT detallepedidos_pkey PRIMARY KEY (iddetallefactura);
 L   ALTER TABLE ONLY public.detallepedidos DROP CONSTRAINT detallepedidos_pkey;
       public            postgres    false    218            �           2606    16482    direcciones direcciones_pkey 
   CONSTRAINT     c   ALTER TABLE ONLY public.direcciones
    ADD CONSTRAINT direcciones_pkey PRIMARY KEY (iddireccion);
 F   ALTER TABLE ONLY public.direcciones DROP CONSTRAINT direcciones_pkey;
       public            postgres    false    220            �           2606    16484    estadofactura estadopedido_pkey 
   CONSTRAINT     c   ALTER TABLE ONLY public.estadofactura
    ADD CONSTRAINT estadopedido_pkey PRIMARY KEY (idestado);
 I   ALTER TABLE ONLY public.estadofactura DROP CONSTRAINT estadopedido_pkey;
       public            postgres    false    222            �           2606    16486    marcas marcas_pkey 
   CONSTRAINT     U   ALTER TABLE ONLY public.marcas
    ADD CONSTRAINT marcas_pkey PRIMARY KEY (idmarca);
 <   ALTER TABLE ONLY public.marcas DROP CONSTRAINT marcas_pkey;
       public            postgres    false    224            �           2606    16488    facturas pedidos_pkey 
   CONSTRAINT     Z   ALTER TABLE ONLY public.facturas
    ADD CONSTRAINT pedidos_pkey PRIMARY KEY (idfactura);
 ?   ALTER TABLE ONLY public.facturas DROP CONSTRAINT pedidos_pkey;
       public            postgres    false    223            �           2606    16490    productos productos_pkey 
   CONSTRAINT     ^   ALTER TABLE ONLY public.productos
    ADD CONSTRAINT productos_pkey PRIMARY KEY (idproducto);
 B   ALTER TABLE ONLY public.productos DROP CONSTRAINT productos_pkey;
       public            postgres    false    226            �           2606    16492     productos productos_producto_key 
   CONSTRAINT     _   ALTER TABLE ONLY public.productos
    ADD CONSTRAINT productos_producto_key UNIQUE (producto);
 J   ALTER TABLE ONLY public.productos DROP CONSTRAINT productos_producto_key;
       public            postgres    false    226            �           2606    16494    tipousuarios tipousuarios_pkey 
   CONSTRAINT     `   ALTER TABLE ONLY public.tipousuarios
    ADD CONSTRAINT tipousuarios_pkey PRIMARY KEY (idtipo);
 H   ALTER TABLE ONLY public.tipousuarios DROP CONSTRAINT tipousuarios_pkey;
       public            postgres    false    228            �           2606    16496 )   tipousuarios tipousuarios_tipousuario_key 
   CONSTRAINT     k   ALTER TABLE ONLY public.tipousuarios
    ADD CONSTRAINT tipousuarios_tipousuario_key UNIQUE (tipousuario);
 S   ALTER TABLE ONLY public.tipousuarios DROP CONSTRAINT tipousuarios_tipousuario_key;
       public            postgres    false    228            �           2606    16498    valoraciones unico 
   CONSTRAINT     S   ALTER TABLE ONLY public.valoraciones
    ADD CONSTRAINT unico UNIQUE (id_detalle);
 <   ALTER TABLE ONLY public.valoraciones DROP CONSTRAINT unico;
       public            postgres    false    231            �           2606    16500 (   usuarios usuarios_correo_electronico_key 
   CONSTRAINT     q   ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_correo_electronico_key UNIQUE (correo_electronico);
 R   ALTER TABLE ONLY public.usuarios DROP CONSTRAINT usuarios_correo_electronico_key;
       public            postgres    false    229            �           2606    16502    usuarios usuarios_dui_key 
   CONSTRAINT     S   ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_dui_key UNIQUE (dui);
 C   ALTER TABLE ONLY public.usuarios DROP CONSTRAINT usuarios_dui_key;
       public            postgres    false    229            �           2606    16504    usuarios usuarios_pkey 
   CONSTRAINT     [   ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_pkey PRIMARY KEY (idusuario);
 @   ALTER TABLE ONLY public.usuarios DROP CONSTRAINT usuarios_pkey;
       public            postgres    false    229            �           2606    16506    usuarios usuarios_usuario_key 
   CONSTRAINT     [   ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_usuario_key UNIQUE (usuario);
 G   ALTER TABLE ONLY public.usuarios DROP CONSTRAINT usuarios_usuario_key;
       public            postgres    false    229            �           2606    16508    valoraciones valoraciones_pkey 
   CONSTRAINT     g   ALTER TABLE ONLY public.valoraciones
    ADD CONSTRAINT valoraciones_pkey PRIMARY KEY (id_valoracion);
 H   ALTER TABLE ONLY public.valoraciones DROP CONSTRAINT valoraciones_pkey;
       public            postgres    false    231            �           2606    16509 )   detallepedidos detallepedidos_pedido_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.detallepedidos
    ADD CONSTRAINT detallepedidos_pedido_fkey FOREIGN KEY (pedido) REFERENCES public.facturas(idfactura);
 S   ALTER TABLE ONLY public.detallepedidos DROP CONSTRAINT detallepedidos_pedido_fkey;
       public          postgres    false    3266    218    223            �           2606    16514 +   detallepedidos detallepedidos_producto_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.detallepedidos
    ADD CONSTRAINT detallepedidos_producto_fkey FOREIGN KEY (producto) REFERENCES public.productos(idproducto);
 U   ALTER TABLE ONLY public.detallepedidos DROP CONSTRAINT detallepedidos_producto_fkey;
       public          postgres    false    3270    218    226            �           2606    16519 $   direcciones direcciones_cliente_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.direcciones
    ADD CONSTRAINT direcciones_cliente_fkey FOREIGN KEY (cliente) REFERENCES public.clientes(idcliente);
 N   ALTER TABLE ONLY public.direcciones DROP CONSTRAINT direcciones_cliente_fkey;
       public          postgres    false    216    220    3258            �           2606    16524    facturas pedidos_cliente_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.facturas
    ADD CONSTRAINT pedidos_cliente_fkey FOREIGN KEY (cliente) REFERENCES public.clientes(idcliente);
 G   ALTER TABLE ONLY public.facturas DROP CONSTRAINT pedidos_cliente_fkey;
       public          postgres    false    3258    223    216            �           2606    16529    facturas pedidos_estado_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.facturas
    ADD CONSTRAINT pedidos_estado_fkey FOREIGN KEY (estado) REFERENCES public.estadofactura(idestado);
 F   ALTER TABLE ONLY public.facturas DROP CONSTRAINT pedidos_estado_fkey;
       public          postgres    false    222    3264    223            �           2606    16534    productos productos_marca_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.productos
    ADD CONSTRAINT productos_marca_fkey FOREIGN KEY (marca) REFERENCES public.marcas(idmarca);
 H   ALTER TABLE ONLY public.productos DROP CONSTRAINT productos_marca_fkey;
       public          postgres    false    224    226    3268            �           2606    16539    usuarios usuarios_tipo_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_tipo_fkey FOREIGN KEY (tipo) REFERENCES public.tipousuarios(idtipo);
 E   ALTER TABLE ONLY public.usuarios DROP CONSTRAINT usuarios_tipo_fkey;
       public          postgres    false    228    3274    229            �           2606    16544 )   valoraciones valoraciones_id_detalle_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.valoraciones
    ADD CONSTRAINT valoraciones_id_detalle_fkey FOREIGN KEY (id_detalle) REFERENCES public.detallepedidos(iddetallefactura);
 S   ALTER TABLE ONLY public.valoraciones DROP CONSTRAINT valoraciones_id_detalle_fkey;
       public          postgres    false    231    218    3260            p   �   x�mϱ�0����)x"�����G�k{jJ��}~I��_��#�4�l���l�.H&������`7p�94�^��Pt�4[�z\?B����PYTk��o��:d�vt�����s�B��l{�Ol8U����m�yGKO�#�]�K~ޢp����t���c0HlCD*`�Vvn��U!�L�g      q   �   x���Mj�0��t�,�����Ҫ%	)����dӍ�8N��A5��z�^�&io������5�V���ز����.��Jh�
�}�]���m85��Z�ј!d��o�鼡���M���4����0<����������æ�9��# ���'啑d4*���j��[h�c��' Y��D���g��5�ސG-�i��Q�i.X���4��,L7�@4�Q�+�T�0s����_�}      s   1   x�35�4"cNC=Nc.S3Ns$C.S ׌�܀�&���� �      u   6   x�3��45�t�,JMN���SH�IM,�/�L�4440�4����55�������� 1��      w   7   x�3�H�K�L�+I�2�t��K�ɬJL��2�t�+)JM��9�Js@�=... �E�      x   :   x�eʱ�0��"x��0���E��U��\��I���4��b������~H�      y   �   x�EN�
�@�w��/�l�QK� �Z�"6k���1�����{ja7/f&�m�EF��y�:<mc9Χ
3�X#�WP̩����r�i_n�)�zc��7pF�ZxBE�cdJePmk������mDqD;;x�*uiGg}�/��}� wE���`$V�r�F쓇:�4�R�h<rx�e>:      {   �  x�m�͎� ���)x��c�m�����,���nf�lC1��}�>E_��'M:����;�s����㏟8S�3�J�d{�ȗ�.`}�i�鼚�BZ�w������	g$�� �(r<�C��
9��x�K����Y_�{e�5R6M�o�|��9��ə�)�W�+�?S���q��b
\Č��;)��>�W�&2�a��FI��@�*�r !$*@���[��P���./ X�D{�#8�;��N>?�h�4#\U�O/��X��,�$0��)�8 A	��#3G�Vm�����^k)���o?��VpY��2�mI��}ZqNX&������;���o�[�%���u���52��^%t�����u��m�k�nƫ�_~�Zܔ�~
H}L�|N����n9dV�>�`<DK?H�N�#��Ʋ*S��qۚ����V�n4�w��u���� L�؃      }   ,   x�3���/�2�tL����,.)JL�/�2�K�KI1c���� ��      ~   �   x�=��
�0 ����n�����SbPT �ŋ�i�!�h��w�{��}���d-�h�"p�f���%��8���[%U��P�p,̩�=����e�x5^�p�/���/+��1"�$6�ybr2%m a�eFk�Hki#��0�����u(�      �      x������ � �     