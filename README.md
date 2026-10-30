# Trinity Medical Center · Sistema de gestión hospitalaria

Aplicación web para administrar un hospital: pacientes, médicos, citas, historial clínico, camas y farmacia, con un panel principal de indicadores y gráficas. Proyecto final de la carrera de Ingeniería en Sistemas.

![Dashboard](docs/screenshots/dashboard.png)

## Funcionalidades

- **Autenticación por sesiones:** las páginas internas requieren iniciar sesión y no se pueden abrir después de cerrarla.
- **Panel principal:** totales de pacientes y médicos, citas del día, camas disponibles, medicamentos con stock bajo, tendencia de citas de los últimos 7 días y ocupación de camas por área.
- **Pacientes:** registro con datos de contacto, seguro social, domicilio, consultorio y horario (matutino o vespertino).
- **Médicos:** registro con especialidad, horario de entrada y salida, y teléfono.
- **Citas e historial clínico:** programación y cancelación de citas, y registro de diagnósticos y tratamientos.
- **Camas:** control de camas por área (UCI, General, Privada) y su estado (disponible u ocupada).
- **Farmacia:** inventario con alerta de stock bajo.
- **Interfaz:** menú lateral de iconos que se expande al pasar el cursor, buscador en cada tabla y textos largos plegables.

## Tecnologías

PHP 8 · MySQL / MariaDB · JavaScript · CSS · Chart.js · Docker

## Instalación

### Con Docker

```bash
git clone https://github.com/USUARIO/trinity-medical-center.git
cd trinity-medical-center
docker compose up -d --build
```

Espera un minuto a que la base termine de importarse y abre http://localhost:8080/login.php

### Con XAMPP

1. Copia la carpeta `app` dentro de `htdocs` (por ejemplo, como `hospital`).
2. En phpMyAdmin, importa `database/schema.sql`.
3. Abre http://localhost/hospital/login.php

## Acceso de demostración

| Usuario | Contraseña |
|---------|------------|
| admin   | admin123   |

Los datos incluidos son ficticios.

## Estructura

```
├── app/          código de la aplicación (PHP, CSS, JS, imágenes)
├── database/     estructura de la base de datos y datos de prueba
├── docs/         pseudocódigo, diagramas de flujo y capturas
├── Dockerfile
└── docker-compose.yml
```

## Documentación

El pseudocódigo y los diagramas de flujo del sistema están en [`docs/`](docs/).

## Mejoras pendientes

- Guardar las contraseñas con `password_hash()` en lugar de texto plano.
- Roles de usuario (administrador, médico, recepción).
- Estado de las citas (programada, completada, cancelada).