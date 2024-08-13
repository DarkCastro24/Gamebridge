// Constante para establecer la ruta y parámetros de comunicación con la API.
const API_USUARIOS = '../../app/api/dashboard/usuarios.php?action=';
const API_TIPOUSUARIO = '../../app/api/dashboard/usuarios.php?action=readUserType';

// Método manejador de eventos que se ejecuta cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', function () {
    readRows(API_USUARIOS);
});

// Función para llenar la tabla con los datos de los registros. Se manda a llamar en la función readRows().
function fillTable(dataset) {
    let content = '';
    let icon = '';
    let accion = '';
    let tipo ='';
    // Se recorre el conjunto de registros (dataset) fila por fila a través del objeto row.
    dataset.map(function (row) {
        switch(row.tipo)
        {
            case 1:
                tipo = 'Root';
                break;
            case 2:
                tipo = 'Administrador';
                break;
            case 3:
                tipo = 'Vendedor';
                break;
        }
        if(row.estado == true)
        {
            icon = 'fa fa-lock'
            accion = false;
        } else {
            icon  = 'fa fa-unlock-alt';
            accion = true;
        }
        // Se crean y concatenan las filas de la tabla con los datos de cada registro.
        content += `
            <tr> 
                <td>${row.idusuario}</td>
                <td>${row.usuario}</td>
                <td>${row.dui}</td>
                <td>${row.telefono}</td>
                <td>${tipo}</td>
                <td>${row.correo_electronico}</td>                             
                <td>
                    <a href="#" onclick="openDeleteDialog(${row.idusuario},${accion})" class="btn waves-effect waves-orange btn deleteButton tooltipped" data-tooltip="Eliminar"><i class="${icon} fa-lg"></i></a>
                    <a href="#" onclick="openUpdateModal(${row.idusuario})" class="btn waves-effect btn updateButton tooltipped" data-tooltip="Actualizar"><i class="fa fa-refresh fa-lg" aria-hidden="true"></i></a>
                </td>
            </tr>
        `;
    });
    // Se agregan las filas al cuerpo de la tabla mediante su id para mostrar los registros.
    document.getElementById('tbody-rows').innerHTML = content;
}

// Funcion para busqueda filtrada 
function searchUser() {
    searchRows(API_USUARIOS, 'search-form');
}

// Función para preparar el formulario al momento de insertar un registro.
function openCreateModal() {
    // Se restauran los elementos del formulario.
    document.getElementById('save-form').reset();
    document.getElementById('txtId').disabled = false;
    document.getElementById('txtClave').disabled = false;
    document.getElementById('txtClave2').disabled = false;
    document.getElementById('txtDui').disabled = false;
    document.getElementById('cmbTipo').disabled = false;
    // Ocultamos el input que contiene el ID del registro
    document.getElementById('auxId').style.display = 'none';
    // Llenamos el select con los tipos de usuario de la base 
    fillSelect(API_TIPOUSUARIO, 'cmbTipo', null);
    // Abrimos el modal con JQuery
    $('#modalDatos').modal('show');
}

// Funcion para guardar o modificar datos (se llama en el boton guardar del modal)
function saveData() {
    if (document.getElementById("txtClave").value != '') {
        if (document.getElementById("txtClave").value == document.getElementById("txtClave2").value) {
            let action = '';
            if (document.getElementById('auxId').value) {
                action = 'update';
            } else {
                action = 'create';
            }
            saveRow(API_USUARIOS, action, 'save-form', 'modalDatos');
        } else {
            sweetAlert(3, 'Las claves ingresadas no coinciden', null,'Confirme su contraseña');
        }
    } else {
        sweetAlert(3, 'Complete todos los campos solicitados', null,'No dejes campos vacios');
    }
}

// Función para preparar el formulario al momento de modificar un registro.
function openUpdateModal(id) {
    // Se restauran los elementos del formulario.
    document.getElementById('save-form').reset();
    // Mandamos a llamar el modal desde JS
    var myModal = new bootstrap.Modal(document.getElementById('modalDatos'));
    myModal.show();
    document.getElementById('modal-title').textContent = 'ACTUALIZAR USUARIO';
    // Ocultamos el input que contiene el ID del registro
    document.getElementById('auxId').style.display = 'none';
    // Se deshabilitan los campos de alias y contraseña.
    document.getElementById('txtId').disabled = true;
    document.getElementById('txtClave').disabled = true;
    document.getElementById('txtClave2').disabled = true;
    document.getElementById('txtDui').disabled = true;
    document.getElementById('cmbTipo').disabled = true;
    document.getElementById('auxId').value = id;
    // Se define un objeto con los datos del registro seleccionado.
    const data = new FormData();
    data.append('txtId', id);
    // Realizamos una peticion a la API indicando el caso a utilizar y enviando la direccion de la API como parametro
    fetch(API_USUARIOS + 'readOne', {
        method: 'post',
        body: data
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje indicando el problema.
        if (request.ok) {
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                if (response.status) {
                    // Se inicializan los campos del formulario con los datos del registro seleccionado.
                    document.getElementById('txtId').value = response.dataset.idusuario;
                    document.getElementById('txtUsuario').value = response.dataset.usuario;
                    document.getElementById('txtCorreo').value = response.dataset.correo_electronico;
                    document.getElementById('txtClave').value = response.dataset.clave;
                    document.getElementById('txtTelefono').value = response.dataset.telefono;
                    document.getElementById('txtDui').value = response.dataset.dui;
                    document.getElementById('txtClave2').value = response.dataset.clave;
                    fillSelect(API_TIPOUSUARIO, 'cmbTipo', response.dataset.tipo);
                } else {
                    sweetAlert(2, response.exception, null);
                }
            });
        } else {
            console.log(request.status + ' ' + request.statusText);
        }
    }).catch(function (error) {
        console.log(error);
    });
}

// Función para establecer el registro a eliminar y abrir una caja de dialogo de confirmación.
function openDeleteDialog(id,accion) {
    // Se define un objeto con los datos del registro seleccionado.
    const data = new FormData();
    data.append('txtId', id);
    data.append('txtAccion', accion);
    // Se llama a la función que elimina un registro. Se encuentra en el archivo components.js
    confirmDelete(API_USUARIOS, data);
}

