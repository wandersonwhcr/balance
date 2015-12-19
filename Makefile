all:

install: dependencies database

uninstall: dependencies
	php vendor/bin/phinx migrate -t0

tests: dependencies database
	php vendor/bin/phpunit --coverage-text=php://stdout --coverage-clover=build/coverage.xml --whitelist module/Balance/src
	php vendor/bin/phpcpd module/Balance
	php vendor/bin/parallel-lint module/Balance
	php vendor/bin/phpcs --standard=phpcs.xml --runtime-set ignore_warnings_on_exit true --encoding=utf-8 --extensions=php,phtml --colors --ignore=module/Balance/migrations module/Balance
	php vendor/bin/php-cs-fixer fix --config-file=php-cs-fixer.php --dry-run --diff
	php vendor/bin/phpmd module/Balance text phpmd.xml

reports: dependencies
	php vendor/bin/phploc --log-xml=build/phploc.xml module/Balance/src

api:
	apigen generate --source module/Balance/src --destination build/gh-pages/api/latest --template-theme bootstrap --title 'Balance' --tree

clean:
	rm -rf build

# Dependências

dependencies:
	composer install
	bower install

database:
	php vendor/bin/phinx migrate
