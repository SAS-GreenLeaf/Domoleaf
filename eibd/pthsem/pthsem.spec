Summary:        GNU Pth fork with semaphore support
Name:           pthsem
Version:        2.0.8
Release:        1
Epoch:          0
License:        LGPL v2.1 or later
Group:          System/Libraries
URL:            http://www.auto.tuwien.ac.at/~mkoegler/index.php/pth
Source:         pthsem_%{version}.tar.gz
BuildRoot:      %{_tmppath}/%{name}-%{version}-build


%description
This package contains the enhanced version
Pth is a very portable POSIX/ANSI-C based library for Unix platforms
which provides non-preemptive priority-based scheduling for multiple
threads of execution ("multithreading") inside server applications. All
threads run in the same address space of the server application, but
each thread has it's own individual program-counter, run-time stack,
signal mask and errno variable.

%package -n libpthsem20
License:        LGPL v2.1 or later
Summary:        GNU Pth fork with semaphore support
Group:          System/Libraries
Provides:       pthsem
%define library_name libpthsem20
%define debug_package_requires %{library_name} = %{version}-%{release}

%description -n libpthsem20
This package contains the enhanced version
Pth is a very portable POSIX/ANSI-C based library for Unix platforms
which provides non-preemptive priority-based scheduling for multiple
threads of execution ("multithreading") inside server applications. All
threads run in the same address space of the server application, but
each thread has it's own individual program-counter, run-time stack,
signal mask and errno variable.

%package -n libpthsem-devel
License:        LGPL v2.1 or later
Summary:        GNU Pth fork with semaphore support
Group:          System/Libraries
Provides:       pthsem-devel
Requires:       %{library_name} = %{version}

%description -n libpthsem-devel
Development headers and libraries for pthsem (based on GNU pth).

%package compat
License:        LGPL v2.1 or later
Summary:        GNU Pth fork with semaphore support
Group:          System/Libraries
Provides:       libpth-devel pth-devel
Requires:       libpthsem-devel

%description compat
Development headers and libraries for builing GNU pth applications with pthsem.

%prep
%setup -q


%build
%configure --enable-compat
# this is necessary; without it make -j fails
make pth_p.h
%{__make} %{?jobs:-j%jobs}

%check
make test

%install
rm -rf $RPM_BUILD_ROOT
make DESTDIR=$RPM_BUILD_ROOT install
rm -f $RPM_BUILD_ROOT%{_libdir}/*.la


%clean
rm -rf $RPM_BUILD_ROOT


%post   -n %{library_name} -p /sbin/ldconfig

%postun -n %{library_name} -p /sbin/ldconfig


%files -n libpthsem20
%defattr(-,root,root)
%doc ANNOUNCE AUTHORS COPYING HACKING HISTORY INSTALL NEWS PORTING README SUPPORT TESTS THANKS USERS
%{_libdir}/libpth*.so.*

%files -n libpthsem-devel
%defattr(-,root,root)
%{_bindir}/pthsem-config
%doc %{_mandir}/man1/pthsem-config*
%{_includedir}/pthsem.h
%{_datadir}/aclocal/pthsem.m4
%{_libdir}/libpth*.so
%{_libdir}/*.a
%{_libdir}/pkgconfig/pthsem.pc
%doc %{_mandir}/man3/*

%files compat
%defattr(-,root,root)
%{_bindir}/pth-config
%{_includedir}/pth.h
%{_datadir}/aclocal/pth.m4
%doc %{_mandir}/man1/pth-config*

%changelog
* Thu Mar 18 2005 Martin Koegler <mkoegler@auto.tuwien.ac.at> - 0:2.0.4-1
- adapted for pthsem
* Thu Feb 24 2005 Michael Schwendt <mschwendt[AT]users.sf.net> - 0:2.0.4-1
- Update to 2.0.4.
- Remove ancient changelog entries which even pre-date Fedora.

* Tue Dec 14 2004 Michael Schwendt <mschwendt[AT]users.sf.net> - 0:2.0.3-1
- Update to 2.0.3, minor and common spec adjustments + LGPL, %%check,
  use URLs for official GNU companion sites.

* Thu Oct 07 2004 Adrian Reber <adrian@lisas.de> - 0:2.0.2-0.fdr.2
- iconv-ing spec to utf8

* Wed Oct 06 2004 Adrian Reber <adrian@lisas.de> - 0:2.0.2-0.fdr.1
- Update to 2.0.2 and current Fedora guidelines.
- added workaround for make -j problem

* Sat Mar 22 2003 Ville Skyttä <ville.skytta at iki.fi> - 0:2.0.0-0.fdr.1
- Update to 2.0.0 and current Fedora guidelines.
- Exclude %%{_libdir}/*.la

* Fri Feb  7 2003 Ville Skyttä <ville.skytta at iki.fi> - 1.4.1-1.fedora.1
- First Fedora release, based on Ryan Weaver's work.
- Move (most of) docs to main package.

