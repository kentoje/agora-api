include bin/make/variables.mk

start:
	@printf "${cyan}";
	@echo "Starting the server"
	@printf "${yellow}";
	symfony server:start -d
.PHONY: start

stop:
	@printf "${cyan}";
	@echo "Shutting down the server"
	@printf "${yellow}";
	symfony server:stop
.PHONY: stop

cc:
	@printf "${cyan}"
	@echo "Clearing cache"
	@printf "${yellow}";
	bin/console cache:clear
.PHONY: cc

router:
	@printf "${cyan}"
	@echo "Displaying routes"
	@printf "${yellow}";
	bin/console debug:router
.PHONY: router

install:
	@printf "${cyan}"
	@echo "Installing dependencies"
	@printf "${yellow}";
	composer install
.PHONY: install

db-create:
	@printf "${cyan}"
	@echo "Creating database"
	@printf "${yellow}";
	bin/console doctrine:database:create
.PHONY: db-create

db-migration:
	@printf "${cyan}"
	@echo "Launching migrations"
	@printf "${yellow}";
	bin/console doctrine:migration:migrate
.PHONY: db-migration

db-fixture:
	@printf "${cyan}"
	@echo "Loading fixtures"
	@printf "${yellow}";
	bin/console doctrine:fixtures:load
.PHONY: db-fixture

db-update:
	@printf "${cyan}"
	@echo "Updating database"
	@printf "${yellow}";
	make db-migration && make db-fixture
.PHONY: db-update

swagger:
	@printf "${purple}"
	@echo "Update Swagger documentation"
	@printf "${yellow}";
	vendor/bin/openapi --format json --output ./public/swagger/swagger.json ./swagger/swagger.php src
.PHONY: swagger
