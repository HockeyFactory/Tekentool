CSS_PATH=./css
LESS_FILE=./less/main.less
JS_PATH=./js

JS_FILES_LIB = ${JS_PATH}/libs/andlog.js \
		${JS_PATH}/libs/jquery.js \
		${JS_PATH}/libs/jquery-ui-1.10.0.custom.js \
		${JS_PATH}/libs/can.custom.js \
		${JS_PATH}/libs/can.fixture.js \
		${JS_PATH}/libs/raphael.js \
		${JS_PATH}/libs/raphael.free-transform.js \
		${JS_PATH}/libs/raphael.export.js \
		${JS_PATH}/libs/rgbcolor.js \
		${JS_PATH}/libs/canvg.js \
		${JS_PATH}/libs/svg_todataurl.js \
		

JS_FILES_APP = ${JS_PATH}/plugins/jquery-jufo.js \
		${JS_PATH}/application/settings.js \
		${JS_PATH}/application/symbol.js \
		${JS_PATH}/application/paper.js \
		${JS_PATH}/application/hockeyfactory.js
		

JS_TARGET_LIB = ${JS_PATH}/libraries
JS_TARGET_APP = ${JS_PATH}/app

BIN_JSHINT = ./node_modules/jshint/bin/jshint
BIN_UGLIFY = ./node_modules/uglify-js/bin/uglifyjs
BIN_LESS = ./node_modules/less/bin/lessc

HF_CONFIG=include/config/hockey.php
VERSION=`git log --oneline --first-parent | wc -l | tr -d ' '`
DATE=$(shell date +"%Y-%m-%d %I:%M:%S%p")
CHECK=√ Done
HR= \
--------------------------------------------------




#
# BUILD
#

build:
	@echo "${HR}"
	@echo "Building Template v${VERSION}..."
	@echo "${HR}"

	@npm install
	@echo "Installing npm modules...                   ${CHECK}"

	@rm -f ${CSS_PATH}/*.css
	@rm -f ${JS_TARGET_LIB}.*
	@rm -f ${JS_TARGET_APP}.*
	@echo "Remove current installed template...        ${CHECK}"
	
	@${BIN_JSHINT} ${JS_PATH}/application/*.js --config ${JS_PATH}/.jshintrc
	@echo "Running JSHint on javascript...             ${CHECK}"

	@sed "s/\var version = [0-9]*;/var version = ${VERSION};/g" ${JS_PATH}/application/settings.js > ${JS_PATH}/application/settings.js.tmp
	@mv ${JS_PATH}/application/settings.js.tmp ${JS_PATH}/application/settings.js

	@cat ${JS_FILES_LIB} > ${JS_TARGET_LIB}.js
	@${BIN_UGLIFY} --compress  unused=false,dead_code=false --output=${JS_TARGET_LIB}.min.js ${JS_TARGET_LIB}.js
	@echo "Compiling and minifying javascript libs...  ${CHECK}"

	@cat ${JS_FILES_APP} > ${JS_TARGET_APP}.js
	@${BIN_UGLIFY} --compress  unused=false,dead_code=false --source-map=${JS_TARGET_APP}.js.map --source-map-root=${JS_TARGET_APP} --source-map-url=${JS_TARGET_APP}.js.map --output=${JS_TARGET_APP}.min.js ${JS_TARGET_APP}.js
	@echo "Compiling and minifying javascript app...    ${CHECK}"


	@${BIN_LESS} --line-numbers=comments ${LESS_FILE} > ${CSS_PATH}/main.css.tmp;
	@sed "s|`pwd`|.|" ${CSS_PATH}/main.css.tmp > ${CSS_PATH}/main.css;
	@rm ${CSS_PATH}/main.css.tmp;

	@make compile --silent
	
	@sed "s/v000/v${VERSION}/g" ${CSS_PATH}/main.css > ${CSS_PATH}/main.css.tmp
	@sed "s/v000/v${VERSION}/g" ${CSS_PATH}/main.min.css > ${CSS_PATH}/main.min.css.tmp
	@sed "s/0000-00-00/${DATE}/g" ${CSS_PATH}/main.css.tmp > ${CSS_PATH}/main.css
	@sed "s/0000-00-00/${DATE}/g" ${CSS_PATH}/main.min.css.tmp > ${CSS_PATH}/main.min.css
	@sed "s/\$version = [0-9]*;/$version = ${VERSION};/g" ${HF_CONFIG} > ${HF_CONFIG}.tmp
	@mv ${HF_CONFIG}.tmp ${HF_CONFIG}

	@rm ${CSS_PATH}/*.tmp
	@echo "Setting version                             ${CHECK}"
	
	@echo "${HR}"
	@echo "Scripts successfully built at ${DATE}.      ${CHECK}"
	@echo "${HR}"

bump:
	@git add -u
	@git commit -m "version bump v${VERSION}"

compile:
	@echo "${HR}"
	@echo "Compiling Template v${VERSION}..."
	@echo "${HR}"

	@rm -f ${CSS_PATH}/*.*
	@echo "Remove current installed template...        ${CHECK}"
	
	@${BIN_LESS} --line-numbers=comments ${LESS_FILE} > ${CSS_PATH}/main.css.tmp;
	@sed "s|`pwd`|.|" ${CSS_PATH}/main.css.tmp > ${CSS_PATH}/main.css;
	@rm ${CSS_PATH}/main.css.tmp;
	@${BIN_LESS} --yui-compress ${LESS_FILE} > ${CSS_PATH}/main.min.css;
	@echo "Compiling LESS with Recess...               ${CHECK}"
	
	@echo "${HR}"
	@echo "Template successfully built at ${DATE}.  ${CHECK}"
	@echo "${HR}"

#
# WATCH LESS FILES
#

watch:
	echo "Watching less files..."; \
	watchr -e "watch('less/(.*)\.less') { system 'make compile' }"

.PHONY: build docs watch gh-pages
