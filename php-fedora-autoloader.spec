#
# Fedora spec file for php-fedora-autoloader
#
# Copyright (c) 2016 Shawn Iwinski <shawn@iwin.ski>
#                    Remi Collet <remi@fedoraproject.org>
#
# License: MIT
# http://opensource.org/licenses/MIT
#
# Please preserve changelog entries
#

%global github_owner     xxxxx
%global github_name      xxxxx
%global github_version   1.0.0
%global github_commit    xxxxx

%global composer_vendor  fedora
%global composer_project autoloader

# "php": ">= 5.3.3"
%global php_min_ver 5.3.3

# Build using "--without tests" to disable tests
%global with_tests 0%{!?_without_tests:1}

%{!?phpdir:  %global phpdir  %{_datadir}/php}

Name:          php-%{composer_vendor}-%{composer_project}
Version:       %{github_version}
Release:       1%{?github_release}%{?dist}
Summary:       Fedora Autoloader

Group:         Development/Libraries
License:       MIT
URL:           https://github.com/%{github_owner}/%{github_name}
#Source0:       %%{url}/archive/%%{github_commit}/%%{name}-%%{github_version}-%%{github_commit}.tar.gz
Source0:       %{name}-%{github_version}.tar.gz

BuildArch:     noarch
# Tests
%if %{with_tests}
## composer.json
BuildRequires: php(language) >= %{php_min_ver}
BuildRequires: php-composer(phpunit/phpunit)
## phpcompatinfo (computed from version 1.0.0)
BuildRequires: php-spl
%endif

# composer.json
Requires:      php(language) >= %{php_min_ver}

# Composer
Provides:      php-composer(%{composer_vendor}/%{composer_project}) = %{version}

%description
%{summary}.


%prep
#%%setup -qn %%{github_name}-%%{github_commit}
%setup


%build
# Empty build section, nothing to build


%install
mkdir -p %{buildroot}%{phpdir}/Fedora/Autoloader/Test
cp -rp src/* %{buildroot}%{phpdir}/Fedora/Autoloader/
cp -rp tests/* %{buildroot}%{phpdir}/Fedora/Autoloader/Test/


%check
%if %{with_tests}
%{_bindir}/phpunit --verbose
%else
: Tests skipped
%endif


%files
%{!?_licensedir:%global license %%doc}
%license LICENSE
%doc *.md
%doc composer.json
%dir %{phpdir}/Fedora
     %{phpdir}/Fedora/autoload.php
     %{phpdir}/Fedora/Autoloader.php
%exclude %{phpdir}/Fedora/Autoloader/Test


%changelog
* Sun Jun 12 2016 Shawn Iwinski <shawn@iwin.ski> - 1.0.0-1
- Initial package
