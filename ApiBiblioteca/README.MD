# Api para sistema de biblioteca con PHP puro

## Generalidades para el desarrollador
- el archivo router.php solo sirve para el despliegue de desarrollo usando Vscode y 
la extension php server, no afecta en nada al despliegue de produccion

- index.php es el corazon de la api este se encarga de enrutar y brindar configuraciones
y generalidades de la api

- para la subida de las images hay 4 formatos aceptados [png,jpge,jpg,webp]
- para la subida de las images se debe usar data:image/png;base64, o data:image/jpeg;base64, etc para que la api acepte y guarde el archivo en public/uploads


## ENDPOINTS
### Autenticación
POST /api/login  - Terminado
POST /api/register - Terminado

### Libros
GET /api/libros - Terminado
GET /api/libros/{nombre} //este fue cambiado en el doc - Terminado
POST /api/libros - Terminado
PUT /api/libros/{id} -  Terminado






