// Constantes para establecer las rutas y parámetros de comunicación con la API.
const API_PRODUCTOS = '../../app/api/dashboard/productos.php?action=';
const ENDPOINT_CATEGORIAS = '../../app/api/dashboard/productos.php?action=readCategoria';
const ENDPOINT_MARCA = '../../app/api/dashboard/productos.php?action=readMarca';

// Método manejador de eventos que se ejecuta cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', function () {
    // Se llama a la función que obtiene los registros para llenar la tabla. Se encuentra en el archivo components.js
    readRows(API_PRODUCTOS);
});

// Función para llenar la tabla con los datos de los registros. Se manda a llamar en la función readRows().
function fillTable(dataset) {
    let content = '';
    let icon = '';
    let accion = '';
    // Se recorre el conjunto de registros (dataset) fila por fila a través del objeto row.
    dataset.map(function (row) {
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
                <td><img src="../../resources/img/productos/${row.imagen}" class="materialboxed" height="100"></td>
                <td>${row.categoria}</td>
                <td>${row.marca}</td>
                <td>${row.producto}</td>
                <td>${row.precio}</td>
                <td>
                    <a href="#" onclick="openDeleteDialog(${row.id},${accion})" class="btn waves-effect waves-orange btn deleteButton tooltipped" data-tooltip="Eliminar"><i class="${icon} fa-lg"></i></a>
                    <a href="#" onclick="openUpdateModal(${row.id})" class="btn waves-effect btn updateButton tooltipped" data-tooltip="Actualizar"><i class="fa fa-refresh fa-lg" aria-hidden="true"></i></a>
                </td>
            </tr>
        `;
    });
    // Se agregan las filas al cuerpo de la tabla mediante su id para mostrar los registros.
    document.getElementById('tbody-rows').innerHTML = content;
}

// Funcion para busqueda filtrada 
function searchProduct() {
    searchRows(API_PRODUCTOS, 'search-form');
}

// Función para preparar el formulario al momento de insertar un registro.
function openCreateModal() {
    // Se restauran los elementos del formulario.
    document.getElementById('save-form').reset();
    document.getElementById('archivo_producto').required = true;
    fillSelect(ENDPOINT_CATEGORIAS, 'cmbCategoria', null);
    fillSelect(ENDPOINT_MARCA, 'cmbMarca', null);
    // Ocultamos el input que contiene el ID del registro
    document.getElementById('auxId').style.display = 'none';
    // Abrimos el modal con JQuery
    $('#modalDatos').modal('show');
}

// Funcion para guardar o modificar datos (se llama en el boton guardar del modal)
function saveData() {
    let action = '';
    if (document.getElementById('auxId').value) {
        action = 'update';
    } else {
        action = 'create';
    }
    saveRow(API_PRODUCTOS, action, 'save-form', 'modalDatos');   
}

// Función para establecer el registro a eliminar y abrir una caja de dialogo de confirmación.
function openDeleteDialog(id,accion) {
    const data = new FormData();
    data.append('txtId', id);
    data.append('txtAccion', accion);
    // Ejecutamos la funcion para eliminar un producto
    confirmDelete(API_PRODUCTOS, data);
}