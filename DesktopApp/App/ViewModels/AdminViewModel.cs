using App.Models;
using App.Services;
using App.Views;
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

namespace App.ViewModels
{
    public partial class AdminViewModel : ViewModelBase
    {
        private readonly LibroService _libroService = new();

        [ObservableProperty] private ObservableCollection<Libro> _libros = new();
        private List<Libro> _todosLosLibros = new();

        // Campos de Formulario
        [ObservableProperty] private int _idFormulario;
        [ObservableProperty] private string _tituloFormulario = string.Empty;
        [ObservableProperty] private string _autorFormulario = string.Empty;
        [ObservableProperty] private string _categoriaFormulario = string.Empty;
        [ObservableProperty] private string _stockFormulario = "0";
        [ObservableProperty] private string _disponibleFormulario = "0";
        [ObservableProperty] private string? _imagenBase64Formulario;
        [ObservableProperty] private string _nombreArchivoImagen = "Sin portada seleccionada";

        [ObservableProperty] private string _textoBusqueda = string.Empty;
        [ObservableProperty] private bool _esEdicion;
        [ObservableProperty] private string _mensajeFormulario = string.Empty;

        private Libro? _libroSeleccionado;
        public Libro? LibroSeleccionado
        {
            get => _libroSeleccionado;
            set
            {
                if (SetProperty(ref _libroSeleccionado, value) && value != null)
                {
                    IdFormulario = value.Id;
                    TituloFormulario = value.Titulo;
                    AutorFormulario = value.Autor;
                    CategoriaFormulario = value.Categoria;
                    StockFormulario = value.Stock.ToString();
                    DisponibleFormulario = value.Disponible.ToString();
                    ImagenBase64Formulario = null; // No sobreescribir a menos que elija una nueva
                    NombreArchivoImagen = !string.IsNullOrEmpty(value.ImagenUrl) ? "Imagen guardada en Servidor" : "Sin portada";
                    EsEdicion = true;
                    MensajeFormulario = string.Empty;
                }
            }
        }

        public AdminViewModel()
        {
            Task.Run(async () => await CargarLibrosAsync());
        }

        [RelayCommand]
        public async Task CargarLibrosAsync()
        {
            var lista = await _libroService.ObtenerLibrosAsync();
            _todosLosLibros = lista;

            
            Avalonia.Threading.Dispatcher.UIThread.Post(() =>
            {
                Libros.Clear();
                foreach (var libro in lista)
                {
                    Libros.Add(libro);
                }
            });
        }

        [RelayCommand]
        private void BuscarLibro()
        {
            if (string.IsNullOrWhiteSpace(TextoBusqueda))
            {
                Libros = new ObservableCollection<Libro>(_todosLosLibros);
            }
            else
            {
                var filtrados = _todosLosLibros.Where(l =>
                    (l.Titulo != null && l.Titulo.Contains(TextoBusqueda, StringComparison.OrdinalIgnoreCase)) ||
                    (l.Autor != null && l.Autor.Contains(TextoBusqueda, StringComparison.OrdinalIgnoreCase)) ||
                    (l.Categoria != null && l.Categoria.Contains(TextoBusqueda, StringComparison.OrdinalIgnoreCase))
                );
                Libros = new ObservableCollection<Libro>(filtrados);
            }
        }

        [RelayCommand]
        private async Task SeleccionarImagen()
        {

            var desktopLifetime = App.Current?.ApplicationLifetime
                as Avalonia.Controls.ApplicationLifetimes.IClassicDesktopStyleApplicationLifetime;

            if (desktopLifetime?.MainWindow == null) return;


            var topLevel = TopLevel.GetTopLevel(desktopLifetime.MainWindow);
            if (topLevel == null) return;

            var archivos = await topLevel.StorageProvider.OpenFilePickerAsync(new FilePickerOpenOptions
            {
                Title = "Seleccionar Portada del Libro",
                FileTypeFilter = new[] { FilePickerFileTypes.ImageAll }
            });

            if (archivos.Count > 0)
            {
                var archivo = archivos[0];
                NombreArchivoImagen = archivo.Name;

                using var stream = await archivo.OpenReadAsync();
                using var memoryStream = new MemoryStream();
                await stream.CopyToAsync(memoryStream);
                byte[] imageBytes = memoryStream.ToArray();

                string extension = Path.GetExtension(archivo.Name).Replace(".", "").ToLower();
                if (extension == "jpg") extension = "jpeg";

                ImagenBase64Formulario = $"data:image/{extension};base64,{Convert.ToBase64String(imageBytes)}";
            }
        }

        [RelayCommand]
        private async Task GuardarLibro()
        {
            if (string.IsNullOrWhiteSpace(TituloFormulario) || string.IsNullOrWhiteSpace(AutorFormulario))
            {
                MostrarMensaje("Título y Autor son obligatorios.");
                return;
            }

            if (!int.TryParse(StockFormulario, out int stockNumerico) || stockNumerico < 0)
            {
                MostrarMensaje("El Stock Total debe ser un número válido.");
                return;
            }

            int disponibleNumerico = EsEdicion ? int.Parse(DisponibleFormulario) : stockNumerico;

            var libroDto = new Libro
            {
                Id = IdFormulario,
                Titulo = TituloFormulario,
                Autor = AutorFormulario,
                Categoria = CategoriaFormulario,
                Stock = stockNumerico,
                Disponible = disponibleNumerico,
                ImagenBase64 = ImagenBase64Formulario // Se envía la cadena base64 limpia a tu API
            };

            bool exito = EsEdicion ? await _libroService.ActualizarLibroAsync(libroDto)
                : await _libroService.AgregarLibroAsync(libroDto);

            if (exito)
            {
                MostrarMensaje(EsEdicion ? "¡Libro actualizado con éxito!" : "¡Libro creado con éxito!");
                LimpiarFormulario();
                await CargarLibrosAsync(); // Refresca inmediatamente la grilla
            }
            else
            {
                MostrarMensaje("Error al procesar la solicitud en la API.");
            }
        }

        private void MostrarMensaje(string mensaje)
        {
            MensajeFormulario = mensaje;
        }

        [RelayCommand]
        private async Task EliminarLibro()
        {

            if (IdFormulario == 0 || !EsEdicion)
            {
                MensajeFormulario = "Selecciona un libro de la lista para poder eliminarlo.";
                return;
            }

            MensajeFormulario = "Eliminando libro...";


            bool exito = await _libroService.EliminarLibroAsync(IdFormulario);

            if (exito)
            {

                
                var libroEnCache = _todosLosLibros.FirstOrDefault(l => l.Id == IdFormulario);
                if (libroEnCache != null)
                {
                    _todosLosLibros.Remove(libroEnCache);
                }

                
                Avalonia.Threading.Dispatcher.UIThread.Post(() =>
                {
                    var libroABorrar = Libros.FirstOrDefault(l => l.Id == IdFormulario);
                    if (libroABorrar != null)
                    {
                        Libros.Remove(libroABorrar);
                    }
                });


                LimpiarFormulario();
                MensajeFormulario = "Libro eliminado correctamente.";
                
            }
            else
            {
                MensajeFormulario = "Error del servidor o de conexión al intentar eliminar el libro.";
            }
        }
        

        [RelayCommand]
        private void LimpiarFormulario()
        {
            IdFormulario = 0;
            TituloFormulario = string.Empty;
            AutorFormulario = string.Empty;
            CategoriaFormulario = string.Empty;
            StockFormulario = "0";
            DisponibleFormulario = "0";
            ImagenBase64Formulario = null;
            NombreArchivoImagen = "Sin portada seleccionada";
            EsEdicion = false;
            LibroSeleccionado = null;
            MensajeFormulario = string.Empty;
        }
    }
}
