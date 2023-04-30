<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YosyPics API</title>
    <style>
        .bg-dark {
            background-color: #343a40;
        }

        .data {
            color: black;
            font-size: 20px;
            margin-top: 20px;
            border: 1px solid black;
            display: flex;
            width: fit-content;
            padding: 10px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            transition: 0.2s;
            transition-property: background-color, color;
        }

        .data:hover {
            cursor: pointer;
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
        }

        .titulo {
            color: black;
            font-size: 30px;
            font-weight: bold;
        }

        .parrafo {
            color: black;
            font-size: 20px;
            font-weight: bolder;
        }

        .formulario {
            display: flex;
            flex-direction: column;
            width: fit-content;
        }

        .formulario input,
        .input {
            padding: 10px;
            border-radius: 5px;
            width: 300px;
            margin-bottom: 10px;
        }

        .formulario input:hover,
        .input:hover {
            cursor: pointer;
        }

        .formulario button,
        .button {
            padding: 10px;
            border-radius: 5px;
            width: auto;
            margin-bottom: 10px;
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            font-weight: bold;
            transition: 0.2s;
            transition-property: background-color, color;
        }

        .formulario button:hover,
        .button:hover {
            cursor: pointer;
            background-color: white;
            color: black;
        }

        .card {
            background-color: #dee2e6;
        }

        .hidden {
            display: none;
        }

        main {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 20px;
            margin: 20px;
            justify-content: center;
        }

        @media screen and (max-width: 600px) {
            main {
                grid-template-columns: repeat(1, 1fr);
            }
        }

        main>div {
            display: flex;
            flex-direction: column;
            gap: 10px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            height: fit-content;
            width: fit-content;
            justify-content: center;
            align-items: center;
            padding: 15px;
        }

        .input-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
    </style>
</head>

<body class="bg-dark">
    <main>
        <div class="card">
            <h1 class="titulo">Datos del usuario</h1>
            <p class="parrafo">
                Hacer petición a la API
            </p>

            <form class="formulario" id="formulario">
                <div class="input-group">
                    <label for="token">Token</label>
                    <input type="text" id="token" placeholder="Ingrese el token">
                    <button type="submit">Solicitar datos del usuario</button>
                </div>
            </form>
            <div>
                <p class="hidden" id="data"></p>
            </div>
        </div>
        <div class="card">
            <h2 class="titulo">Login</h2>
            <p class="parrafo">
                Iniciar sesión con la API REST
            </p>
            <form class="formulario" id="formLogin">
                <div class="input-group">
                    <label for="correo">Correo electrónico</label>
                    <input class="input" type="email" id="correo" placeholder="Ingrese el correo">
                </div>
                <div class="input-group">
                    <label for="password">Contrase&ntilde;a</label>
                    <input class="input" type="password" id="password" placeholder="Ingrese la contrase&ntilde;a">
                </div>

                <button class="button" type="submit">Iniciar sesión</button>
            </form>

            <p class="hidden" id="responseLogin">
            </p>
        </div>
        <div class="card">
            <h2 class="titulo">Registro</h2>
            <p class="parrafo">
                Registrarse con la API REST
            </p>
            <form class="formulario" id="formRegistro" novalidate>
                <div class="input-group">
                    <label for="nombre">Nombre</label>
                    <input class="input" type="text" id="nombre" placeholder="Ingrese el nombre">
                </div>
                <div class="input-group">
                    <label for="apellido">Apellido</label>
                    <input class="input" type="text" id="apellido" placeholder="Ingrese el apellido">
                </div>
                <div class="input-group">
                    <label for="correoR">Correo electrónico</label>
                    <input class="input" type="email" id="correoR" placeholder="Ingrese el correo">
                </div>
                <div class="input-group">
                    <label for="passwordR">Contrase&ntilde;a</label>
                    <input class="input" type="password" id="passwordR" placeholder="Ingrese la contrase&ntilde;a">
                </div>
                <button class="button" type="submit">Registrarse</button>
            </form>

            <p class="hidden" id="responseRegistro">
            </p>
        </div>
        <div class="card">
            <h2 class="titulo">Olvide mi contraseña</h2>
            <p class="parrafo">
                Olvide mi contrase&ntilde;a
            </p>
            <form class="formulario" id="formOlvide" novalidate>
                <div class="input-group">
                    <label for="correoR">Correo electrónico</label>
                    <input class="input" type="email" id="correoOlv" placeholder="Ingrese el correo">
                </div>
                <button class="button" type="submit">Enviar correo</button>
            </form>

            <p class="hidden" id="responseOlvide">
            </p>
        </div>
        <div class="card">
            <h2 class="titulo">Restablecer contraseña</h2>
            <p class="parrafo">
                Restablecer contraseña
            </p>
            <form class="formulario" id="formRestablecer" novalidate>
                <input type="hidden" id="tokenRes" value="<?php echo isset($_GET['token']) ? $_GET['token'] : null ?>">
                <div class="input-group">
                    <label for="correoR">Correo electrónico</label>
                    <input class="input" type="email" id="correoRes" placeholder="Ingrese el correo">
                </div>
                <div class="input-group">
                    <label for="passwordR">Contrase&ntilde;a</label>
                    <input class="input" type="password" id="passwordRes" placeholder="Ingrese la contrase&ntilde;a">
                </div>
                <button class="button" type="submit">Restablecer contrase&ntilde;a</button>
            </form>

            <p class="hidden" id="responseRestablecer">
            </p>
        </div>
    </main>


    <script>
        // Formularios
        const form = document.getElementById('formulario');
        const formLogin = document.getElementById('formLogin');
        const formRegistro = document.getElementById('formRegistro');
        const formRestablecer = document.getElementById('formRestablecer');
        const formOlvide = document.getElementById('formOlvide');

        // Contenedores de respuesta
        const dataContainer = document.getElementById('data');
        const responseLogin = document.getElementById('responseLogin');
        const responseRegistro = document.getElementById('responseRegistro');
        const responseRestablecer = document.getElementById('responseRestablecer');
        const responseOlvide = document.getElementById('responseOlvide');

        // Campos de entrada
        const token = document.getElementById('token');
        const correo = document.getElementById('correo');
        const password = document.getElementById('password');
        const nombre = document.getElementById('nombre');
        const apellido = document.getElementById('apellido');
        const correoR = document.getElementById('correoR');
        const passwordR = document.getElementById('passwordR');
        const correoOlv = document.getElementById('correoOlv');
        const passwordRes = document.getElementById('passwordRes');
        const correoRes = document.getElementById('correoRes');
        const tokenRes = document.getElementById('tokenRes');

        // En este se hace la solicitud de cambio de contraseña, se envía un correo electrónico
        formOlvide.addEventListener('submit', (e) => {
            e.preventDefault();

            responseOlvide.classList.remove('hidden');
            responseOlvide.classList.add('data');

            let data = {
                'correo': correoOlv.value,
            }

            async function fetchAPI(url, options) {
                const response = await fetch(url, options);
                return await response.json();
            }

            fetchAPI('http://localhost/YosyPics_Api/api/auth/forgot', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                })
                .then(data => {
                    console.log(data);

                    if (data.status) {
                        responseOlvide.innerHTML = `<p>${data.message}</p>`;
                    } else {
                        let mensajes = data.message;

                        if (typeof mensajes === 'string') {
                            responseOlvide.innerHTML = `<p>${mensajes}</p>`;
                        } else {
                            Object.entries(mensajes).forEach(([key, value]) => {
                                responseOlvide.innerHTML += `🔴 ${value} <br>`
                            })
                        }
                    }
                })
                .catch(error => {
                    console.log(error);
                })
        })

        // En este evento se realiza el restablecimiento de contraseña
        formRestablecer.addEventListener('submit', (e) => {
            e.preventDefault();

            responseRestablecer.classList.remove('hidden');
            responseRestablecer.classList.add('data');

            let data = {
                'correo': correoRes.value,
                'password': passwordRes.value,
                'token': tokenRes.value,
            }

            async function fetchAPI(url, options) {
                const response = await fetch(url, options);

                return await response.json();
            }

            fetchAPI('http://localhost/YosyPics_Api/api/auth/reset', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                })
                .then(data => {
                    console.log(data);
                    if (data.status) {
                        responseRestablecer.innerHTML = `<p>${data.message}</p>`;
                    } else {
                        let mensajes = data.message;

                        if (typeof mensajes === 'string') {
                            responseRestablecer.innerHTML = `<p>${mensajes}</p>`;
                        } else {
                            Object.entries(mensajes).forEach(([key, value]) => {
                                responseRestablecer.innerHTML += `🔴 ${value} <br>`
                            })
                        }
                    }

                })
                .catch(error => {
                    console.log(error);
                })
        })

        // En este evento se realiza el registro de un nuevo usuario
        formRegistro.addEventListener('submit', (e) => {
            e.preventDefault();

            responseRegistro.classList.remove('hidden');
            responseRegistro.classList.add('data');

            let header = {
                'Content-Type': 'application/json',
            }

            let data = {
                'nombre': nombre.value,
                'apellido': apellido.value,
                'correo': correoR.value,
                'password': passwordR.value,
            }

            async function fetchAPI(url, options) {
                const response = await fetch(url, options);
                return await response.json();
            }

            fetchAPI('http://localhost/YosyPics_Api/api/auth/register', {
                    method: 'POST',
                    headers: header,
                    body: JSON.stringify(data)
                })
                .then(data => {
                    if (data.status) {
                        let mensaje = data.message;

                        responseRegistro.innerHTML = `<p>${mensaje}</p>`;
                    } else {
                        let mensajes = data.message;

                        if (typeof mensajes === 'string') {
                            responseRegistro.innerHTML = `<p>${mensajes}</p>`;
                        } else {
                            Object.entries(mensajes).forEach(([key, value]) => {
                                responseRegistro.innerHTML += `🔴 ${value} <br>`
                            })
                        }
                    }
                })
                .catch(error => {
                    console.log(error);
                })
        })

        // En este evento se realiza el login de un usuario
        formLogin.addEventListener('submit', (e) => {
            e.preventDefault();

            responseLogin.classList.remove('hidden');
            responseLogin.classList.add('data');

            let header = {
                'Content-Type': 'application/json',
            }

            let data = {
                'correo': correo.value,
                'password': password.value,
            }

            async function fetchAPI(url, options) {
                const response = await fetch(url, options);
                return await response.json();
            }

            fetchAPI('http://localhost/YosyPics_Api/api/auth/login', {
                    method: 'POST',
                    headers: header,
                    body: JSON.stringify(data)
                })
                .then(data => {
                    if (data.status) {
                        let mensaje = data.message;
                        let token = data.token;

                        responseLogin.innerHTML = `Mensaje: ${mensaje} <br> Token: ${token}`;
                    } else {
                        let mensajes = data.message;

                        if (typeof mensajes === 'string') {
                            responseLogin.innerHTML = `Error: ${mensajes}`;
                        } else {
                            Object.entries(mensajes).forEach(([key, value]) => {
                                responseLogin.innerHTML += `🔴 ${value} <br>`
                            })
                        }
                    }
                })
                .catch(error => {
                    responseLogin.innerHTML = `Mensaje: ${error.message}`;
                })
        })

        // En este evento se realiza la petición de datos del usuario con el token
        form.addEventListener('submit', (e) => {
            e.preventDefault();

            dataContainer.classList.remove('hidden');
            dataContainer.classList.add('data');

            let header = {
                'Content-Type': 'application/json',
                'www-token-auth': token.value
            };

            async function fetchAPI(url, options) {
                const response = await fetch(url, options);
                return await response.json();
            }

            fetchAPI('http://localhost/YosyPics_Api/api/auth/user', {
                    method: 'GET',
                    headers: header
                })
                .then(data => {
                    if (data.status) {
                        let nombre = data.user.nombre;
                        let apellido = data.user.apellido;
                        let correo = data.user.correo;
                        let verificado = data.user.verificado;
                        let estado = data.user.estado;

                        dataContainer.innerHTML = `Nombre: ${nombre} <br> Apellido: ${apellido} <br> Correo: ${correo} <br> Verificado: ${verificado == 1 ? 'Si' : 'No'} <br> Estado: ${estado == 1 ? 'Activo' : 'Inactivo'}`;
                    } else {
                        dataContainer.innerHTML = data.message;
                    }
                })
                .catch(err => console.log(err));
        })

        // Borrar datos del usuario en el contenedor
        dataContainer.addEventListener('click', () => {
            let option = confirm('¿Desea borrar los datos?');

            if (option) {
                dataContainer.innerHTML = '';
                token.value = '';
                dataContainer.classList.add('hidden');
                dataContainer.classList.remove('data');
            }
        })
    </script>
</body>

</html>