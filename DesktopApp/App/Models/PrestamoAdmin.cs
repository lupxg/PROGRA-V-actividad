using System;
using System.Collections.Generic;
using System.Text;
using System.Text.Json.Serialization;

namespace App.Models
{
    public class PrestamoAdmin
    {
        [JsonPropertyName("id")]
        public int Id { get; set; }

        [JsonPropertyName("usuario_id")]
        public int UsuarioId { get; set; }

        [JsonPropertyName("libro_id")]
        public int LibroId { get; set; }

        [JsonPropertyName("fecha_prestamo")]
        public string FechaPrestamoString { get; set; } = string.Empty;

        [JsonPropertyName("fecha_devolucion")]
        public string FechaDevolucionString { get; set; } = string.Empty;

        [JsonPropertyName("estado")]
        public string Estado { get; set; } = "Pendiente";

        // --- Propiedades Auxiliares para mostrar en la interfaz de Avalonia ---
        // Se usarán si modificas el SQL con JOINs o si deseas poblar los datos localmente
        public string NombreUsuario => $"Usuario #{UsuarioId}";
        public string TituloLibro => $"Libro #{LibroId}";

        // Conversión limpia de las fechas de texto provenientes de MySQL a DateTime de C#
        public DateTime? FechaPrestamo => DateTime.TryParse(FechaPrestamoString, out var dt) ? dt : null;
        public DateTime? FechaDevolucion => DateTime.TryParse(FechaDevolucionString, out var dt) ? dt : null;
    }
}
