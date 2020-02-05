install:
	composer install

lint:
	composer run-script phpcs -- --standard=PSR12 src bin tests composer.json

lint-fix:
	composer run-script phpcbf -- --standard=PSR12 src bin tests composer.json

test:
	composer run-script phpunit tests
lt:
	make lint
	make test	