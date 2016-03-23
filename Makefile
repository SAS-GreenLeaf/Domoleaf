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

# Main rule to call compilation rules and generate deb packages
all: package
full: clean package

package:
	@echo "[ \033[33m..\033[0m ] Compiling for $(ARCH)..."
	@make ARCH="${ARCH}" -C monitor_knx
	@make ARCH="${ARCH}" -C monitor_enocean
	@echo "[ \033[32mok\033[0m ] Done compiling for $(ARCH)"
	@cp monitor_knx/monitor_knx              domoslave/usr/bin
	@cp monitor_enocean/monitor_enocean      domoslave/usr/bin

# Clean rules
clean:
	@make -C monitor_knx clean
	@make -C monitor_enocean clean
	@rm -f override
	@rm -f Packages

.PHONY: all full master slave clean
