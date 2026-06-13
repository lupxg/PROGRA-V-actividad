using Avalonia.Data.Converters;
using Avalonia.Media.Imaging;
using System;
using System.Globalization;
using System.IO;
using System.Diagnostics;
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
                   
                    return AllocateBitmapAsync(url);
                }
            }
            return null;
        }

        private static Bitmap? AllocateBitmapAsync(string url)
        {
            try
            {
                
                using var response = _httpClient.GetAsync(url).GetAwaiter().GetResult();
                if (response.IsSuccessStatusCode)
                {
                    using var stream = response.Content.ReadAsStreamAsync().GetAwaiter().GetResult();
                    return new Bitmap(stream);
                }
            }
            catch (Exception ex)
            {
                
                Debug.WriteLine($"[UrlToBitmapConverter] Error: {ex.Message}");
            }
            return null;
        }

        public object? ConvertBack(object? value, Type targetType, object? parameter, CultureInfo culture)
        {
            throw new NotImplementedException();
        }
    }
}