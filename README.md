# MultimediosAPI_ProyectoGrupal

API REST en PHP y MySQL para la gestion de tickets de soporte tecnico.

Proyecto del curso IF7102 - Multimedios. Entrega 2: Construccion del API.

## 1. Descripcion

El proyecto implementa una API para un Sistema de Tickets TI.

La API permite gestionar:

- Tickets.
- Asignaciones de tickets a tecnicos.
- Comentarios de seguimiento.
- Historial de cambios.
- Usuarios, roles, categorias, prioridades y estados.

## 2. Tecnologias

- PHP.
- MySQL.
- PDO para conexion segura a base de datos.
- JSON para entrada y salida de datos.
- Postman para pruebas.

## 3. Configuracion de base de datos

La conexion se configura con un archivo `.env` en la raiz del proyecto.

Ejemplo:

```env
DB_HOST=localhost
DB_PORT=3306
DB_NAME=multimedios_tickets
DB_USER=root
DB_PASSWORD=
DB_CHARSET=utf8mb4
```

El archivo `.envexample` queda como plantilla para compartir la configuracion sin exponer credenciales reales.

## 4. Crear la base de datos

Desde la raiz del proyecto:

```powershell
Get-Content -Raw database\proyectoGrupal_bd.sql | & "C:\xampp\mysql\bin\mysql.exe" -u root --default-character-set=utf8mb4
```

El script crea la base `multimedios_tickets`, sus tablas y datos de prueba.

## 5. Ejecutar la API

Desde la raiz del proyecto:

```powershell
& "C:\xampp\php\php.exe" -S 127.0.0.1:8000 -t api
```

URL base:

```text
http://127.0.0.1:8000
```

Para detener el servidor local, presione `Ctrl + C`.

## 6. Forma de uso

La API usa el parametro `recurso` para seleccionar el modulo.

Ejemplos:

```text
http://127.0.0.1:8000/?recurso=tickets
http://127.0.0.1:8000/?recurso=asignaciones
http://127.0.0.1:8000/?recurso=comentarios
http://127.0.0.1:8000/?recurso=historial
```

Si no se envia `recurso`, se usa `tickets` por defecto:

```text
http://127.0.0.1:8000/
```

## 7. Endpoints principales

### 7.1 Tickets

Listar:

```http
GET http://127.0.0.1:8000/?recurso=tickets
```

Obtener por id:

```http
GET http://127.0.0.1:8000/?recurso=tickets&id=1
```

Crear:

```http
POST http://127.0.0.1:8000/?recurso=tickets
Content-Type: application/json
```

```json
{
  "titulo": "Problema con impresora",
  "descripcion": "La impresora no responde desde la manana",
  "categoria_id": 1,
  "prioridad_id": 2,
  "estado_id": 1,
  "solicitante_id": 3,
  "tecnico_id": null
}
```

Actualizar:

```http
PUT http://127.0.0.1:8000/?recurso=tickets&id=1
Content-Type: application/json
```

Eliminar:

```http
DELETE http://127.0.0.1:8000/?recurso=tickets&id=1
```

### 7.2 Asignaciones

Listar:

```http
GET http://127.0.0.1:8000/?recurso=asignaciones
```

Filtrar por ticket:

```http
GET http://127.0.0.1:8000/?recurso=asignaciones&ticket_id=2
```

Crear:

```http
POST http://127.0.0.1:8000/?recurso=asignaciones
Content-Type: application/json
```

```json
{
  "ticket_id": 1,
  "tecnico_id": 2,
  "asignado_por": 1
}
```

Actualizar:

```http
PUT http://127.0.0.1:8000/?recurso=asignaciones&id=1
Content-Type: application/json
```

Eliminar:

```http
DELETE http://127.0.0.1:8000/?recurso=asignaciones&id=1
```

### 7.3 Comentarios

Listar:

```http
GET http://127.0.0.1:8000/?recurso=comentarios
```

Filtrar por ticket:

```http
GET http://127.0.0.1:8000/?recurso=comentarios&ticket_id=2
```

Crear:

```http
POST http://127.0.0.1:8000/?recurso=comentarios
Content-Type: application/json
```

```json
{
  "ticket_id": 1,
  "usuario_id": 2,
  "contenido": "Se reviso el equipo y se encontro falla en la fuente."
}
```

Actualizar:

```http
PUT http://127.0.0.1:8000/?recurso=comentarios&id=1
Content-Type: application/json
```

Eliminar:

```http
DELETE http://127.0.0.1:8000/?recurso=comentarios&id=1
```

### 7.4 Historial

Listar:

```http
GET http://127.0.0.1:8000/?recurso=historial
```

Filtrar por ticket:

```http
GET http://127.0.0.1:8000/?recurso=historial&ticket_id=2
```

Crear:

```http
POST http://127.0.0.1:8000/?recurso=historial
Content-Type: application/json
```

```json
{
  "ticket_id": 1,
  "usuario_id": 2,
  "accion": "Cambio de estado",
  "valor_anterior": "Pendiente",
  "valor_nuevo": "En proceso"
}
```

Actualizar:

```http
PUT http://127.0.0.1:8000/?recurso=historial&id=1
Content-Type: application/json
```

Eliminar:

```http
DELETE http://127.0.0.1:8000/?recurso=historial&id=1
```

## 8. Documentacion del codigo

Los archivos principales usan secciones con este formato:

```php
/**
 * ============================================================
 * SECTION: Nombre de la seccion
 * ============================================================
 */
```

Tambien se agregaron comentarios tipo DocBlock para explicar:

- Responsabilidad de cada clase.
- Responsabilidad de cada metodo publico.
- Validaciones principales.
- Uso de cada archivo de rutas.

## 9. Coleccion Postman

La coleccion de pruebas esta en:

```text
postman/MultimediosAPI_Tickets.postman_collection.json
```

La coleccion incluye pruebas para:

- Tickets.
- Asignaciones.
- Comentarios.
- Historial.
