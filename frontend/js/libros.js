const API_URL = "http://localhost/api";

const modalElement = document.getElementById("prestamoModal");

const modal = new bootstrap.Modal(modalElement);



async function cargarLibros() {

    try {

        const response = await fetch(`${API_URL}/libros`);

        const resultado = await response.json();

        const tabla = document.getElementById("tablaLibros");

        tabla.innerHTML = "";

        resultado.data.forEach(libro => {

            tabla.innerHTML += `
                <tr>

                    <td>${libro.id}</td>

                    <td>${libro.titulo}</td>

                    <td>${libro.autor}</td>

                    <td>${libro.stock}</td>

                    <td>
                        ${libro.disponible == 1
                    ? '<span class="badge bg-success">Sí</span>'
                    : '<span class="badge bg-danger">No</span>'
                }
                    </td>

                    <td>
                        ${libro.imagen_url
                    ? `<img src="${libro.imagen_url}" width="80" class="img-thumbnail">`
                    : 'Sin imagen'
                }
                    </td>

                    <td>

                        <button
                            class="btn btn-primary btn-sm"
                            onclick="abrirModalPrestamo(${libro.id})"
                        >
                            Prestar
                        </button>

                    </td>

                </tr>
            `;
        });

    } catch (error) {

        console.error(error);
    }
}



function abrirModalPrestamo(libroId) {

    const usuario = JSON.parse(
        localStorage.getItem("usuario")
    );

    if (!usuario) {

        alert("Debe iniciar sesión");

        return;
    }

    document.getElementById("libro_id").value = libroId;

    document.getElementById("usuario_nombre").value =
        usuario.nombre;

    document.getElementById("resultadoPrestamo").innerHTML = "";

    modal.show();
}

function controlarAuthUI() {

    const usuario = JSON.parse(
        localStorage.getItem("usuario")
    );

    const btnLogin = document.getElementById("btnLogin");
    const btnLogout = document.getElementById("btnLogout");
    const btnPrestamos = document.getElementById("btnMisPrestamos");

    if (!usuario) {

        btnLogin.style.display = "inline-block";
        btnLogout.style.display = "none";
        btnPrestamos.style.display = "none";

    } else {

        btnLogin.style.display = "none";
        btnLogout.style.display = "inline-block";
        btnPrestamos.style.display = "inline-block";
    }
}

function logout() {

    localStorage.removeItem("usuario");

    window.location.href = "index.html";
}

document
    .getElementById("formPrestamo")
    .addEventListener("submit", async function (e) {

        e.preventDefault();

        const prestamo = {

            usuario_id: JSON.parse(
                localStorage.getItem("usuario")
            ).id,

            libro_id: parseInt(
                document.getElementById("libro_id").value
            ),

            fecha_devolucion:
                document.getElementById("fecha_devolucion").value,
            
            estado: "prestado",
        };

        try {

            const response = await fetch(`${API_URL}/prestamos`, {

                method: "POST",

                headers: {
                    "Content-Type": "application/json"
                },

                body: JSON.stringify(prestamo)
            });

            const resultado = await response.json();

            const resultadoDiv =
                document.getElementById("resultadoPrestamo");

            if (response.ok) {

                resultadoDiv.innerHTML = `
                    <div class="alert alert-success">
                        Préstamo registrado correctamente. Redirigiendo...
                    </div>
                `;

                setTimeout(() => {
                    window.location.href = "prestamos.html";
                }, 1000);
                //cargarLibros();

            } else {

                resultadoDiv.innerHTML = `
                    <div class="alert alert-danger">
                        ${resultado.message}
                    </div>
                `;
            }

        } catch (error) {

            console.error(error);

            document.getElementById("resultadoPrestamo").innerHTML = `
                <div class="alert alert-danger">
                    Error de conexión con la API
                </div>
            `;
        }
    });



window.onload = function () {
    cargarLibros();
    controlarAuthUI();
};