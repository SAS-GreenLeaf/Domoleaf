# Get master's version in control file
VERSION_MASTER := $(shell cat domomaster/etc/domoleaf/.domomaster.version)

# Get slave version in control file
VERSION_SLAVE := $(shell cat domoslave/etc/domoleaf/.domoslave.version)

# Get arch
NATIVE := $(shell dpkg --print-architecture)
ifeq ($(ARCH),arm64)
	ARCH := arm64
endif
ifeq ($(ARCH),armel)
	ARCH := armel
endif
ifeq ($(ARCH),armhf)
	ARCH := armhf
endif
ifeq ($(ARCH),i386)
	ARCH := i386
endif
ifeq ($(ARCH),mips)
	ARCH := mips
endif
ifeq ($(ARCH),mipsel)
	ARCH := mipsel
endif
ifeq ($(ARCH),powerpc)
	ARCH := powerpc
endif
ifeq ($(ARCH),ppc64el)
	ARCH := ppc64el
endif
ifeq ($(ARCH),)
	ARCH := $(NATIVE)
endif

# Master's package name
MASTER_NAME = domomaster_$(VERSION_MASTER)_all.deb

# Slave's package name, different by arch
SLAVE_NAME  = domoslave_$(VERSION_SLAVE)_$(ARCH).deb

# Main rule to call compilation rules and generate deb packages
all: prepare-debian package-master package-slave
full: clean prepare-debian package-master package-slave
master: prepare-debian package-master
slave: prepare-debian package-slave

debian : prepare-debian package-master package-slave
ubuntu1204: prepare-debian prepare-ubuntu1204 package-master package-slave

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
	
	@cp dists/ubuntu/precise/domomaster.control domomaster/DEBIAN/control
	@cp dists/ubuntu/precise/domomaster.postinst domomaster/DEBIAN/postinst
	
	@cp dists/ubuntu/precise/domoslave.control domoslave/DEBIAN/control
	@cp dists/ubuntu/precise/domoslave.postinst domoslave/DEBIAN/postinst

# master
package-master:
	@chmod 755 domomaster/DEBIAN/* gengettext
	@rm -rf domomaster/etc/domoleaf/www
	@mkdir -p domomaster/etc/domoleaf
	@cp -r www domomaster/etc/domoleaf/
	@./gengettext
	@sed -i 's/\[version\]/$(VERSION_MASTER)/g' domomaster/DEBIAN/control
	@dpkg-deb --build domomaster > /dev/null
	@mv domomaster.deb         ../$(MASTER_NAME)
	
# slave
package-slave:
	@chmod 755 check_compiler domoslave/DEBIAN/*
	@echo "[ \033[33m..\033[0m ] Compiling for $(ARCH)..."
	@./check_compiler
	@make ARCH="${ARCH}" -C monitor_knx
	@make ARCH="${ARCH}" -C monitor_enocean
	@echo "[ \033[32mok\033[0m ] Done compiling for $(ARCH)"
	@cp monitor_knx/monitor_knx              domoslave/usr/bin
	@cp monitor_enocean/monitor_enocean      domoslave/usr/bin
	@sed -i 's/\[version\]/$(VERSION_SLAVE)/g' domoslave/DEBIAN/control
	@sed -i 's/Architecture: any/Architecture: $(ARCH)/g' domoslave/DEBIAN/control # Modify arch
	@dpkg-deb --build domoslave > /dev/null
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

.PHONY: all full master slave clean
