package OpenSSL::safe::installdata;

use strict;
use warnings;
use Exporter;
our @ISA = qw(Exporter);
our @EXPORT = qw(
    @PREFIX
    @libdir
    @BINDIR @BINDIR_REL_PREFIX
    @LIBDIR @LIBDIR_REL_PREFIX
    @INCLUDEDIR @INCLUDEDIR_REL_PREFIX
    @APPLINKDIR @APPLINKDIR_REL_PREFIX
    @ENGINESDIR @ENGINESDIR_REL_LIBDIR
    @MODULESDIR @MODULESDIR_REL_LIBDIR
    @PKGCONFIGDIR @PKGCONFIGDIR_REL_LIBDIR
    @CMAKECONFIGDIR @CMAKECONFIGDIR_REL_LIBDIR
    $COMMENT $VERSION @LDLIBS
);

our $COMMENT                    = 'This file should be used when building against this OpenSSL build, and should never be installed';
our @PREFIX                     = ( '/run/media/jago_dragon/WebDev/Servers-linux/SharedLibs/openssl-3.5.8' );
our @libdir                     = ( '' );
our @BINDIR                     = ( '/run/media/jago_dragon/WebDev/Servers-linux/SharedLibs/openssl-3.5.8/apps' );
our @BINDIR_REL_PREFIX          = ( 'apps' );
our @LIBDIR                     = ( '/run/media/jago_dragon/WebDev/Servers-linux/SharedLibs/openssl-3.5.8' );
our @LIBDIR_REL_PREFIX          = ( '' );
our @INCLUDEDIR                 = ( '/run/media/jago_dragon/WebDev/Servers-linux/SharedLibs/openssl-3.5.8/include', '/run/media/jago_dragon/WebDev/Servers-linux/SharedLibs/openssl-3.5.8/include' );
our @INCLUDEDIR_REL_PREFIX      = ( 'include', './include' );
our @APPLINKDIR                 = ( '/run/media/jago_dragon/WebDev/Servers-linux/SharedLibs/openssl-3.5.8/ms' );
our @APPLINKDIR_REL_PREFIX      = ( 'ms' );
our @ENGINESDIR                 = ( '/run/media/jago_dragon/WebDev/Servers-linux/SharedLibs/openssl-3.5.8/engines' );
our @ENGINESDIR_REL_LIBDIR      = ( 'engines' );
our @MODULESDIR                 = ( '/run/media/jago_dragon/WebDev/Servers-linux/SharedLibs/openssl-3.5.8/providers' );
our @MODULESDIR_REL_LIBDIR      = ( 'providers' );
our @PKGCONFIGDIR               = ( '/run/media/jago_dragon/WebDev/Servers-linux/SharedLibs/openssl-3.5.8' );
our @PKGCONFIGDIR_REL_LIBDIR    = ( '.' );
our @CMAKECONFIGDIR             = ( '/run/media/jago_dragon/WebDev/Servers-linux/SharedLibs/openssl-3.5.8' );
our @CMAKECONFIGDIR_REL_LIBDIR  = ( '.' );
our $VERSION                    = '3.5.8';
our @LDLIBS                     =
    # Unix and Windows use space separation, VMS uses comma separation
    $^O eq 'VMS'
    ? split(/ *, */, '-ldl ')
    : split(/ +/, '-ldl ');

1;
