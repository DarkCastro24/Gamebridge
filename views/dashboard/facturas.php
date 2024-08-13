<?php
// Se incluye la clase con las plantillas del documento.
require_once('../../app/helpers/dashboard_page.php');
// Se imprime la plantilla del encabezado enviando el título de la página web.
Dashboard_Page::headerTemplate('Administrar facturas','Facturas');
?>

<head>
  <!-- Seccion para incluir CSS -->
  <link type="text/css" rel="stylesheet" href="../../resources/css/styles.css" />
</head>

<div class="container"><br><br>
        <div class="row"> 
          <!-- Formulario de búsqueda -->
          <div class="row">
              <div class="col col-lg-10 col-md-8">
                <div class="row">
                  <div class="col col-md-8 col-lg-6">
                    <!-- Formulario de búsqueda -->
                    <form method="post" id="search-form">
                        <div class="input-field">
                            <input type="text" name="search" class="form-control" id="search" placeholder="Buscar por nombre de cliente">
                        </div>
                    </form>
                  </div>
                  <div class="col col-md-4 col-lg-6">
                    <div class="input-field">
                      <button type="button" onclick="search()" class="btn btn-dark" data-tooltip="Buscar"><i class="fa fa-search"></i></button>
                      <a href="../../app/reports/dashboard/productos.php" target="_blank" class="btn btn-dark" data-tooltip="Reporte de usuarios por tipo"><i class="fa fa-file-text" aria-hidden="true"></i></a>
                    </div>
                  </div>
                </div>
              </div>
        </div>
      </div> <br><br>

<div class="container">
  <!-- Seccion tabla -->
  <table class="table" id="miTabla">
    <thead class="table-dark">
      <tr id="tableHeader">
        <th>ID</th>
        <th>Cliente</th>
        <th>Estado de factura</th>
        <th>Fecha</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody id="tbody-rows">
    </tbody>
  </table>
</div>

<div id="save-modal" class="modal">
  <!-- Componente modal para modificar y agregar -->
  <div class="modal-content">
    <h4 id="modal-title" class="center-align"></h4>
    <form method="post" id="save-form">
      <input class="hide" type="number" id="txtId" name="txtId" />
      <div class="row">
        <div class="input-field col l12 s12 m12">
          <select id="cmbTipo" name="cmbTipo">
          </select>
          <label>Usuario vendedor</label>
        </div>
      </div>
      <div class="row">
        <div class="input-field col l12 s12 m12">
          <select id="cmbEstado" name="cmbEstado">
          </select>
          <label>Estado de la factura</label>
        </div>
      </div>
      <div class="row">
        <div class="col s12  colconfig">
          <a class="waves-effect waves btn cancelButton modal-close" href="#!"><i class="material-icons left">clear</i>Cancelar</a>
          <button type="submit" class="waves-effect waves btn addButton "><i class="material-icons left">check</i>Guardar</button>
        </div>
      </div>
    </form>
  </div>
</div>


<!-- Componente Modal para mostrar productos de una factura -->
<div id="address-modal" class="modal">
  <div class="modal-content">
    <h4 id="modal-title" class="center-align">Detalles de la factura </h4><br>

    <form method="post" id="address-form" enctype="multipart/form-data">
      <input class="hide" type="number" id="txtIdx" name="txtIdx" />
    </form>

    <table class="striped centered responsive-table" id="miTabla2">
      <thead>
        <tr id="tableHeader">
          <th>Producto</th>
          <th>Precio unitario</th>
          <th>Cantidad</th>
          <th>Total unitario</th>
        </tr>
      </thead>
      <tbody id="tbody-rows2">
      </tbody>
    </table>
    <div class="row right-align">
      <p>TOTAL A PAGAR (US$) <b id="pago"></b></p>
    </div>
    <br>
    <div class="row">
      <div class="col s12  colconfig">
        <a class="waves-effect waves btn cancelButton modal-close" href="#!"><i class="material-icons left">clear</i>Salir
        </a>
      </div>
    </div>
    </form>
  </div>
</div>
<?php
// Se imprime la plantilla del pie enviando el nombre del controlador para la página web.
Dashboard_Page::footerTemplate('facturas.js');
?>