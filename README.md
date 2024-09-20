## Levantar el proyecto
Realice los siguientes pasos:

Clonar el repositorio
`git clone https://github.com/pedroguaillas/accountapp.git`

Ingrese a la carpeta
`cd accountapp`

Cargue las librerias
`composer install`

Instalar dependencias de laravel con Docker
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs

Levantar el contenedor
`./vendor/laravel/sail up -d`

Copiar un el archivo .env.example a .env
`cp .env.example .env`

Levantar Sail en Docker
`./vendor/bin/sail up -d`

Correr las migraciones
`./vendor/bin/sail artisan migrate`

Generar la clave de la aplicación
`./vendor/bin/sail artisan key:generate`

Instalar de pendencias de node
`.vendor/bin/sail npm install` 

Levantar el servidor node
`./vendor/bin/sail npm run dev`

