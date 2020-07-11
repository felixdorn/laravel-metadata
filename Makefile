.PHONY: ci

testing:
	./vendor/bin/pest --coverage --min=95
ci:
	./vendor/bin/pest --coverage --min=95 --coverage-clover clover.xml
