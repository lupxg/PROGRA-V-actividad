using System;
using System.Diagnostics;
using CommunityToolkit.Mvvm.ComponentModel;
using CommunityToolkit.Mvvm.Input;

namespace App.ViewModels;

public partial class MainWindowViewModel : ViewModelBase
{
    public IRelayCommand MessageCommand {  get;}
    public MainWindowViewModel()
    {
        MessageCommand = new RelayCommand(Message);
        
    }

    
    public void Message()
    {
        Debug.WriteLine("Hello, World!");
    }
    [RelayCommand]
    public void Prueba()
    {
        Debug.WriteLine("Prueba");
    }
}
