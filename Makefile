setup:
	[ -f .env ] || cp ./backend/.env.example ./backend/.env
	docker compose build
	sleep 3
	docker compose up -d
	sleep 3
	docker compose exec -it php php artisan key:generate
	db-refresh
	echo 'Project setup complete.'

remove:
	docker compose down --rmi all --volumes

rebuild:
	docker compose down
	docker compose build --no-cache

up:
	docker compose up -d

down:
	docker compose down

ps:
	docker compose ps

db-refresh:
	docker compose exec -it php php artisan migrate:fresh
	docker compose exec -it php php artisan db:seed

composer-install:
	docker compose exec -it php composer install

	