<?php
// Se incluye la clase con las plantillas del documento.
require_once('../../app/helpers/dashboard_page.php');
// Se imprime la plantilla del encabezado enviando el título de la página web.
Dashboard_Page::headerLogin('Inicio de sesión');
?>

<section class="h-100 gradient-form" style="background-color: #eee;">
  <div class="container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-xl-12">
        <div class="card rounded-3 text-black">
          <div class="row g-0">
            <div class="col-lg-6">
              <div class="card-body p-md-5 mx-md-4">

                <div class="text-center">
                  <h3 class="mt-1 mb-5 pb-1">Inicio de sesión</h3>
                </div>

                <form method="post" id="session-form">

                  <div class="form-outline mb-4">
                    <label class="form-label" for="form2Example11">Usuario</label>
                    <input type="email" id="txtAlias" name="txtAlias" class="form-control" placeholder="Ingrese su nombre de usuario" />

                  </div>

                  <div class="form-outline mb-4">
                    <label class="form-label" for="form2Example22">Contraseña</label>
                    <input type="password" id="txtClave" name="txtClave" class="form-control" placeholder="Ingrese su contraseña" />

                  </div>

                </form><br>

                <div class="text-center">
                  <center><button onclick="iniciarSesion()" class="btn btn-dark btn-lg btn-block botonLogin" type="button">Iniciar sesión</button></center><br>
                </div>

                <div class="align-items-center justify-content-center">
                  <center><a class="text-muted" href="#!">Olvidaste tu contraseña?</a></center>
                </div>

              </div>
            </div>
            <div class="col-lg-6 d-flex align-items-center gradient-custom-2">
              <div class="text-white px-3 py-4 p-md-5 mx-md-4">
                <h4 class="mb-4">We are more than just a company</h4>
                <p class="small mb-0">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                  tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud
                  exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>


<?php
// Se imprime la plantilla del pie enviando el nombre del controlador para la página web.
Dashboard_Page::footerLogin('index.js');
?>