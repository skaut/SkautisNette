# Runs everything inside Docker; no PHP or Composer is needed on the host.
# Local overrides (git-ignored): make.local, e.g. DOCKER_ROOTLESS=1 for rootless Docker.
-include make.local

PHP ?= 8.4
DOCKER_ROOTLESS ?= 0
IMAGE = skautis-nette-dev:$(PHP)

# Rootful Docker (CI) runs as the host user so files stay writable on the host;
# rootless Docker already maps root inside the container to the host user.
ifeq ($(DOCKER_ROOTLESS),1)
DOCKER_USER =
else
DOCKER_USER = --user $(shell id -u):$(shell id -g)
endif

RUN = docker run --rm $(DOCKER_USER) -e HOME=/tmp -e COMPOSER_HOME=/tmp/composer -v "$(CURDIR)":/app -w /app $(IMAGE)

.PHONY: help build install update test stan ci

help: ## Show this help
	@grep -E '^[a-zA-Z_-]+:.*## ' Makefile | awk -F ':[^#]*## ' '{printf "  \033[36m%-10s\033[0m %s\n", $$1, $$2}'
	@echo "  PHP=8.4|8.5 selects the PHP version (default 8.4)"

build: ## Build the development image for the selected PHP version
	docker build --build-arg PHP_VERSION=$(PHP) -t $(IMAGE) docker

install: ## composer install
	$(RUN) composer install --no-interaction --prefer-dist

update: ## composer update
	$(RUN) composer update --no-interaction --prefer-dist

test: ## Nette Tester
	$(RUN) vendor/bin/tester tests -s -C

stan: ## PHPStan
	$(RUN) vendor/bin/phpstan analyse --no-progress --memory-limit=512M

ci: stan test ## Everything CI runs
