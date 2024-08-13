<?php
// Se incluye la clase con las plantillas del documento.
require_once('../../app/helpers/public_page.php');
// Se imprime la plantilla del encabezado enviando el título de la página web.
Public_Page::headerTemplate('Gamebridge | Registro','Iniciar sesión');
?>

<style>
  .espacio-register{
    padding-top: 30px;
  }

  body{
    background-color: #d9d4d4;
  }
</style>

  <div class="container py-1 fondo-register">
    <div class="row g-0 align-items-center espacio-register">
      <div class="col-lg-7 mb-7 mb-lg-0">
        <div class="card cascading-right" style="background: hsla(0, 0%, 100%, 0.55); backdrop-filter: blur(30px);">
          <div class="card-body p-4 shadow-5 ">
            <h2 class="fw-bold mb-5 text-center">Registro de clientes</h2>

            <form method="post" id="register-form">

            <div class="row">
                <div class="col-md-6 mb-4">
                  <div class="form-outline">
                    <label class="form-label" for="firstName">Nombre</label>
                    <input type="text" id="nombre" name="nombre" class="form-control form-control-lg" />
                  </div>
                </div>
                <div class="col-md-6 mb-4">
                  <div class="form-outline">
                    <label class="form-label" for="lastName">Apellido</label>
                    <input type="text" id="apellido" name="apellido" class="form-control form-control-lg" />
                  </div>
                </div>
                <div class="col-md-6 mb-4">
                  <div class="form-outline">
                    <label class="form-label" for="lastName">Correo electronico</label>
                    <input type="email" id="correo" name="correo" class="form-control form-control-lg" />
                  </div>
                </div>
                <div class="col-md-6 mb-4">
                  <div class="form-outline">
                    <label class="form-label" for="lastName">DUI</label>
                    <input type="text" id="dui" name="dui" class="form-control form-control-lg" />
                  </div>
                </div>
                <div class="col-md-6 mb-4">
                  <div class="form-outline">
                    <label class="form-label" for="lastName">Clave</label>
                    <input type="password" id="clave" name="clave" class="form-control form-control-lg" />
                  </div>
                </div>
                <div class="col-md-6 mb-4">
                  <div class="form-outline">
                    <label class="form-label" for="lastName">Confirmar clave</label>
                    <input type="password" id="clave2" name="clave2" class="form-control form-control-lg" />
                  </div>
                </div>
              </div>    

            </form><br>
            <center><button onclick="registrarCliente()" class="btn btn-dark btn-lg btn-block botonRegister" type="button">Registrarse</button></center>
          </div>
        </div>
      </div>
      <div class="col-lg-5 mb-5 mb-lg-0">
        <img src="https://mdbootstrap.com/img/new/ecommerce/vertical/004.jpg" class="w-95 rounded-4 shadow-4" alt=""/>
      </div>
    </div>
  </div>


<?php
// Se imprime la plantilla del pie enviando el nombre del controlador para la página web.
Public_Page::footerTemplate('register.js');
?>