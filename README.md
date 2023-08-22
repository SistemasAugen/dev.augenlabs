# Laravel 5.5 and Vue.js

Para usar este proyecto es necesario lo siguiente:
### REQUISITOS
```
* PHP version >= 7.0
* Componser
* NodeJS (version >= 8.9.4) 
```

### INSTALACION

Lo primero que necesitamos para instalar el proyecto es instalar las librerias PHP con Composer.

Ejecuta en terminal:

```
composer install
```
Despues descargamos los paquetes necesarios para vuejs y compilamos los archivos
```
npm install
```
Por ultimo es necesario crear las tablas y sembrar los datos en la base de datos
```
php artisan migrate --seed
```
Con esto ya estaria instalado el proyecto.

### EJECUTAR LOCALMENTE
Primero hay que activar el servidor local (xampp, laragon, etc...)
Ejecutar en terminal (para ver la pagina en el navegador):
```
php -S localhost:8000
```
En otra terminal ejecutar (para compilar el .js y compilar cada vez que haya un cambio en vue):
```
npm run watch
```
Para ver los cambios de vue subir al servidor el archivo:
```
public/js/admin.js
```
para pasar los cambios a productivo ejecutar el comando:
```
npm run prod
```
y subir el mismo archivo

### NOTA
No olvides antes agregarle la información de la base de datos al archivo .env



## License
las licencias de [Laravel](https://laravel.com/) y [Vue.JS](https://vuejs.org/).
