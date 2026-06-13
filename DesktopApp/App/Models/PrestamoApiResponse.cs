using System;
using System.Collections.Generic;
using System.Text;
using System.Text.Json.Serialization;

namespace App.Models
{
    public class PrestamoApiResponse
    {

        [JsonPropertyName("status")]
        public string Status { get; set; } = string.Empty;

        [JsonPropertyName("page")]
        public int Page { get; set; }

        [JsonPropertyName("count")]
        public int Count { get; set; }

        [JsonPropertyName("data")]
        public List<PrestamoAdmin> Data { get; set; } = new();

    }
}
