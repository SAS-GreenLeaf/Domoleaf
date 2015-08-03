##
##  pthsem
##  Copyright (c) 1999-2006 Ralf S. Engelschall <rse@engelschall.com>
##  Copyright (c) 2005-2009 Martin Koegler <mkoegler@auto.tuwien.ac.at>
##
##  This file is part of pthsem, a non-preemptive thread scheduling
##  library.
##
##  This library is free software; you can redistribute it and/or
##  modify it under the terms of the GNU Lesser General Public
##  License as published by the Free Software Foundation; either
##  version 2.1 of the License, or (at your option) any later version.
##
##  This library is distributed in the hope that it will be useful,
##  but WITHOUT ANY WARRANTY; without even the implied warranty of
##  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
##  Lesser General Public License for more details.
##
##  You should have received a copy of the GNU Lesser General Public
##  License along with this library; if not, write to the Free Software
##  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307
##  USA, or contact Ralf S. Engelschall <rse@engelschall.com>.
##

AM_CPPFLAGS    =  -I. -I$(srcdir)

pkgconfigdir=$(libdir)/pkgconfig
aclocaldir=$(datadir)/aclocal

nodist_pkgconfig_DATA=pthsem.pc
nodist_aclocal_DATA=pthsem.m4

noinst_PROGRAMS=test_std test_httpd test_misc test_mp test_philo test_sig \
	test_select test_sfio test_uctx 

man1_MANS=pthsem-config.1
man3_MANS=pthsem.3
nodist_include_HEADERS=pthsem.h
bin_SCRIPTS=pthsem-config
lib_LTLIBRARIES = libpthsem.la

if ENABLE_PTHREAD
noinst_PROGRAMS+=test_pthread
man1_MANS+=pthread-config.1
man3_MANS+=pthread.3
nodist_include_HEADERS+=pthread.h
bin_SCRIPTS+=pthread-config
lib_LTLIBRARIES += libpthread.la
endif

SUBDIRS = debian
if ENABLE_COMPAT
SUBDIRS += compat
endif

TESTS=test_std

test_std_SOURCES=test_std.c test_common.c test_common.h
test_std_LDADD=libpthsem.la
test_httpd_SOURCES=test_httpd.c test_common.c
test_httpd_LDADD=libpthsem.la
test_misc_SOURCES=test_misc.c test_common.c
test_misc_LDADD=libpthsem.la
test_mp_SOURCES=test_mp.c test_common.c
test_mp_LDADD=libpthsem.la
test_philo_SOURCES=test_philo.c test_common.c
test_philo_LDADD=libpthsem.la
test_sig_SOURCES=test_sig.c test_common.c
test_sig_LDADD=libpthsem.la
test_select_SOURCES=test_select.c test_common.c
test_select_LDADD=libpthsem.la
test_sfio_SOURCES=test_sfio.c test_common.c
test_sfio_LDADD=libpthsem.la
test_uctx_SOURCES=test_uctx.c test_common.c
test_uctx_LDADD=libpthsem.la
test_pthread_SOURCES=test_pthread.c test_common.c
test_pthread_LDADD=libpthread.la

CLEANFILES=pthsem.m4 pthsem.h pthsem-config pth_p.h pthsem.3 pthsem-config.1
BUILT_SOURCES=pthsem.m4 pthsem.h pthsem-config pth_p.h pthsem.3 pthsem-config.1
EXTRA_DIST=pth.m4 pthsem.spec shtool pth-config.pod  pth.pod  pthread-config.pod  pthread.pod \
	pth-valgrind.patch pth-sem.patch config.param HACKING HISTORY PORTING \
	USERS TESTS rse-pmt.ps pthread.ps ANNOUNCE SUPPORT \
	pth-cond.patch pth-cygwin.patch pth_p.h.in pth-config.1 pth.3 pthread-config.1 pthread.3 \
	libpthsem.sym

pthsem.m4 : pth.m4
	cp $< $@
pthsem.h : pth.h
	cp $< $@
pthsem-config : pth-config
	cp $< $@
pthsem-config.1 : pth-config.1
	cp $< $@
pthsem.3 : pth.3
	cp $< $@

PTHSEM_SRC=pth_debug.c pth_ring.c pth_pqueue.c pth_time.c pth_errno.c pth_mctx.c \
        pth_uctx.c pth_tcb.c pth_sched.c pth_attr.c pth_lib.c pth_event.c \
        pth_data.c pth_clean.c pth_cancel.c pth_msg.c pth_sync.c pth_fork.c \
        pth_util.c pth_high.c pth_syscall.c pth_ext.c pth_compat.c pth_string.c

S           = $(srcdir)/

HSRCS = $(S)pth_compat.c $(S)pth_debug.c $(S)pth_syscall.c $(S)pth_errno.c $(S)pth_ring.c $(S)pth_mctx.c \
        $(S)pth_uctx.c $(S)pth_clean.c $(S)pth_time.c $(S)pth_tcb.c $(S)pth_util.c $(S)pth_pqueue.c $(S)pth_event.c \
        $(S)pth_sched.c $(S)pth_data.c $(S)pth_msg.c $(S)pth_cancel.c $(S)pth_sync.c $(S)pth_attr.c $(S)pth_lib.c \
        $(S)pth_fork.c $(S)pth_high.c $(S)pth_ext.c $(S)pth_string.c $(S)pthread.c

SHTOOL      = $(srcdir)/shtool
SED         = sed
POD2MAN     = pod2man

PTHSEM_VERSION=20:28:0

libpthsem_la_SOURCES=$(PTHSEM_SRC)
libpthsem_la_DEPENDENCIES=libpthsem.sym
libpthsem_la_LDFLAGS=-rpath $(libdir) -version-info $(PTHSEM_VERSION) -export-symbols $(srcdir)/libpthsem.sym

libpthread_la_SOURCES=$(PTHSEM_SRC) pthread.c
libpthread_la_LDFLAGS=-rpath $(libdir) -version-info $(PTHSEM_VERSION)

#   build the private shared header file
pth_p.h: $(S)pth_p.h.in
	$(SHTOOL) scpp -o pth_p.h -t $(S)pth_p.h.in -Dcpp -Cintern -M '==#==' $(HSRCS)

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
$(S)pth.3: $(S)pth.pod
	@BASENAME="pth"; SEC=3; \
	NAME="pthsem"; ONELINE="pthsem Portable Threads"; \
	$(_MANPAGE)
$(S)pthread-config.1: $(S)pthread-config.pod
	@BASENAME="pthread-config"; SEC=1; \
	NAME="pthsem"; ONELINE="POSIX Threading API of pthsem"; \
	$(_MANPAGE)
$(S)pthread.3: $(S)pthread.pod
	@BASENAME="pthread"; SEC=3; \
	NAME="pthsem"; ONELINE="POSIX Threading API of pthsem"; \
	$(_MANPAGE)

test: check

