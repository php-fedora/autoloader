NAME := php-fedora-autoload
VERSION := $(shell grep '%global github_version' ${NAME}.spec | awk '{print $$3}')

default:
	rm -rf ${NAME}-${VERSION}
	mkdir ${NAME}-${VERSION}
	cp -pr LICENSE *.md composer.json phpunit.xml.dist src tests ${NAME}-${VERSION}
	tar -cvzf $(shell rpm --eval='%{_sourcedir}')/${NAME}-${VERSION}.tar.gz ${NAME}-${VERSION}
	rm -rf ${NAME}-${VERSION}
	rpmbuild -ba ./${NAME}.spec
