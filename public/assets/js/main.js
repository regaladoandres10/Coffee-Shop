//Envio del ajax
const API_URL = 'http://localhost:3000/public/api.php';

async function sendAuthRequest(action, formData, method = 'POST') {
    const url = `${API_URL}?resource=auth&action=${action}`;
    const data = {};
    
    //Convertir FormData a un objeto plano
    formData.forEach((value, key) => data[key] = value);

    //Ver que datos se envian
    console.log('Enviando datos:', data);
    console.log('URL:', url);

    try {
        const response = await fetch(url, {
            method: method, 
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });

        console.log('Respuesta HTTP:', response.status, response.statusText);
        
        //Leer la respuesta
        let responseData;
        try {
            responseData = await response.json();
            console.log('Respuesta JSON:', responseData);
        } catch (jsonError) {
            console.error('Error parseando JSON:', jsonError);
            const text = await response.text();
            console.log('Respuesta texto:', text);
            return { 
                success: false, 
                message: `Error de servidor (${response.status}): ${text || response.statusText}` 
            };
        }

        //Si la respuesta no es exitosa
        if (!response.ok) {
            return { 
                success: false, 
                message: responseData.message || `Error: ${response.status} ${response.statusText}` 
            };
        }

        return responseData;
        

    } catch (error) {
        console.error('Error de red o de proceso:', error);
        return { success: false, message: 'Fallo la conexión con el servidor.' };
    }
}

async function sendUserRequest(resource, formData) {
    const data = {};
    formData.forEach((value, key) => data[key] = value);

    const url = `${API_URL}?resource=${resource}`;
    
    console.log('Enviando datos:', data);
    console.log('URL:', url);

    try {
        const response = await fetch(url, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(data)
        });

        const json = await response.json();
        return json;

    } catch (error) {
        console.error(error);
        return { success: false, message: "Error de conexión con el servidor." };
    }
}


//Logica del login.php
const loginForm = document.getElementById('loginForm');
const loginMessage = document.getElementById('loginMessage');

if (loginForm) {
    loginForm.addEventListener('submit', async function(event) {
        event.preventDefault(); //Evita el envío tradicional del formulario

        const formData = new FormData(loginForm);
        const result = await sendAuthRequest('login', formData);

        loginMessage.textContent = result.message;

        if (result.success) {
            loginMessage.className = 'mt-3 text-center text-success';
            //Redirigir según el rol
            const role = result.data.RoleName
            
            if (role === 'Administrador') {
                window.location.href = '/public/views/admin/index.php'; //Redirigir al panel
            } else {
                window.location.href = 'index.php'; //Redirigir a la página principal del cliente
            }

        } else {
            loginMessage.className = 'mt-3 text-center text-danger';
        }
    });
}


//Lógica de Registro (signup.php)
const signupForm = document.getElementById('signupForm');
const signupMessage = document.getElementById('signupMessage');

if (signupForm) {
    signupForm.addEventListener('submit', async function(event) {
        //Evita el envío tradicional del formulario
        event.preventDefault(); 

        const formData = new FormData(signupForm);
        const result = await sendAuthRequest('register', formData);

        signupMessage.textContent = result.message;

        if (result.success) {
            signupMessage.className = 'mt-3 text-center text-success';
            //Redirigir al login después del registro
            window.location.href = 'logIn.php'; 
        } else {
            signupMessage.className = 'mt-3 text-center text-danger';
        }
    });
}

//Logica de crear usuarios
const createUserForm = document.getElementById('createUserForm');
const formMessage = document.getElementById('formMessage');

if (createUserForm) {
    createUserForm.addEventListener('submit', async function (event) {
        event.preventDefault();

        const formData = new FormData(createUserForm);

        //Confirmar contraseña
        if (formData.get("password") !== formData.get("confirmPassword")) {
            formMessage.className = "alert alert-danger";
            formMessage.textContent = "Las contraseñas no coinciden.";
            formMessage.classList.remove("d-none");
            return;
        }

        //Enviar datos a la API usando misma lógica que sendAuthRequest
        const result = await sendUserRequest('users', formData);

        //Mostrar mensaje
        formMessage.classList.remove("d-none");

        if (result.success) {
            formMessage.className = "alert alert-success";
            formMessage.textContent = result.message || "Usuario creado correctamente.";

            //Cerrar modal después de 1 segundo
            setTimeout(() => {
                const modal = bootstrap.Modal.getInstance(
                    document.getElementById('createUserModal')
                );
                if (modal) modal.hide();

                // Opcional: recargar tabla
                // loadUsersList();
            }, 800);

        } else {
            formMessage.className = "alert alert-danger";
            formMessage.textContent = result.message || "Error al crear usuario.";
        }
    });
}



/**
 * Maneja el envío de formularios para crear o actualizar un recurso.
 * @param {string} resource - El recurso de la API (ej: 'products').
 * @param {string} formId - ID del formulario a serializar.
 */
async function handleSubmit(resource, formId) {
    const form = document.getElementById(formId);
    const formData = new FormData(form);
    const id = formData.get('id'); 

    let url = `${API_BASE_URL}?resource=${resource}`;
    let method = 'POST';

    // Si existe ID, es una actualización (PUT)
    if (id) {
        url += `&id=${id}`;
        method = 'PUT';
    }

    //Convertir FormData a JSON
    const data = Object.fromEntries(formData.entries());

    try {
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();
        const msgElement = document.getElementById('formMessage'); //Elemento para mensajes

        if (result.success) {
            msgElement.textContent = result.message;
            msgElement.className = 'alert alert-success';
            form.reset(); //Limpiar el formulario
            //Recargar la tabla
            loadData(resource, `${resource}TableBody`); 
            //Cerrar modal si aplica
        } else {
            msgElement.textContent = result.message || 'Error desconocido.';
            msgElement.className = 'alert alert-danger';
        }
    } catch (error) {
        console.error('Error en la solicitud:', error);
    }
}

/**
 * Maneja el login y registro.
 * @param {string} action - 'login' o 'register'.
 * @param {string} formId - ID del formulario.
 */
async function handleAuth(action, formId) {
    const form = document.getElementById(formId);
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());

    try {
        const response = await fetch(`${API_BASE_URL}?resource=auth&action=${action}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();
        const msgElement = document.getElementById('authMessage');

        if (result.success) {
            msgElement.textContent = result.message;
            msgElement.className = 'alert alert-success';
            
            // Redirigir al usuario
            if (action === 'login') {
                //Aquí deberías redirigir al dashboard o a la página principal
                window.location.href = '/public/views/admin/index.php';
            } else {
                // Después de registrarse, pedirle que inicie sesión
                window.location.href = '/public/views/auth/login.php';
            }
        } else {
            msgElement.textContent = result.message || 'Credenciales inválidas.';
            msgElement.className = 'alert alert-danger';
        }
    } catch (error) {
        console.error(`Error en ${action}:`, error);
    }
}

