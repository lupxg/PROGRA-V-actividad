
using System;
using System.Net.Http;
using System.Net.Http.Json;
using System.Threading.Tasks;
using App.Models;

namespace App.Services
{
    public class PrestamoService
    {
        private readonly HttpClient _httpClient;

        public PrestamoService()
        {
            _httpClient = new HttpClient
            {
                BaseAddress = new Uri("http://localhost:8000/api/")
            };
        }

        public async Task<PrestamoApiResponse?> ObtenerPrestamosAsync(int pagina = 1)
        {
            try
            {
                return await _httpClient.GetFromJsonAsync<PrestamoApiResponse>($"prestamos?page={pagina}");
            }
            catch { return null; }
        }

        /// <summary>
        /// POST /api/prestamos
        /// </summary>
        public async Task<bool> CrearPrestamoAsync(PrestamoAdmin nuevoPrestamo)
        {
            try
            {
                // Enviamos el objeto. C# usará de forma automática el JsonPropertyName (usuario_id, libro_id, etc.)
                var respuesta = await _httpClient.PostAsJsonAsync("prestamos", nuevoPrestamo);
                return respuesta.IsSuccessStatusCode; // Devuelve true si el código es 201
            }
            catch (Exception)
            {
                return false;
            }
        }

        /// <summary>
        /// DELETE /api/prestamos/{id}
        /// </summary>
        public async Task<bool> EliminarPrestamoAsync(int id)
        {
            try
            {
                var respuesta = await _httpClient.DeleteAsync($"prestamos/{id}");
                return respuesta.IsSuccessStatusCode; // Devuelve true si el código es 200
            }
            catch (Exception)
            {
                return false;
            }
        }

        /// <summary>
        /// PUT u Opcional /api/prestamos/{id}
        /// </summary>
        public async Task<bool> ActualizarEstadoPrestamoAsync(int id, string nuevoEstado)
        {
            try
            {
                var datos = new { estado = nuevoEstado };
                var respuesta = await _httpClient.PutAsJsonAsync($"prestamos/{id}", datos);
                return respuesta.IsSuccessStatusCode;
            }
            catch (Exception)
            {
                return false;
            }
        }
    }
}
