// Constante para establecer la ruta y parámetros de comunicación con la API.
const API_CATALOGO = '../../app/api/public/catalogo.php?action=';

// Método manejador de eventos que se ejecuta cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', function () {
    // Se busca en la URL las variables (parámetros) disponibles.
    let params = new URLSearchParams(location.search);
    // Se obtienen los datos localizados por medio de las variables.
    const ID = params.get('id');
    const NAME = params.get('categoria');
    // Se llama a la función que muestra los productos de la categoría seleccionada previamente.
    readProductosCategoria(ID, NAME);
    const p = document.getElementById("subtitulo");
    p.innerText = "Categoría: " + NAME;

});

// Función para obtener y mostrar los productos de acuerdo a la categoría seleccionada.
function readProductosCategoria(id) {
    // Se define un objeto con los datos del registro seleccionado.
    const data = new FormData();
    data.append('id_categoria', id);
    fetch(API_CATALOGO + 'readProductosCategoria', {
        method: 'post',
        body: data
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje indicando el problema.
        if (request.ok) {
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                if (response.status) {
                    let content = '';
                    // Se recorre el conjunto de registros devuelto por la API (dataset) fila por fila a través del objeto row.
                    response.dataset.map(function (row) {
                        // Se crean y concatenan las tarjetas con los datos de cada producto.
                        content += `
                        <div class="col">
                            <div class="card h-100 shadow-sm">
                                <img src="../../resources/img/productos/${row.imagen}" class="card-img-top" alt="..."/>
                                <div class="label-top shadow-sm">${row.producto}</div>
                                    <div class="card-body">
                                        <div class="clearfix mb-3">
                                        <span class="float-start badge rounded-pill bg-secondary">&dollar;${row.precio}</span>
                                        <span class="float-end"><a href="detalle.php?id=${row.idproducto}" class="small text-muted">Ver detalles</a></span>
                                        </div>
                                        <h5 class="card-title"> ${row.descripcion} </h5>
                                        <div class="text-center my-4">
                                            <a href="detalle.php?id=${row.idproducto}" class="btn btn-outline-dark"> 
                                                <div class="row">
                                                    <div class="col-9">
                                                        <p class="buttonProducts">Añadir al carrito</p>
                                                    </div>
                                                    <div class="col-3">
                                                        <span class="material-symbols-outlined buttonProductsIcon"> add_shopping_cart</span>
                                                    </div>
                                                </div>                    
                                            </a>
                                        </div>
                                        <div class="clearfix mb-1">
                                            <span class="float-start"><i class="far fa-question-circle"></i></span>
                                            <span class="float-end"><i class="fas fa-plus"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    });
                    // Se agregan las tarjetas a la etiqueta div mediante su id para mostrar los productos.
                    document.getElementById('productos').innerHTML = content;
                } else {
                    // Se presenta un mensaje de error cuando no existen datos para mostrar.
                    document.getElementById('title').innerHTML = `<i class="material-icons small">cloud_off</i><span class="red-text">${response.exception}</span>`;
                }
            });
        } else {
            console.log(request.status + ' ' + request.statusText);
        }
    }).catch(function (error) {
        console.log(error);
    });
}