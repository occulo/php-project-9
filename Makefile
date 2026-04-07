PORT ?= 8000

install:
	composer install

lint:
	composer exec --verbose phpcs -- public src

lint-fix:
	composer exec --verbose phpcbf -- public src

start:
	PHP_CLI_SERVER_WORKERS=5 php -S 0.0.0.0:$(PORT) -t public