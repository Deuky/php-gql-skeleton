build:
	export ENV=build; \
	docker compose build; \
	docker compose run --rm php bash -c "composer install && composer dump-autoload";
	docker compose build;

