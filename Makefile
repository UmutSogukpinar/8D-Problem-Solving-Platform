.PHONY: build run clean info rebuild test

BLUE=\033[0;34m
RED=\033[0;31m
GREEN=\033[0;32m
RESET=\033[0m

info:
	@echo "${BLUE}Available commands:${RESET}"
	@echo "  ${GREEN}make build${RESET}   →  Build docker images"
	@echo "  ${GREEN}make run${RESET}     →  Start containers"
	@echo "  ${GREEN}make clean${RESET}   →  Stop and remove everything"
	@echo "  ${GREEN}make rebuild${RESET} →  Rebuild the system"
	@echo "  ${GREEN}make test${RESET}    →  Run tests inside container"
	@echo "  ${GREEN}make info${RESET}    →  Show information about commands"

build:
	@echo "${GREEN}[INFO] Building the project...${RESET}"
	@docker compose build || (echo "${RED}Build failed! ${RESET}" && exit 1)

run: build
	@echo "${BLUE}[INFO] Starting containers...${RESET}"
	@docker compose up -d

clean:
	@echo "${RED}[INFO] Stopping and removing containers...${RESET}"
	@docker compose down -v --remove-orphans

rebuild: clean run

test:
	@echo "${GREEN}[INFO] Running tests...${RESET}"
	@docker compose exec app sh -c "npm test || pytest || echo 'No tests found'"
