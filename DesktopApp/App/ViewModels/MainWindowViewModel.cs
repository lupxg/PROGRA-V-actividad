using System;
using System.Diagnostics;
using CommunityToolkit.Mvvm.ComponentModel;
using CommunityToolkit.Mvvm.Input;
using App.ViewModels.AdminViewModels;

namespace App.ViewModels;

public partial class MainWindowViewModel : ViewModelBase
{
    

    [ObservableProperty]
    private ObservableObject? _vistaActual;
    public MainWindowViewModel(string rol)
    {
       
        DeterminarVistaPorRol(rol);

    }

    private void DeterminarVistaPorRol(string rol)
    {
       
        switch (rol.ToLower())
        {
            case "bibliotecario":
                VistaActual = new BibliotecarioViewModel();
                break;

            case "lector":
            default:
                VistaActual = new LectorViewModel();
                break;
            case "admin":
                VistaActual = new AdminViewModel();
                break;
        }
    }

}
