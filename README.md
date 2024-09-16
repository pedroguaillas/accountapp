## Levantar el proyecto
Realice los siguientes pasos:

Clone el repositorio
`git clone https://github.com/pedroguaillas/accountapp.git`

Ingrese a la carpeta
`cd accountapp`

Cargue las librerias
`composer install`

Levantar el contenedor
`./vendor/laravel/sail up -d

1. clonamos en  ubuntu https://github.com/pedroguaillas/accountapp.git
2. ejecutamos para isntlaar dependencias https://laravel.com/docs/11.x/sail#installing-composer-dependencies-for-existing-projects
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs
3. cp .env.example .env
4. ./vendor/bin/sail up -d
5. ./vendor/bin/sail artisan migrate
6. ./vendor/bin/sail artisan key:generate
7. instalar de pendencias node .vendor/bin/sail npm install 
8. lwvantar el servidor node ./vendor/bin/sail npm run dev

