up:
	docker compose up -d
 
down:
	docker compose down
 
install: up
	make install-wordpress
 
install-wordpress:
	docker compose run --rm wpcli wp core install \
		--url=http://localhost:8080 \
		--title="Range Finder Coffee" \
		--admin_user=admin \
		--admin_password=admin123 \
		--admin_email=admin@example.com \
		--skip-email
 
activate-theme:
	docker compose run --rm wpcli wp theme activate rangefinder-coffee
 
customize-site:
	docker compose run --rm wpcli wp option update blogname "Range Finder Coffee"
	docker compose run --rm wpcli wp option update blogdescription ""
	docker compose run --rm wpcli wp menu create "Primary Menu"
	docker compose run --rm wpcli wp menu item add-post Primary Menu 42
	docker compose run --rm wpcli wp menu location assign Primary Menu primary
 
publish-changes:
	docker compose run --rm wpcli wp post update 1 --post_status=publish
 