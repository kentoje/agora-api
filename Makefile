include bin/make/variables.mk

start:
	@printf "${cyan}";
	@echo "Starting the server"
	@printf "${end}";
	symfony server:start -d
.PHONY: start

stop:
	@printf "${cyan}";
	@echo "Shutting down the server"
	@printf "${end}";
	symfony server:stop
.PHONY: stop

log:
	@printf "${cyan}";
	@echo "Displaying logs"
	@printf "${end}";
	symfony server:log
.PHONY: log

cc:
	@printf "${cyan}"
	@echo "Clearing cache"
	@printf "${end}";
	bin/console cache:clear
.PHONY: cc

test:
	@printf "${cyan}"
	@echo "Running tests"
	@printf "${end}";
	php bin/phpunit
.PHONY: test

test-file:
	@read -p "What file do you want to test? " fileName; \
	php bin/phpunit --filter $$fileName
.PHONY: test-file

router:
	@printf "${cyan}"
	@echo "Displaying routes"
	@printf "${end}";
	bin/console debug:router
.PHONY: router

install:
	@printf "${cyan}"
	@echo "Installing dependencies"
	@printf "${end}";
	php /usr/local/bin/composer.phar install
.PHONY: install

db-create:
	@printf "${cyan}"
	@echo "Creating database"
	@printf "${end}";
	bin/console doctrine:database:create
.PHONY: db-create

db-migration:
	@printf "${cyan}"
	@echo "Launching migrations"
	@printf "${end}";
	bin/console doctrine:migration:migrate
.PHONY: db-migration

db-fixture:
	@printf "${cyan}"
	@echo "Loading fixtures"
	@printf "${end}";
	bin/console doctrine:fixtures:load
.PHONY: db-fixture

db-update:
	@printf "${cyan}"
	@echo "Updating database"
	@printf "${end}";
	make db-migration && make db-fixture
.PHONY: db-update

swagger:
	@printf "${purple}"
	@echo "Update Swagger documentation"
	@printf "${end}";
	vendor/bin/openapi --format json --output ./public/swagger/swagger.json ./swagger/swagger.php src
.PHONY: swagger
