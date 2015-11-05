# Get master's version in control file
VERSION_MASTER := $(shell cat glmaster/etc/domoleaf/.glmaster.version)

# Get slave version in control file
VERSION_SLAVE := $(shell cat glslave/etc/domoleaf/.glslave.version)

# Get arch
ARCH := $(shell dpkg --print-architecture)

# Master's package name
MASTER_NAME = glmaster_$(VERSION_MASTER)_all.deb

# Slave's package name, different by arch
SLAVE_NAME  = glslave_$(VERSION_SLAVE)_$(ARCH).deb

# Main rule to call compilation rules and generate deb packages
all: prepare-debian compile packages

debian : prepare-debian compile packages
ubuntu1204: prepare-ubuntu1204 compile packages
ubuntu1404: prepare-ubuntu1404 compile packages

prepare-debian:
	
	@rm -rf glmaster/DEBIAN
	@mkdir glmaster/DEBIAN
	@cp dists/debian/glmaster.control glmaster/DEBIAN/control
	@cp dists/debian/glmaster.preinst glmaster/DEBIAN/preinst
	@cp dists/debian/glmaster.postinst glmaster/DEBIAN/postinst
	@cp dists/debian/glmaster.prerm glmaster/DEBIAN/prerm
	@cp dists/debian/glmaster.postrm glmaster/DEBIAN/postrm
	
	@rm -rf glslave/DEBIAN
	@mkdir glslave/DEBIAN
	@cp dists/debian/glslave.control glslave/DEBIAN/control
	@cp dists/debian/glslave.preinst glslave/DEBIAN/preinst
	@cp dists/debian/glslave.postinst glslave/DEBIAN/postinst
	@cp dists/debian/glslave.prerm glslave/DEBIAN/prerm
	@cp dists/debian/glslave.postrm glslave/DEBIAN/postrm

prepare-ubuntu1204:
	
	@rm -rf glmaster/DEBIAN
	@mkdir glmaster/DEBIAN
	@cp dists/ubuntu/precise/glmaster.control glmaster/DEBIAN/control
	@cp dists/ubuntu/precise/glmaster.preinst glmaster/DEBIAN/preinst
	@cp dists/ubuntu/precise/glmaster.postinst glmaster/DEBIAN/postinst
	@cp dists/ubuntu/precise/glmaster.prerm glmaster/DEBIAN/prerm
	@cp dists/ubuntu/precise/glmaster.postrm glmaster/DEBIAN/postrm
	
	@rm -rf glslave/DEBIAN
	@mkdir glslave/DEBIAN
	@cp dists/ubuntu/precise/glslave.control glslave/DEBIAN/control
	@cp dists/ubuntu/precise/glslave.preinst glslave/DEBIAN/preinst
	@cp dists/ubuntu/precise/glslave.postinst glslave/DEBIAN/postinst
	@cp dists/ubuntu/precise/glslave.prerm glslave/DEBIAN/prerm
	@cp dists/ubuntu/precise/glslave.postrm glslave/DEBIAN/postrm

prepare-ubuntu1404:
	
	@rm -rf glmaster/DEBIAN
	@mkdir glmaster/DEBIAN
	@cp dists/ubuntu/trusty/glmaster.control glmaster/DEBIAN/control
	@cp dists/ubuntu/trusty/glmaster.preinst glmaster/DEBIAN/preinst
	@cp dists/ubuntu/trusty/glmaster.postinst glmaster/DEBIAN/postinst
	@cp dists/ubuntu/trusty/glmaster.prerm glmaster/DEBIAN/prerm
	@cp dists/ubuntu/trusty/glmaster.postrm glmaster/DEBIAN/postrm
	
	@rm -rf glslave/DEBIAN
	@mkdir glslave/DEBIAN
	@cp dists/ubuntu/trusty/glslave.control glslave/DEBIAN/control
	@cp dists/ubuntu/trusty/glslave.preinst glslave/DEBIAN/preinst
	@cp dists/ubuntu/trusty/glslave.postinst glslave/DEBIAN/postinst
	@cp dists/ubuntu/trusty/glslave.prerm glslave/DEBIAN/prerm
	@cp dists/ubuntu/trusty/glslave.postrm glslave/DEBIAN/postrm

# compilation rules
compile:
	@chmod 755 check_compiler glmaster/DEBIAN/* glslave/DEBIAN/*
	@echo "[ \033[33m..\033[0m ] Compiling for $(ARCH)..."
	@./check_compiler
	@make -C monitor_knx
	@make -C monitor_enocean
	@echo "[ \033[32mok\033[0m ] Done compiling for $(ARCH)"

# Rule to generate debian packages
packages:
	@cp monitor_knx/monitor_knx              glslave/usr/bin
	@cp monitor_enocean/monitor_enocean      glslave/usr/bin
	@rm -rf glmaster/etc/domoleaf/www
	@mkdir -p glmaster/etc/domoleaf
	@cp -r www glmaster/etc/domoleaf/
	@sed -i 's/\[version\]/$(VERSION_MASTER)/g' glmaster/DEBIAN/control
	@dpkg-deb --build glmaster > /dev/null
	@sed -i 's/\[version\]/$(VERSION_SLAVE)/g' glslave/DEBIAN/control
	@sed -i 's/Architecture: any/Architecture: $(ARCH)/g' glslave/DEBIAN/control # Modify arch
	@dpkg-deb --build glslave > /dev/null
	@mv glmaster.deb         ../$(MASTER_NAME)
	@mv glslave.deb          ../$(SLAVE_NAME)

# Clean rules
clean:
	@make -C monitor_knx clean
	@make -C monitor_enocean clean
	@rm -f ../$(MASTER_NAME)
	@rm -f ../$(SLAVE_NAME)
	@rm -rf glmaster/DEBIAN
	@rm -rf glslave/DEBIAN
	@rm -f override
	@rm -f Packages

.PHONY: all compile packages clean
