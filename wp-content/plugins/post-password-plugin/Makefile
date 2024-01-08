.PHONY: phpcs test

WP_PATH := ../../WordPress

clean:
	rm -rf vendor
	rm -rf meta-block/node_modules

install:
	composer install --no-dev

install-dev:
	composer install
	cd meta-block && yarn install

test:
	WORDPRESS_PATH=$(WP_PATH) vendor/bin/phpunit -v \
		--colors \
		--coverage-clover clover.xml \
		--coverage-html coverage \
		--configuration test/phpunit.xml 

stan:
	vendor/bin/phpstan -vvv \
		analyse \
		--memory-limit 1G \
		-c phpstan.neon 
