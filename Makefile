# Get master's version in control file
VERSION_MASTER := $(shell grep "Version: " glmaster/DEBIAN/control | sed 's/ //g' | cut -d : -f 2)

# Get slave version in control file
VERSION_SLAVE := $(shell grep "Version: " glslave/DEBIAN/control | sed 's/ //g' | cut -d : -f 2)

# Get arch
ARCH := $(shell dpkg --print-architecture)

# Master's package name
MASTER_NAME = glmaster_$(VERSION_MASTER)_all.deb

# Slave's package name, different by arch
SLAVE_NAME        = glslave_$(VERSION_SLAVE)_$(ARCH).deb

# Main rule to call compilation rules and generate deb packages
all: compile packages

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
	@echo $(VERSION_MASTER) > glmaster/etc/domoleaf/.glmaster.version
	@dpkg-deb --build glmaster > /dev/null
	@echo $(VERSION_SLAVE) > glslave/etc/domoleaf/.glslave.version
	@sed -i 's/Architecture: any/Architecture: $(ARCH)/g' glslave/DEBIAN/control # Modify arch
	@dpkg-deb --build glslave > /dev/null
	@sed -i 's/Architecture: $(ARCH)/Architecture: any/g' glslave/DEBIAN/control # Reset arch
	@mv glmaster.deb         ../$(MASTER_NAME)
	@mv glslave.deb          ../$(SLAVE_NAME)

# Clean rules
clean:
	@make -C monitor_knx clean
	@make -C monitor_enocean clean
	@rm -f ../$(MASTER_NAME)
	@rm -f ../$(SLAVE_NAME)
	@rm -f override
	@rm -f Packages

.PHONY: all compile packages clean
