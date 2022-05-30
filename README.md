# diimo
Diimo project

#Install
- composer update
- composer install
- update .env DB(create db in server)
- add smtp config

#Laravel
- php artisan jwt:secret
- php artisan migrate:fresh --seed
- php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"
- php artisan l5-swagger:generate

#FIlE .env
Add Variable L5_SWAGGER_CONST_HOST=http://project.test/api/v1
