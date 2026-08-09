.PHONY: up down restart logs cli shell db-shell activate-theme install

up:
	docker compose up -d

down:
	docker compose down

restart:
	docker compose restart

logs:
	docker compose logs -f wordpress

# Usage: make cli ARGS="plugin list"
cli:
	docker compose run --rm wpcli $(ARGS)

shell:
	docker compose exec wordpress bash

db-shell:
	docker compose exec db mariadb -u$${MYSQL_USER:-wordpress} -p$${MYSQL_PASSWORD:-changeme} $${MYSQL_DATABASE:-wordpress}

install:
	docker compose run --rm wpcli core install \
		--url="http://localhost:$${WORDPRESS_PORT:-8080}" \
		--title="Range Finder Coffee" \
		--admin_user=admin \
		--admin_password=admin \
		--admin_email=admin@example.com

activate-theme:
	docker compose run --rm wpcli theme activate rangefinder-coffee
