# Executables
EXEC_PHP      = php
COMPOSER      = composer
REDIS         = redis-cli
GIT           = git
YARN          = yarn
NPX           = npx

# Alias
SYMFONY       = $(EXEC_PHP) bin/console
SULUADMIN     = $(EXEC_PHP) bin/adminconsole
# if you use Docker you can replace with: "docker-compose exec my_php_container $(EXEC_PHP) bin/console"

# Executables: vendors
PHPUNIT       = ./vendor/bin/phpunit

# Executables: local only
SYMFONY_BIN   = symfony

# Executables: prod only
CERTBOT       = certbot

# Misc
.DEFAULT_GOAL = help
.PHONY        : # Not needed here, but you can put your all your targets to be sure
                # there is no name conflict between your files and your targets.

## —— Harder, Better, Faster, Stronger  ———————————————————————————————————
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

install: composer.lock ## Install vendors according to the current composer.lock file
	@$(COMPOSER) install --no-progress --prefer-dist --optimize-autoloader


## —— Symfony ———————————————————————————————————————————————————————————————
sf: ## List all Symfony commands
	@$(SYMFONY)

cc: ## Clear the cache. DID YOU CLEAR YOUR CACHE????
	@$(SYMFONY) c:c

warmup: ## Warmup the cache
	@$(SYMFONY) cache:warmup

purge: ## Purge cache and logs
	@rm -rf var/cache/* var/logs/*

migration: ## Doctrine Migration
	@$(SYMFONY) doctrine:migration:diff --no-interaction
	@$(SYMFONY) doctrine:migration:migrate --no-interaction

## —— Symfony binary  ————————————————————————————————————————————————————————
cert-install: ## Install the local HTTPS certificates
	@$(SYMFONY_BIN) server:ca:install

serve: ## Serve the application with HTTPS support (add "--no-tls" to disable https)
	@$(SYMFONY_BIN) serve --daemon --port=$(HTTP_PORT)

unserve: ## Stop the webserver
	@$(SYMFONY_BIN) server:stop

## —— Sulu  ————————————————————————————————————————————————————————
sulu-reset: ## Build the DB, control the schema validity, load fixtures and check the migration status
	@$(SYMFONY) d:d:d -f --if-exists
	@$(SYMFONY) doctrine:database:create --if-not-exists
	@$(SYMFONY) doctrine:migration:migrate --no-interaction
	@$(SYMFONY) doctrine:migration:diff --no-interaction --allow-empty-diff
	@$(SULUADMIN) sulu:build dev --no-interaction

sulu-build-admin: ## Downloads the current admin application build from the sulu/skeleton repository.
	@$(SULUADMIN) sulu:admin:download-build --no-interaction

sulu-download-language: ## Downloads the currently approved translations for the given language
	@$(SULUADMIN) sulu:admin:download-language --no-interaction

## —— Yarn / JavaScript —————————————————————————————————————————————————————
dev: ## Rebuild assets for the dev env
	@$(YARN) install --check-files
	@$(YARN) dev

watch: ## Watch files and build assets when needed for the dev env
	@$(YARN) watch

encore: ## Build assets for production
	@$(YARN) prod