using App.Models;
using System;
using System.Collections.Generic;
using System.Net.Http;
using System.Net.Http.Json;
using System.Text;
using System.Threading.Tasks;

namespace App.Services
{
    internal class LibroService
    {
        private readonly HttpClient _http;
        private const string BaseUrl = "http://localhost:3000/api/libros"; // Ajusta a tu puerto real si varía

        public LibroService()
        {
            _http = new HttpClient();
        }

        public async Task<List<Libro>> ObtenerLibrosAsync()
        {
            try
            {
                
                var respuesta = await _http.GetFromJsonAsync<ApiResponse<List<Libro>>>(BaseUrl);
                return respuesta?.Data ?? new List<Libro>();
            }
            catch
            {
                return new List<Libro>();
            }
        }

        public async Task<bool> AgregarLibroAsync(Libro libro)
        {
            try
            {
                var respuesta = await _http.PostAsJsonAsync(BaseUrl, libro);
                return respuesta.IsSuccessStatusCode;
            }
            catch
            {
                return false;
            }
        }

        public async Task<bool> ActualizarLibroAsync(Libro libro)
        {
            try
            {
                var respuesta = await _http.PutAsJsonAsync($"{BaseUrl}/{libro.Id}", libro);
                return respuesta.IsSuccessStatusCode;
            }
            catch
            {
                return false;
            }
        }
        public async Task<bool> EliminarLibroAsync(int id)
        {
            try
            {
                // Envia la peticion DELETE a http://localhost:3000/api/libros/{id}
                var respuesta = await _http.DeleteAsync($"{BaseUrl}/{id}");
                return respuesta.IsSuccessStatusCode;
            }
            catch
            {
                return false;
            }
        }

    }
}
