using App.Services;
using App.Views;
using CommunityToolkit.Mvvm.ComponentModel;
using CommunityToolkit.Mvvm.Input;
using System.Diagnostics;
using System.Threading.Tasks;


namespace App.ViewModels;

// La clase DEBE ser "partial" para que el Source Generator funcione
public partial class LoginViewModel : ViewModelBase
{

    private readonly LoginService _loginService = new();

    [ObservableProperty]
    private string? _correo;

    [ObservableProperty]
    private string? _password;

    [ObservableProperty]
    private string? _mensajeError;

    [ObservableProperty]
    private bool _estaCargando;

 
    [RelayCommand]
    private async Task IniciarSesion()
    {
        EstaCargando = true;
        MensajeError = string.Empty;

        var (success, message,user) = await _loginService.LoginAsync(Correo, Password);
        EstaCargando = false;

       

        if (success && user != null)
        {
            var mainWindowViewModel = new MainWindowViewModel(user.Rol);
            var mainWindow = new MainWindow
            {
                DataContext = mainWindowViewModel
            };
            mainWindow.Show();

            if (Avalonia.Application.Current?.ApplicationLifetime is Avalonia.Controls.ApplicationLifetimes.IClassicDesktopStyleApplicationLifetime desktop)
            {
                var loginWindow = desktop.MainWindow; 
                desktop.MainWindow = mainWindow;      
                loginWindow?.Close();                 
            }
        }
        else
        {
            
            MensajeError = message;
        }
    }
}