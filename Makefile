.PHONY: help build run stop restart logs shell clean prune test lint

# Variables
IMAGE_NAME = arnrso/home_arnrso_app
IMAGE_TAG = latest
CONTAINER_NAME = home_arnrso_app
PORT = 8080

help: ## Show this help message
	@echo 'Usage: make [target]'
	@echo ''
	@echo 'Available targets:'
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-20s\033[0m %s\n", $$1, $$2}'

build: ## Build Docker image
	docker build -t $(IMAGE_NAME):$(IMAGE_TAG) .

build-no-cache: ## Build Docker image without cache
	docker build --no-cache -t $(IMAGE_NAME):$(IMAGE_TAG) .

run: ## Run container
	docker run -d \
		--name $(CONTAINER_NAME) \
		-p $(PORT):80 \
		-e APP_ENV=prod \
		$(IMAGE_NAME):$(IMAGE_TAG)
	@echo "Container started on http://localhost:$(PORT)"

run-dev: ## Run container in development mode
	docker run -d \
		--name $(CONTAINER_NAME) \
		-p $(PORT):80 \
		-v $(PWD):/app \
		-e APP_ENV=dev \
		$(IMAGE_NAME):$(IMAGE_TAG)
	@echo "Container started in dev mode on http://localhost:$(PORT)"

stop: ## Stop container
	docker stop $(CONTAINER_NAME) || true
	docker rm $(CONTAINER_NAME) || true

restart: stop run ## Restart container

logs: ## Show container logs
	docker logs -f $(CONTAINER_NAME)

shell: ## Open shell in running container
	docker exec -it $(CONTAINER_NAME) sh

ps: ## Show running containers
	docker ps --filter name=$(CONTAINER_NAME)

clean: stop ## Remove container and image
	docker rmi $(IMAGE_NAME):$(IMAGE_TAG) || true

prune: ## Remove all unused Docker resources
	docker system prune -af

test: ## Run tests locally (without Docker)
	php bin/phpunit

lint: ## Run code quality checks locally
	php -d memory_limit=512M vendor/bin/phpstan analyse
	vendor/bin/ecs check

fix: ## Fix code style issues
	vendor/bin/ecs check --fix

ci: ## Run all CI checks locally (tests + static analysis + code style)
	@echo "Installing AssetMapper dependencies..."
	php bin/console importmap:install
	@echo "Running PHPUnit tests..."
	php bin/phpunit
	@echo "Running PHPStan static analysis..."
	php -d memory_limit=512M vendor/bin/phpstan analyse
	@echo "Running ECS code style check..."
	vendor/bin/ecs check
	@echo "✅ All CI checks passed!"

build-run: build run ## Build and run container

rebuild: clean build run ## Clean, build and run container
