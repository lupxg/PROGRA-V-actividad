using System;
using System.Collections.Generic;
using System.Text;
using System.Text.Json.Serialization;

namespace App.Models
{
    public class Libro
    {
        [JsonPropertyName("id")]
        public int Id { get; set; }

        [JsonPropertyName("titulo")]
        public string Titulo { get; set; } = string.Empty;

        [JsonPropertyName("autor")]
        public string Autor { get; set; } = string.Empty;

        [JsonPropertyName("categoria")]
        public string Categoria { get; set; } = string.Empty;

        [JsonPropertyName("stock")]
        public int Stock { get; set; }

        [JsonPropertyName("disponible")]
        public int Disponible { get; set; }

        [JsonPropertyName("imagen_url")]
        public string? ImagenUrl { get; set; }

        
        [JsonPropertyName("imagen_base64")]
        public string? ImagenBase64 { get; set; }
    }
}
