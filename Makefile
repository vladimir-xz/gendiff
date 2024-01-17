install:
	composer install
validate:
	composer validate
lint:
	composer exec --verbose phpcs -- --standard=PSR12 src bin
test:
	composer exec --verbose phpunit tests
test-coverage:
	phpunit --log-junit 'reports/unitreport.xml' --coverage-clover 'reports/clover.xml' test/
