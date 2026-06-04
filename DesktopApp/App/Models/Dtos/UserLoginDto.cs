using System;
using System.Collections.Generic;
using System.Text;
using System.Text.Json.Serialization;

namespace App.Models.Dtos
{
    internal class UserLoginDto
    {
        [JsonPropertyName("correo")]
        public string? Email { get; set; }
        public string? Password { get; set; }
    }
}
