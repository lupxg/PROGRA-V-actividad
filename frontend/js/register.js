const API_URL = "/api";

const form = document.getElementById("registerForm");

form.addEventListener("submit", async function (e) {

    e.preventDefault();

    const resultado = document.getElementById("resultado");

    resultado.innerHTML = "";

    const usuario = {

        nombre: document.getElementById("nombre").value,

        correo: document.getElementById("correo").value,

        password: document.getElementById("password").value,

        rol: "lector"
    };

    try {

        const response = await fetch(`${API_URL}/register`, {

            method: "POST",

            headers: {
                "Content-Type": "application/json"
            },

            body: JSON.stringify(usuario)
        });

        const data = await response.json();

        if (response.ok) {

            resultado.innerHTML = `
                <div class="alert alert-success">
                    ${data.message}
                </div>
            `;

            form.reset();

            setTimeout(() => {

                window.location.href = "login.html";

            }, 1500);

        } else {

            resultado.innerHTML = `
                <div class="alert alert-danger">
                    ${data.message}
                </div>
            `;
        }

    } catch (error) {

        console.error(error);

        resultado.innerHTML = `
            <div class="alert alert-danger">
                Error al conectar con el servidor.
            </div>
        `;
    }
});