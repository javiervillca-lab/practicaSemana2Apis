# SIGVE API

Backend de la aplicación SIGVE construido con Symfony 7.4, API Platform 4.3, Doctrine ORM, PostgreSQL y autenticación mediante JWT.

## Requisitos previos

### Opción A: ejecución con Docker

Instalar en el equipo:

- Docker Engine 20.10 o superior.
- Docker Compose v2, disponible como `docker compose`. El proyecto también mantiene compatibilidad con el comando `docker-compose` usado por el `Makefile`.
- GNU Make.
- Git.

Con esta opción no es necesario instalar PHP, Composer, PostgreSQL ni Nginx directamente en el equipo: estos servicios se ejecutan dentro de los contenedores.

## Instalación y puesta en marcha

1. Clonar el repositorio y entrar en la carpeta del proyecto:

	```bash
	git clone git@github.com:javiervillca-lab/practicaSemana2Apis.git
	cd practicaSemana2Apis
	```

2. Confirmar que Docker está disponible:

	```bash
	docker --version
	docker compose version
	make --version
	```

3. Construir las imágenes del proyecto:

	```bash
	make build
	```

	Este paso crea el contenedor PHP con PHP 8.4, las extensiones requeridas, Composer, Xdebug y las herramientas de desarrollo. También construye el contenedor de Nginx y PostgreSQL.

4. Iniciar los servicios:

	```bash
	make up
	```

	Los servicios iniciados son:

	| Servicio | Contenedor | Dirección |
	| --- | --- | --- |
	| Backend PHP-FPM | `sigve-api-be` | Red interna Docker |
	| Nginx | `sigve-web` | `http://localhost:8008` |
	| PostgreSQL | `sigve-postgres` | `localhost:5439` |
	| Mailcatcher | `sigve-mailer` | `http://localhost:1081` |

5. Instalar las dependencias PHP dentro del contenedor:

	```bash
	make composer-install-nointeraction
	```

6. Crear o actualizar el esquema de la base de datos ejecutando las migraciones:

	```bash
	docker exec --user "$(id -u)" sigve-api-be php bin/console doctrine:migrations:migrate --no-interaction
	```

7. Limpiar la caché de Symfony:

	```bash
	docker exec --user "$(id -u)" sigve-api-be php bin/console cache:clear
	```

La API queda disponible en `http://localhost:8008/api` y la documentación interactiva de API Platform en `http://localhost:8008/api`.

## Ejecución sin Docker

También es posible ejecutar el backend directamente en el equipo. Esta alternativa requiere instalar manualmente PHP, Composer, PostgreSQL y Symfony CLI.

### Requisitos locales

- PHP 8.2 o superior.
- Extensiones PHP `ctype`, `iconv`, `intl`, `pdo_pgsql`, `pgsql` y `zip`.
- Composer 2.
- PostgreSQL 16 o superior.
- Symfony CLI.
- Git.

Comprobar las herramientas instaladas:

```bash
php -v
php -m
composer --version
psql --version
symfony version
```

En Ubuntu o Debian, las extensiones principales pueden instalarse con:

```bash
sudo apt update
sudo apt install php-cli php-intl php-pgsql php-zip postgresql postgresql-contrib unzip
```

Composer y Symfony CLI se instalan siguiendo la documentación oficial de cada herramienta. Verificar que los comandos `composer` y `symfony` estén disponibles en el `PATH`.

### Configurar PostgreSQL local

Crear el usuario y la base de datos que utilizará la aplicación. Desde una cuenta con permisos de administración de PostgreSQL:

```bash
sudo -u postgres psql
```

Ejecutar en la consola de PostgreSQL:

```sql
CREATE USER sigve WITH PASSWORD 'admin';
CREATE DATABASE sigve OWNER sigve;
\q
```

Si PostgreSQL ya está configurado con otro usuario, contraseña, host o puerto, utilizar esos valores en `DATABASE_URL`.

### Instalar y configurar el backend

Desde la carpeta raíz del proyecto:

```bash
composer install
cp .env.dev .env
```

Editar `.env` y cambiar la conexión de base de datos para usar el servidor local. El hostname `db` solo es válido dentro de Docker:

```dotenv
DATABASE_URL="postgresql://sigve:admin@127.0.0.1:5432/sigve?serverVersion=16&charset=utf8"
```

Conservar también en `.env` las variables de JWT. El archivo debe contener, como mínimo:

```dotenv
APP_ENV=dev
APP_SECRET=f758ca33e1d4bcd8263e8a0cc8c1b3f5
DATABASE_URL="postgresql://sigve:admin@127.0.0.1:5432/sigve?serverVersion=16&charset=utf8"
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=302c8632957ffd314146179f377d60daa39bf36a7d2e169fe22f89cbef9cfaa7
```

Las claves `config/jwt/private.pem` y `config/jwt/public.pem` ya forman parte del proyecto. Si no existen, generarlas desde la raíz:

```bash
openssl genrsa -aes256 -passout pass:CAMBIAR_ESTA_FRASE -out config/jwt/private.pem 4096
openssl rsa -pubout -in config/jwt/private.pem -passin pass:CAMBIAR_ESTA_FRASE -out config/jwt/public.pem
```

En ese caso, reemplazar `JWT_PASSPHRASE` por `CAMBIAR_ESTA_FRASE` en `.env.local`.

### Migraciones y ejecución

Aplicar las migraciones y limpiar la caché:

### importante ejecutar la migración
```bash 
php bin/console doctrine:migrations:migrate --no-interaction 
php bin/console cache:clear
```

Iniciar el servidor local de Symfony:

```bash
symfony server:start
```

La API quedará disponible normalmente en `http://127.0.0.1:8000/api`. La dirección exacta también se muestra en la terminal. Para ejecutar el servidor en segundo plano:

```bash
symfony server:start -d
```

Detenerlo con:

```bash
symfony server:stop
```

El servicio Mailcatcher definido en Docker no está disponible en esta modalidad. Si la aplicación necesita capturar correos durante el desarrollo, instalar Mailcatcher localmente o utilizar otro servidor SMTP de pruebas y configurar sus variables de correo.

## Configuración

Symfony carga `.env` y, para el entorno `dev`, `.env.dev`. Para configuraciones locales se recomienda crear `.env` y no modificar los archivos base.

### Base de datos

La configuración de desarrollo utiliza PostgreSQL mediante el servicio Docker `db`:

```dotenv
DATABASE_URL="postgresql://sigve:admin@db:5432/sigve?serverVersion=16&charset=utf8"
```

Los valores definidos en `docker-compose.yml` son:

```text
Usuario:     sigve
Contraseña:  admin
Base de datos: sigve
Puerto host: 5439
Puerto contenedor: 5432
```

Desde el contenedor PHP debe usarse `db:5432`. Desde una herramienta instalada en el equipo debe usarse `localhost:5439`.

### JWT

La autenticación utiliza las claves existentes en `config/jwt/`:

- `config/jwt/private.pem`: clave privada para firmar tokens.
- `config/jwt/public.pem`: clave pública para validar tokens.

Las variables necesarias son:

```dotenv
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=<frase_de_la_clave_privada>
```

Si se generan nuevas claves, actualizar `JWT_PASSPHRASE` con la misma contraseña utilizada durante la generación:

```bash
docker exec -it sigve-api-be sh -lc 'mkdir -p config/jwt && openssl genrsa -aes256 -passout pass:CAMBIAR_ESTA_FRASE -out config/jwt/private.pem 4096 && openssl rsa -pubout -in config/jwt/private.pem -passin pass:CAMBIAR_ESTA_FRASE -out config/jwt/public.pem'
```

La frase usada en el comando debe configurarse en `JWT_PASSPHRASE`. En producción, las claves y la frase deben mantenerse fuera del repositorio.

### CORS

El entorno de desarrollo permite solicitudes desde `localhost` y `127.0.0.1`. Para cambiarlo, definir `CORS_ALLOW_ORIGIN` en `.env.local` con una expresión regular compatible con Nelmio CORS.

## Autenticación y uso de la API

El endpoint público para obtener un token es:

```http
POST http://localhost:8008/api/auth
Content-Type: application/json

{"username":"usuario","password":"contraseña"}
```

Las rutas protegidas deben recibir el token en el encabezado:

```http
Authorization: Bearer <token>
```

La creación de usuarios está habilitada públicamente mediante `POST /api/users`. El resto de los recursos bajo `/api` requiere autenticación.

## Comandos habituales

```bash
# Ver el estado de los contenedores
docker compose ps

# Ver los logs de todos los servicios
docker compose logs -f

# Entrar al contenedor PHP
make bash

# Ejecutar comandos Symfony dentro del contenedor PHP
docker exec --user "$(id -u)" sigve-api-be php bin/console <comando>

# Aplicar correcciones de estilo
make code-style

# Detener los contenedores sin eliminar los datos
make stop

# Reconstruir las imágenes y reiniciar los servicios
make rebuild

# Iniciar el servidor Symfony interno, si se necesita para desarrollo
make webserver-run

# Detener el servidor Symfony interno
make webserver-stop
```

El volumen `sigve-pgsql-data` conserva la información de PostgreSQL aunque los contenedores se detengan. Para eliminar también los datos persistidos:

```bash
docker compose down -v
```

## Verificación rápida

Después de iniciar el proyecto, comprobar que la API responde:

```bash
curl -I http://localhost:8008/api
```

Si la respuesta no es correcta, revisar los logs:

```bash
docker compose logs -f sigve-api-be sigve-web db
```

## Estructura principal

- `src/Entity/`: entidades Doctrine y recursos de la API.
- `src/Repository/`: repositorios de persistencia.
- `src/State/Processor/`: procesadores de operaciones de API Platform.
- `config/`: configuración de Symfony, Doctrine, seguridad y JWT.
- `migrations/`: migraciones de la base de datos.
- `docker/`: Dockerfiles y configuración de PHP y Nginx.
- `docker-compose.yml`: definición de los servicios de desarrollo.
