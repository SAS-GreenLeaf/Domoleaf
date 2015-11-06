# Get master's version in control file
VERSION_MASTER := $(shell cat domomaster/etc/domoleaf/.domomaster.version)

# Get slave version in control file
VERSION_SLAVE := $(shell cat domoslave/etc/domoleaf/.domoslave.version)

# Get arch
ARCH := $(shell dpkg --print-architecture)

# Master's package name
MASTER_NAME = domomaster_$(VERSION_MASTER)_all.deb

# Slave's package name, different by arch
SLAVE_NAME  = domoslave_$(VERSION_SLAVE)_$(ARCH).deb

# Main rule to call compilation rules and generate deb packages
all: prepare-debian compile packages

debian : prepare-debian compile packages
ubuntu1204: prepare-ubuntu1204 compile packages
ubuntu1404: prepare-ubuntu1404 compile packages

prepare-debian:
	
	@rm -rf domomaster/DEBIAN
	@mkdir domomaster/DEBIAN
	@cp dists/debian/domomaster.control domomaster/DEBIAN/control
	@cp dists/debian/domomaster.preinst domomaster/DEBIAN/preinst
	@cp dists/debian/domomaster.postinst domomaster/DEBIAN/postinst
	@cp dists/debian/domomaster.prerm domomaster/DEBIAN/prerm
	@cp dists/debian/domomaster.postrm domomaster/DEBIAN/postrm
	
	@rm -rf domoslave/DEBIAN
	@mkdir domoslave/DEBIAN
	@cp dists/debian/domoslave.control domoslave/DEBIAN/control
	@cp dists/debian/domoslave.preinst domoslave/DEBIAN/preinst
	@cp dists/debian/domoslave.postinst domoslave/DEBIAN/postinst
	@cp dists/debian/domoslave.prerm domoslave/DEBIAN/prerm
	@cp dists/debian/domoslave.postrm domoslave/DEBIAN/postrm

prepare-ubuntu1204:
	
	@rm -rf domomaster/DEBIAN
	@mkdir domomaster/DEBIAN
	@cp dists/ubuntu/precise/domomaster.control domomaster/DEBIAN/control
	@cp dists/ubuntu/precise/domomaster.preinst domomaster/DEBIAN/preinst
	@cp dists/ubuntu/precise/domomaster.postinst domomaster/DEBIAN/postinst
	@cp dists/ubuntu/precise/domomaster.prerm domomaster/DEBIAN/prerm
	@cp dists/ubuntu/precise/domomaster.postrm domomaster/DEBIAN/postrm
	
	@rm -rf domoslave/DEBIAN
	@mkdir domoslave/DEBIAN
	@cp dists/ubuntu/precise/domoslave.control domoslave/DEBIAN/control
	@cp dists/ubuntu/precise/domoslave.preinst domoslave/DEBIAN/preinst
	@cp dists/ubuntu/precise/domoslave.postinst domoslave/DEBIAN/postinst
	@cp dists/ubuntu/precise/domoslave.prerm domoslave/DEBIAN/prerm
	@cp dists/ubuntu/precise/domoslave.postrm domoslave/DEBIAN/postrm

prepare-ubuntu1404:
	
	@rm -rf domomaster/DEBIAN
	@mkdir domomaster/DEBIAN
	@cp dists/ubuntu/trusty/domomaster.control domomaster/DEBIAN/control
	@cp dists/ubuntu/trusty/domomaster.preinst domomaster/DEBIAN/preinst
	@cp dists/ubuntu/trusty/domomaster.postinst domomaster/DEBIAN/postinst
	@cp dists/ubuntu/trusty/domomaster.prerm domomaster/DEBIAN/prerm
	@cp dists/ubuntu/trusty/domomaster.postrm domomaster/DEBIAN/postrm
	
	@rm -rf domoslave/DEBIAN
	@mkdir domoslave/DEBIAN
	@cp dists/ubuntu/trusty/domoslave.control domoslave/DEBIAN/control
	@cp dists/ubuntu/trusty/domoslave.preinst domoslave/DEBIAN/preinst
	@cp dists/ubuntu/trusty/domoslave.postinst domoslave/DEBIAN/postinst
	@cp dists/ubuntu/trusty/domoslave.prerm domoslave/DEBIAN/prerm
	@cp dists/ubuntu/trusty/domoslave.postrm domoslave/DEBIAN/postrm

# compilation rules
compile:
	@chmod 755 check_compiler domomaster/DEBIAN/* domoslave/DEBIAN/*
	@echo "[ \033[33m..\033[0m ] Compiling for $(ARCH)..."
	@./check_compiler
	@make -C monitor_knx
	@make -C monitor_enocean
	@echo "[ \033[32mok\033[0m ] Done compiling for $(ARCH)"

# Rule to generate debian packages
packages:
	@cp monitor_knx/monitor_knx              domoslave/usr/bin
	@cp monitor_enocean/monitor_enocean      domoslave/usr/bin
	@rm -rf domomaster/etc/domoleaf/www
	@mkdir -p domomaster/etc/domoleaf
	@cp -r www domomaster/etc/domoleaf/
	@sed -i 's/\[version\]/$(VERSION_MASTER)/g' domomaster/DEBIAN/control
	@dpkg-deb --build domomaster > /dev/null
	@sed -i 's/\[version\]/$(VERSION_SLAVE)/g' domoslave/DEBIAN/control
	@sed -i 's/Architecture: any/Architecture: $(ARCH)/g' domoslave/DEBIAN/control # Modify arch
	@dpkg-deb --build domoslave > /dev/null
	@mv domomaster.deb         ../$(MASTER_NAME)
	@mv domoslave.deb          ../$(SLAVE_NAME)

# Clean rules
clean:
	@make -C monitor_knx clean
	@make -C monitor_enocean clean
	@rm -f ../$(MASTER_NAME)
	@rm -f ../$(SLAVE_NAME)
	@rm -rf domomaster/DEBIAN
	@rm -rf domoslave/DEBIAN
	@rm -f override
	@rm -f Packages

.PHONY: all compile packages clean
