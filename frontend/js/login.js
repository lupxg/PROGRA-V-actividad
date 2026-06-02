const API_URL = "/api";

document
    .getElementById("loginForm")
    .addEventListener("submit", async function (e) {

        e.preventDefault();

        const credentials = {

            correo: document.getElementById("correo").value,

            password: document.getElementById("password").value
        };

        const resultDiv =
            document.getElementById("loginResult");

        try {

            const response = await fetch(`${API_URL}/login`, {

                method: "POST",

                headers: {
                    "Content-Type": "application/json"
                },

                body: JSON.stringify(credentials)
            });

            const data = await response.json();

            if (response.ok) {

                // Save session
                localStorage.setItem(
                    "usuario",
                    JSON.stringify(data.user)
                );

                resultDiv.innerHTML = `
                    <div class="alert alert-success">
                        Login exitoso. Redirigiendo...
                    </div>
                `;

                setTimeout(() => {
                    window.location.href = "index.html";
                }, 1000);

            } else {

                resultDiv.innerHTML = `
                    <div class="alert alert-danger">
                        ${data.message}
                    </div>
                `;
            }

        } catch (error) {

            console.error(error);

            resultDiv.innerHTML = `
                <div class="alert alert-danger">
                    Error de conexión con el servidor
                </div>
            `;
        }
    });