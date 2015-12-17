all:

tests:
	php vendor/bin/phpunit --coverage-text=php://stdout --coverage-clover=build/coverage.xml --whitelist module/Balance/src
	php vendor/bin/phpcpd module/Balance
	php vendor/bin/parallel-lint module/Balance
	php vendor/bin/phpcs --standard=phpcs.xml --runtime-set ignore_warnings_on_exit true --encoding=utf-8 --extensions=php,phtml --colors module/Balance/src module/Balance/view
	php vendor/bin/php-cs-fixer fix --config-file=php-cs-fixer.php --dry-run --diff
	php vendor/bin/phpmd module/Balance text phpmd.xml

reports:
	php vendor/bin/phploc --log-xml=build/phploc.xml module/Balance/src
