./vendor/bin/sail exec rabbitmq rabbitmqctl add_user sail password
./vendor/bin/sail exec rabbitmq rabbitmqctl set_user_tags sail administrator
./vendor/bin/sail exec rabbitmq rabbitmqctl set_permissions -p / sail ".*" ".*" ".*"


## генерация документации swagger

```bash
php artisan l5-swagger:generate


## Redis -Horizon

```.env
QUEUE_CONNECTION=redis


## RabbitMq

```.env
QUEUE_CONNECTION=rabbitmq