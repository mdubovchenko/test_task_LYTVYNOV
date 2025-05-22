build: up composer

up:
	docker compose up -d --build

down:
	docker compose stop

composer:
	docker exec -it weather-php bash -c 'composer install'
