using App.Models.Dtos;
using System;
using App.Models;
using System.Net.Http;
using System.Net.Http.Json;

using System.Threading.Tasks;

namespace App.Services;

internal class LoginService
{
    private HttpClient _client = new();

    public LoginService()
    {
        _client.BaseAddress = new Uri("http://localhost:3000/api/");
    }

    public async Task<(bool Succes, string Message, User? User)> LoginAsync(string? username, string? password)
    {
        if (string.IsNullOrWhiteSpace(username) || string.IsNullOrWhiteSpace(password))
        {
            return (false, "Por favor, rellene todos los campos.",null);
        }
        try
        {
            var loginDto = new UserLoginDto { Email = username, Password = password };

            
            var response = await _client.PostAsJsonAsync("login", loginDto);

            var result = await response.Content.ReadFromJsonAsync<LoginResponse>();

            if (response.IsSuccessStatusCode && result?.Status == "success")
            {
                if(result.User!.Correo == username)
                {
                    if (response.IsSuccessStatusCode && result?.Status == "success")
                    {
                        return (true, result.Message, result.User);
                    }

                }

            }

            return (false, result?.Message, null);
        }
        catch (HttpRequestException)
        {
            return (false, "No se pudo conectar con el servidor. ¿Está la API encendida?",null);
        }
        catch (Exception ex)
        {
            return (false, $"Error inesperado: {ex.Message}",null);
        }


    }



}
