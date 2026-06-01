const API_URL = "http://localhost/api";



async function cargarPrestamos() {

    try {

        const usuario = JSON.parse(
            localStorage.getItem("usuario")
        );

        if (!usuario) {

            alert("Debe iniciar sesión");

            window.location.href = "login.html";

            return;
        }

        document.getElementById("usuarioActual").innerHTML = `
            <strong>${usuario.nombre}</strong>
            (${usuario.correo})
        `;

        const response = await fetch(`${API_URL}/prestamos`);

        const resultado = await response.json();

        const tabla = document.getElementById("tablaPrestamos");

        tabla.innerHTML = "";

        const prestamosUsuario = resultado.data.filter(prestamo =>
            prestamo.usuario_id == usuario.id
        );

        if (prestamosUsuario.length === 0) {

            tabla.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center">
                        No tiene préstamos registrados
                    </td>
                </tr>
            `;

            return;
        }

        prestamosUsuario.forEach(prestamo => {

            tabla.innerHTML += `
                <tr>

                    <td>${prestamo.id}</td>

                    <td>${prestamo.libro_id}</td>

                    <td>${prestamo.fecha_prestamo}</td>

                    <td>${prestamo.fecha_devolucion}</td>
                    <td>
                        ${
                            prestamo.estado === "prestado"
                            ? '<span class="badge bg-success">Activo</span>'
                            : '<span class="badge bg-secondary">Devuelto</span>'
                        }
                    </td>

                </tr>
            `;
        });

    } catch (error) {

        console.error(error);

        alert("Error al cargar préstamos");
    }
}



window.onload = cargarPrestamos;