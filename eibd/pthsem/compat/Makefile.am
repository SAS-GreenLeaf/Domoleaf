aclocaldir=$(datadir)/aclocal
dist_aclocal_DATA=pth.m4
dist_include_HEADERS=pth.h
bin_SCRIPTS=pth-config
man1_MANS=pth-config.1
EXTRA_DIST=pth-config.pod pth-config.1


SED         = sed
POD2MAN     = pod2man

S           = $(srcdir)/

_MANPAGE = \
	echo "Formatting $${BASENAME}.pod into $${BASENAME}.$${SEC} via $(POD2MAN)"; \
	$(POD2MAN) --section=$${SEC} --center="$${ONELINE}" \
	           --release="$(PACKAGE_VERSION)" --date="$${NAME} $(PACKAGE_VERSION)" $(S)$${BASENAME}.pod |\
	$(SED) -e "s;PTH_VERSION_STR;$(PTH_VERSION_STR);" >$${BASENAME}.$${SEC}

#   build the manual pages
$(S)pth-config.1: $(S)pth-config.pod
	@BASENAME="pth-config"; SEC=1; \
	NAME="pthsem"; ONELINE="pthsem Portable Threads"; \
	$(_MANPAGE)

