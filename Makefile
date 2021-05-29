include .env.theme

branch = master

all: placeholder;

placeholder:
	@echo 'Hey, are you familiar with makefile?' && \
	@echo 'Take a look at project Makefile'

start:
	@npm run watch:dev:gulp

start-webpack:
	@./node_modules/.bin/concurrently "npm run watch:dev:gulp" "npm run watch:dev:webpack"

dev: clean
	@npm run build:dev:gulp
ifeq ($(strip $(JS_BUILD_TOOL)),webpack)
	@npm run build:dev:webpack
endif

prod: clean
	@npm run build:prod:gulp
ifeq ($(strip $(JS_BUILD_TOOL)),webpack)
	npm run build:prod:webpack
endif

clean:
	@rm -rf ./css/sourcemaps ./js/build/sourcemaps

node-refresh:
	@rm -rf node_modules/ && npm install

# GIT
pull:
	@git pull origin $(branch)

push:
	@git push origin $(branch)
