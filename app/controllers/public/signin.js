// Constante para establecer la ruta y parámetros de comunicación con la API.
const API_CLIENTES = '../../app/api/public/clientes.php?action=';

function iniciarSesion() {
    // Verificar si el reCAPTCHA ha sido completado
    var recaptchaResponse = grecaptcha.getResponse();
    if (recaptchaResponse.length === 0) {
        // Si el reCAPTCHA no se ha completado, mostrar un mensaje de error
        alert('Por favor, completa el reCAPTCHA.');
        return false; // Evitar que se realice la petición al servidor
    }

    // Si el reCAPTCHA ha sido completado, proceder con la solicitud
    fetch(API_CLIENTES + 'logIn', {
        method: 'post',
        body: new FormData(document.getElementById('session-form'))
    }).then(function (request) {
        if (request.ok) {
            request.json().then(function (response) {
                if (response.status) {
                    sweetAlert(1, response.message, 'index.php');
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
