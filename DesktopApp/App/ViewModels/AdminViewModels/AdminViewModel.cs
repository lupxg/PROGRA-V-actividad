using App.Models;
using App.Services;
using App.Views.AdminViews;
using Avalonia.Controls;
using App.utils;
using Avalonia.Platform.Storage;
using CommunityToolkit.Mvvm.ComponentModel;
using CommunityToolkit.Mvvm.Input;
using System;
using System.Collections.ObjectModel;
using System.IO;
using System.Linq;

using System.Threading.Tasks;
using System.Collections.Generic;

namespace App.ViewModels.AdminViewModels
{
    public partial class AdminViewModel : ViewModelBase
    {
        
        [ObservableProperty] private object? _vistaActual;

        public AdminViewModel()
        {
            MostrarSeccionLibros();
        }

        [RelayCommand]
        private void MostrarSeccionLibros()
        {
            
            VistaActual = new LibrosGestionViewModel();
        }

        [RelayCommand]
        private void MostrarSeccionPedidos()
        {
            
            VistaActual = new PedidosAdminView();
        }



    }
}
