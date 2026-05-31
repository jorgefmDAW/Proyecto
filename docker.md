# Docker - LaLiga Dev Environment

## Estructura del monorepo

```
tu-proyecto/
├── docker-compose.yml
├── frontend/               ← proyecto Angular
│   └── Dockerfile          ← contenido de frontend.Dockerfile
├── backend/                ← proyecto Symfony
│   └── Dockerfile          ← contenido de backend.Dockerfile
└── scraper/                ← scraper Python
    ├── Dockerfile          ← contenido de scraper.Dockerfile
    ├── main.py             ← punto de entrada del scraper
    └── requirements.txt
```

## Primeros pasos

### 1. Copia los Dockerfiles a sus carpetas
```bash
cp frontend.Dockerfile ./frontend/Dockerfile
cp backend.Dockerfile  ./backend/Dockerfile
cp scraper.Dockerfile  ./scraper/Dockerfile
```

### 2. Levanta el entorno de desarrollo (Angular + Symfony + MariaDB + phpMyAdmin)
```bash
docker compose up --build
```

---

## URLs disponibles

| Servicio    | URL                   |
|-------------|-----------------------|
| Angular     | http://localhost:4200 |
| Symfony     | http://localhost:8000 |
| phpMyAdmin  | http://localhost:8080 |
| MariaDB     | localhost:3306        |

## Credenciales de la base de datos

| Campo    | Valor       |
|----------|-------------|
| Host     | database    |
| Puerto   | 3306        |
| BBDD     | LaLiga      |
| Usuario  | laliga_user |
| Password | laliga_pass |

> En Symfony usa `database` como host (nombre del servicio Docker), no `localhost`.

---

## Scraper Python

El scraper **no arranca automáticamente** con `docker compose up` para no molestar
durante el desarrollo. Tienes tres formas de usarlo:

### Ejecutarlo una vez manualmente
```bash
docker compose run --rm scraper python main.py
```

### Arrancarlo con el cron semanal activo (lunes 3:00 AM)
```bash
docker compose --profile scraper up -d scraper
```

### Ver los logs del scraper
```bash
# Logs del cron en tiempo real
docker compose logs -f scraper

# Fichero de log acumulado (persiste entre reinicios)
docker compose exec scraper cat /app/logs/scraper.log
```

### Cambiar la frecuencia del cron
Edita esta línea en `scraper/Dockerfile`:
```
0 3 * * 1   → cada lunes a las 3:00 AM  (configuración actual)
0 3 * * *   → cada día a las 3:00 AM
0 */6 * * * → cada 6 horas
```
Después haz `docker compose build scraper` para aplicar el cambio.

---

## Comandos útiles

```bash
# Levantar en segundo plano
docker compose up -d

# Ver logs de un servicio
docker compose logs -f backend
docker compose logs -f frontend

# Entrar al contenedor de Symfony
docker compose exec backend bash

# Entrar al contenedor de Angular
docker compose exec frontend sh

# Ejecutar migraciones de Symfony
docker compose exec backend php bin/console doctrine:migrations:migrate

# Parar todo
docker compose down

# Parar y borrar la base de datos (¡cuidado!)
docker compose down -v
```

---

## Para tu compañero

Solo necesita tener **Docker Desktop** instalado, clonar el repo y ejecutar:
```bash
docker compose up --build
```
Mismo entorno, mismas versiones, misma base de datos. Sin instalaciones adicionales.
