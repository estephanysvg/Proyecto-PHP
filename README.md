# Mis clientes CRUD version 1.1

## Descripción

Este es un CRUD (Crear, Leer, Actualizar, Eliminar) simple para gestionar clientes. Permite realizar operaciones básicas sobre la información de los clientes, almacenar imágenes y generar PDF con detalles.

## Instalación

1. Clonar el repositorio.
2. Añadir el vendor.
3. Probar login: 0 (user)--> login: Elbert password: Elbert | 1 (admin)--> login: Francesco password: Francesco

## Cambios en la Segunda Entrega

### 1. Eliminación de `AccesoDatos.php`

Se ha eliminado el archivo `AccesoDatos.php` debido a problemas de peso que impedían su subida al repositorio.

### 2. Nuevas Características

- **2) Errores PDO:**
  - He corregido las operaciones de PDO.
  
- **3) Manejo de Errores:**
  - Ahora se muestran mensajes de error cuando se introducen datos incorrectos. Los datos incorrectos son eliminados automáticamente.
 
- **5) Almacenamiento de Imágenes:**
  - Las imágenes ahora se almacenan correctamente tanto en la operación de subir como en la de modificar.

- **Tamaño de Imágenes:**
  - Se ha ajustado el tamaño permitido para subir imágenes. Ahora se aceptan imágenes de mayor tamaño.
 
- ** 7) Generación de PDF:**
  - El sistema imprime PDF con detalles, incluyendo la imagen si es de la carpeta 'uploads' (cruz a la hora de poner la foto de robot).
  
- **8) Contraseña Encriptada y tabla Users**
  - Ahora, la contraseña está encriptada en la base de datos. La aplicación también inicia sesión con el nombre de usuario (se utilizará el login como contraseña). El sql está como "Users.sql"
  
- **9) Inicio de Sesión:**
  - Se ha implementado la funcionalidad de inicio de sesión. Ahora es posible o entrar como "admin" (1) o "user" (0). Si es 0 solo puede acceder a visualizar los datos: lista y detalles. Si el rol es 1 podrá además modificar, borrar y eliminar usuarios



