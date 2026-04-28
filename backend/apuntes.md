# APUNTES Y AUTOMATIZACION
==========================

## Apuntes
==========

### HTTP Status Code
#### Exito
200 -> OK (GET, PUT/PATCH, DELETE) // Operacion exitosa, no se ha creado nada nuevo
201 -> CREATED (POST) // Creacion exitosa, se ha creado un objeto nuevo
204 -> NO CONTENT (DELETE) // Peticion procesada correctamente, no devuelve nada

#### Error
400 -> BAD REQUEST // La peticion esta mal formada o le faltan datos. ej: no rellenar el campo password
404 -> NOT FOUND // El cliente esta pidiendo un recurso que no existe. ej: busca un equipo con un id que no existe 
409 -> CONFLICT // Formato correcto pero choca con los datos del servidor. ej: intentar registrar un usuario que ya existe


### Apuntes varios
PUT -> Actualiza el objeto entero // hay que enviar todos los campos
PATCH -> Actualiza solo un campo del objeto // solo hay que enviar el campo que quieres actualizar

Idempotencia -> Una operacion es idempotente si al ejecutarla muchas veces seguidas obtienes el mismo resultado
SI Idempotente -> Un boton de un ascensor, si le das 10 veces seguidas al boton del piso 5 el resultado es el mismo, ira al piso 5
NO Idempotente -> Boton de añadir al carrito, si le das 1 vez añades el producto una vez, y si le das 5 veces añade el producto 5 veces

Ataques de enumeracion -> Por ejemplo para un endpoint que envie un correo para restablecer la contraseña de un usuario a partir de su email
Si se muestran mensaje especificos (ej: email no encontrado, email invalido) el atacante sabra cual es el error y podra usar fuerza bruta
Si NO se muestran mensajes especificos (ej: credenciales inválidas en ambos casos de error) el atacante no sabe cual es el error y no podrá usar fuerza bruta




## Tareas a realizar para la automatización del proyecto
========================================================

### Eliminar tokens de refresco ya caducados (hacer con un cronjob para que se ejecute automáticamente en una fecha y hora)
php bin/console gesdinet:jwt:clear

### Cron para ejecutar automáticamente en una fecha y hora los scrapers y los generadores de archivo de python tanto de jugadores como partidos 
/home/jorge/python/datos_fantasy/jugadores/scraperJugadores.py -> y luego -> generarJugadores.py
/home/jorge/python/datos_fantasy/partidos/scraperPartidos.py -> y luego -> generarPartidos.py

### Cron para ejecutar automaticamente los comandos de symfony para sincronizar jugadores (y sus puntuaciones) y partidos | despues de ejecutar lo de arriba
/home/jorge/dwes/pruebas/src/Command/SincronizarJugadoresCommand.php
/home/jorge/dwes/pruebas/src/Command/SincronizarPartidosCommand.php
