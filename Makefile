PROJECT_DIR=project
PHP=php
COMPOSER=composer
CONSOLE=$(PHP) $(PROJECT_DIR)/bin/console

install:
	$(COMPOSER) install --no-interaction --working-dir=$(PROJECT_DIR)
	$(MAKE) up
	$(CONSOLE) cache:clear
	$(MAKE) jwt-keys
	$(MAKE) db-migrate

up:
	docker compose up -d

down:
	docker compose down

db-reset:
	$(CONSOLE) doctrine:database:drop --if-exists --force
	$(CONSOLE) doctrine:database:create
	$(MAKE) db-migrate

db-migrate:
	$(CONSOLE) doctrine:migrations:migrate --no-interaction --allow-no-migration

jwt-keys:
	mkdir -p $(PROJECT_DIR)/config/jwt
	$(CONSOLE) lexik:jwt:generate-keypair --overwrite

jwt-check:
	if [ ! -f $(PROJECT_DIR)/config/jwt/private.pem ] || [ ! -f $(PROJECT_DIR)/config/jwt/public.pem ]; then $(MAKE) jwt-keys; else echo "JWT keys already present"; fi

test:
	$(MAKE) db-test-ensure
	APP_ENV=test $(CONSOLE) doctrine:database:drop --if-exists --force
	APP_ENV=test $(CONSOLE) doctrine:database:create
	APP_ENV=test $(CONSOLE) doctrine:migrations:migrate --no-interaction --allow-no-migration
	APP_ENV=test $(PHP) $(PROJECT_DIR)/bin/schema-update.php
	APP_ENV=test $(PHP) $(PROJECT_DIR)/vendor/bin/phpunit -c $(PROJECT_DIR)/phpunit.xml

test-coverage:
	APP_ENV=test XDEBUG_MODE=coverage $(PHP) $(PROJECT_DIR)/vendor/bin/phpunit -c $(PROJECT_DIR)/phpunit.xml --coverage-text

lint:
	$(PHP) -l $(PROJECT_DIR)/src/Kernel.php

stan:
	if [ -f $(PROJECT_DIR)/vendor/bin/phpstan ]; then $(PHP) $(PROJECT_DIR)/vendor/bin/phpstan analyse; else echo "phpstan not installed"; fi

cs:
	if [ -f $(PROJECT_DIR)/vendor/bin/php-cs-fixer ]; then $(PHP) $(PROJECT_DIR)/vendor/bin/php-cs-fixer fix --dry-run --diff; else echo "php-cs-fixer not installed"; fi

db-test-ensure:
	docker exec -i marketplace_db psql -U app -tc "SELECT 1 FROM pg_database WHERE datname='app_test'" | grep -q 1 || docker exec -i marketplace_db psql -U app -c "CREATE DATABASE app_test;"

openapi:
	$(MAKE) db-test-ensure
	APP_ENV=test $(CONSOLE) doctrine:database:create
	APP_ENV=test $(CONSOLE) doctrine:migrations:migrate --no-interaction --allow-no-migration
	APP_ENV=test $(PHP) $(PROJECT_DIR)/bin/schema-update.php
	APP_ENV=test $(PHP) $(PROJECT_DIR)/bin/openapi-export.php

ci:
	$(MAKE) up
	if [ ! -f $(PROJECT_DIR)/vendor/autoload.php ]; then $(COMPOSER) install --no-interaction --working-dir=$(PROJECT_DIR); else echo "Composer dependencies already installed"; fi
	$(MAKE) jwt-check
	APP_ENV=test $(CONSOLE) doctrine:database:drop --if-exists --force
	APP_ENV=test $(CONSOLE) doctrine:database:create
	APP_ENV=test $(CONSOLE) doctrine:migrations:migrate --no-interaction --allow-no-migration
	$(MAKE) test

reset-all:
	docker compose down -v
	rm -rf $(PROJECT_DIR)/var/cache/*
	rm -rf $(PROJECT_DIR)/var/log/*
	$(MAKE) up
	$(MAKE) install
