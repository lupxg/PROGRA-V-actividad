using App.Models;
using App.Services;
using CommunityToolkit.Mvvm.ComponentModel;
using CommunityToolkit.Mvvm.Input;
using System;
using System.Collections.Generic;
using System.Collections.ObjectModel;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace App.ViewModels.AdminViewModels
{
    public partial class PedidosAdminViewModel : ViewModelBase
    {
        private readonly PrestamoService _prestamoService;
        private List<PrestamoAdmin> _todosLosPrestamos = new();

        [ObservableProperty] private ObservableCollection<PrestamoAdmin> _prestamos = new();
        [ObservableProperty] private bool _estaCargando;
        [ObservableProperty] private string _textoBusqueda = string.Empty;
        [ObservableProperty] private string _filtroEstadoActual = "Todos";
        [ObservableProperty] private int _paginaActual = 1;

        // --- PROPIEDADES DEL FORMULARIO DE GESTIÓN ---
        [ObservableProperty] private int _usuarioIdFormulario;
        [ObservableProperty] private int _libroIdFormulario;
        [ObservableProperty] private string _fechaDevolucionFormulario = DateTime.Now.AddDays(7).ToString("yyyy-MM-dd");
        [ObservableProperty] private string _estadoFormulario = "Pendiente";
        [ObservableProperty] private string _mensajeFormulario = string.Empty;

        // Propiedad que define si estamos editando un registro existente o creando uno nuevo
        [ObservableProperty] private bool _esEdicion;

        private PrestamoAdmin? _prestamoSeleccionado;
        public PrestamoAdmin? PrestamoSeleccionado
        {
            get => _prestamoSeleccionado;
            set
            {
                if (SetProperty(ref _prestamoSeleccionado, value))
                {
                    if (value != null)
                    {
                        // Rellenar el formulario con el pedido seleccionado para actualizar o eliminar
                        UsuarioIdFormulario = value.UsuarioId;
                        LibroIdFormulario = value.LibroId;
                        FechaDevolucionFormulario = value.FechaDevolucionString;
                        EstadoFormulario = value.Estado;
                        EsEdicion = true;
                        MensajeFormulario = $"Editando Pedido #{value.Id}";
                    }
                    else
                    {
                        LimpiarFormulario();
                    }
                }
            }
        }

        public PedidosAdminViewModel()
        {
            _prestamoService = new PrestamoService();
            Task.Run(async () => await CargarPrestamosAsync());
        }

        [RelayCommand]
        public async Task CargarPrestamosAsync()
        {
            if (EstaCargando) return;
            EstaCargando = true;
            try
            {
                var respuestaApi = await _prestamoService.ObtenerPrestamosAsync(PaginaActual);
                if (respuestaApi != null && respuestaApi.Status == "success")
                {
                    _todosLosPrestamos = respuestaApi.Data;
                    AplicarFiltrosYBusqueda();
                }
            }
            catch (Exception ex)
            {
                MensajeFormulario = "Error de comunicación con el servidor.";
            }
            finally
            {
                EstaCargando = false;
            }
        }

        // --- COMANDO GUARDAR (CREAR O ACTUALIZAR) ---
        [RelayCommand]
        public async Task GuardarPrestamoCommandAsync()
        {
            if (UsuarioIdFormulario <= 0 || LibroIdFormulario <= 0)
            {
                MensajeFormulario = "Los IDs de Usuario y Libro deben ser válidos.";
                return;
            }

            EstaCargando = true;
            MensajeFormulario = "Procesando...";

            if (EsEdicion && PrestamoSeleccionado != null)
            {
                // --- ACTUALIZAR PEDIDO EXISTENTE ---
                bool exito = await _prestamoService.ActualizarEstadoPrestamoAsync(PrestamoSeleccionado.Id, EstadoFormulario);
                if (exito)
                {
                    MensajeFormulario = "Pedido actualizado correctamente.";
                    await CargarPrestamosAsync();
                    LimpiarFormulario();
                }
                else
                {
                    MensajeFormulario = "No se pudo actualizar el estado (Verifica el método en tu API).";
                }
            }
            else
            {
                // --- CREAR NUEVO PEDIDO ---
                var nuevoPrestamo = new PrestamoAdmin
                {
                    UsuarioId = UsuarioIdFormulario,
                    LibroId = LibroIdFormulario,
                    FechaDevolucionString = FechaDevolucionFormulario,
                    Estado = EstadoFormulario
                };

                bool exito = await _prestamoService.CrearPrestamoAsync(nuevoPrestamo);
                if (exito)
                {
                    MensajeFormulario = "Prestamo creado exitosamente.";
                    await CargarPrestamosAsync();
                    LimpiarFormulario();
                }
                else
                {
                    MensajeFormulario = "Error de servidor al crear préstamo.";
                }
            }
            EstaCargando = false;
        }

        // --- COMANDO ELIMINAR ---
        [RelayCommand]
        public async Task EliminarPrestamoCommandAsync()
        {
            if (PrestamoSeleccionado == null) return;

            EstaCargando = true;
            MensajeFormulario = "Eliminando de la base de datos...";

            // Llama a tu función destroy() del controlador de PHP
            bool exito = await _prestamoService.EliminarPrestamoAsync(PrestamoSeleccionado.Id);

            if (exito)
            {
                MensajeFormulario = "Préstamo eliminado definitivamente.";
                await CargarPrestamosAsync();
                LimpiarFormulario();
            }
            else
            {
                MensajeFormulario = "No se pudo eliminar el registro.";
            }
            EstaCargando = false;
        }

        // --- COMANDO NUEVO / CANCELAR EDICIÓN ---
        [RelayCommand]
        public void LimpiarFormulario()
        {
            UsuarioIdFormulario = 0;
            LibroIdFormulario = 0;
            FechaDevolucionFormulario = DateTime.Now.AddDays(7).ToString("yyyy-MM-dd");
            EstadoFormulario = "Pendiente";
            EsEdicion = false;
            PrestamoSeleccionado = null;
            MensajeFormulario = "Modo: Nuevo Registro";
        }

        // --- LOGICA DE FILTRADOS ---
        [RelayCommand] private void CambiarFiltroEstado(string nuevoEstado) { FiltroEstadoActual = nuevoEstado; AplicarFiltrosYBusqueda(); }
        [RelayCommand] private void BuscarPrestamo() => AplicarFiltrosYBusqueda();

        private void AplicarFiltrosYBusqueda()
        {
            IEnumerable<PrestamoAdmin> resultado = _todosLosPrestamos;

            if (FiltroEstadoActual != "Todos")
                resultado = resultado.Where(p => p.Estado.Equals(FiltroEstadoActual, StringComparison.OrdinalIgnoreCase));

            if (!string.IsNullOrWhiteSpace(TextoBusqueda))
            {
                resultado = resultado.Where(p =>
                    p.UsuarioId.ToString().Contains(TextoBusqueda) ||
                    p.LibroId.ToString().Contains(TextoBusqueda)
                );
            }

            Avalonia.Threading.Dispatcher.UIThread.Post(() =>
            {
                Prestamos.Clear();
                foreach (var prestamo in resultado) Prestamos.Add(prestamo);
            });
        }
    }

}

