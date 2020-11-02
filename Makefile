.PHONY: test tag

test:
	@./vendor/bin/phpunit

tag:
	@./tag.sh
