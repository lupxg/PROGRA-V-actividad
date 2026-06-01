

namespace App.Models
{
    internal class User
    {
        public int Id { get; set; }
        public string? Nombre { get; set; }
        public string? Correo { get; set; }

        public string? Password { get; set; }

        public string Rol {  get; set; }  = "lector";


    }
}
