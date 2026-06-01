using System.Text.Json.Serialization;
using App.Models;

namespace App.Services;

// Clase para mapear la respuesta exacta de la api
internal class LoginResponse
{
    [JsonPropertyName("status")]
    public string? Status { get; set; }

    [JsonPropertyName("message")]
    public string? Message { get; set; }

    [JsonPropertyName("user")]
    public User? User { get; set; }
}