

## Clase 5 – Procesamiento de formularios y seguridad web (PHP)

Carpeta: [`clase5/`](./clase5). Proyecto Tanianailsart con estructura modular (`include`/`require`).

### Formularios creados

**1. Reservar turno – método POST** (`reservar.php`)

Campos: nombre, correo, teléfono (opcional), servicio y mensaje.

- Se leen con `filter_input(INPUT_POST, ...)` y se sanitizan con `trim()` + `htmlspecialchars()`.
- Validación en el servidor: campos obligatorios (`empty`), formato de email (`filter_var`), teléfono con expresión regular, servicio dentro de una lista permitida y largo máximo del mensaje.
- Si hay errores se muestran mensajes por campo y se conservan los datos ya escritos.
- Si todo es correcto se muestra un mensaje de éxito y se limpia el formulario.

**2. Buscar servicios – método GET** (`buscar.php`)

Filtros por texto y categoría que viajan por la URL (`buscar.php?q=gel&categoria=gel`).

- Entradas sanitizadas con `filter_input(INPUT_GET, ...)`, `trim()` y `htmlspecialchars()`.
- Se valida el largo de la búsqueda y que la categoría pertenezca a la lista permitida.
- Se informa la cantidad de resultados o que no hubo coincidencias.

### Seguridad (XSS)

Ninguna variable de `$_POST` o `$_GET` se muestra sin pasar por `limpiar()` (`includes/funciones.php`). Un `<script>` enviado en un campo se ve como texto y no se ejecuta.

### Capturas

<img width="1704" height="487" alt="clase 5 formualarioy seguridad web" src="https://github.com/user-attachments/assets/da6dec9f-7531-41fd-b5f9-20cf4ceba7c8" />
<img width="1655" height="830" alt="Captura de pantalla 2026-10-23 190516" src="https://github.com/user-attachments/assets/2ad960e6-c372-4b2e-9db4-c0d09fa8b83c" />

<img width="1812" height="626" alt="Captura de pantalla 2026-10-23 190523" src="https://github.com/user-attachments/assets/c97acceb-295c-44e6-ab22-82879fce080c" />


### Cómo ejecutarlo

Copiar la carpeta `clase5` en `C:\wamp64\www\` (WampServer) y abrir http://localhost/clase5/
