.PHONY:	clean test checkout coverage depend update-depend

SHELL=/bin/bash

all: checkout depend clean coverage
	@echo "Up-to-date"

clean:
	rm -fr dist
	bin/clean.sh

test:
	bin/phpunit.phar test

checkout:
	git pull

coverage:
	bin/phpunit --coverage-html doc/coverage test

depend:
	bin/depend.sh install

update-depend:
	bin/depend.sh update
