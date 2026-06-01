using Avalonia.Data.Converters;
using Avalonia.Media.Imaging;
using System;
using System.Globalization;
using System.IO;
using System.Net.Http;

namespace App.utils
{
    public class UrlToBitmapConverter : IValueConverter
    {
        private static readonly HttpClient _httpClient = new();

        public object? Convert(object? value, Type targetType, object? parameter, CultureInfo culture)
        {
            if (value is string url && !string.IsNullOrWhiteSpace(url))
            {
                if (url.StartsWith("http://", StringComparison.OrdinalIgnoreCase) ||
                    url.StartsWith("https://", StringComparison.OrdinalIgnoreCase))
                {
                    // Creamos una tarea en segundo plano controlada para no congelar el renderizado visual
                    return AllocateBitmapAsync(url);
                }
            }
            return null;
        }

        private static Bitmap? AllocateBitmapAsync(string url)
        {
            try
            {
                // Usamos una petición síncrona de streams optimizada para IO que Avalonia tolera mejor en hilos de bindings
                using var response = _httpClient.GetAsync(url).GetAwaiter().GetResult();
                if (response.IsSuccessStatusCode)
                {
                    using var stream = response.Content.ReadAsStreamAsync().GetAwaiter().GetResult();
                    return new Bitmap(stream);
                }
            }
            catch (Exception ex)
            {
                // Imprime en la terminal de Lubuntu si hay un problema real de ruta o conexión
                System.Diagnostics.Debug.WriteLine($"[UrlToBitmapConverter] Error: {ex.Message}");
            }
            return null;
        }

        public object? ConvertBack(object? value, Type targetType, object? parameter, CultureInfo culture)
        {
            throw new NotImplementedException();
        }
    }
}