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

our $COMMENT                    = '';
our @PREFIX                     = ( '/run/media/jago_dragon/WebDev/Servers-linux/SharedLibs/openssl-static' );
our @libdir                     = ( '/run/media/jago_dragon/WebDev/Servers-linux/SharedLibs/openssl-static/lib64' );
our @BINDIR                     = ( '/run/media/jago_dragon/WebDev/Servers-linux/SharedLibs/openssl-static/bin' );
our @BINDIR_REL_PREFIX          = ( 'bin' );
our @LIBDIR                     = ( '/run/media/jago_dragon/WebDev/Servers-linux/SharedLibs/openssl-static/lib64' );
our @LIBDIR_REL_PREFIX          = ( 'lib64' );
our @INCLUDEDIR                 = ( '/run/media/jago_dragon/WebDev/Servers-linux/SharedLibs/openssl-static/include' );
our @INCLUDEDIR_REL_PREFIX      = ( 'include' );
our @APPLINKDIR                 = ( '/run/media/jago_dragon/WebDev/Servers-linux/SharedLibs/openssl-static/include/openssl' );
our @APPLINKDIR_REL_PREFIX      = ( 'include/openssl' );
our @ENGINESDIR                 = ( '/run/media/jago_dragon/WebDev/Servers-linux/SharedLibs/openssl-static/lib64/engines-3' );
our @ENGINESDIR_REL_LIBDIR      = ( 'engines-3' );
our @MODULESDIR                 = ( '/run/media/jago_dragon/WebDev/Servers-linux/SharedLibs/openssl-static/lib64/ossl-modules' );
our @MODULESDIR_REL_LIBDIR      = ( 'ossl-modules' );
our @PKGCONFIGDIR               = ( '/run/media/jago_dragon/WebDev/Servers-linux/SharedLibs/openssl-static/lib64/pkgconfig' );
our @PKGCONFIGDIR_REL_LIBDIR    = ( 'pkgconfig' );
our @CMAKECONFIGDIR             = ( '/run/media/jago_dragon/WebDev/Servers-linux/SharedLibs/openssl-static/lib64/cmake/OpenSSL' );
our @CMAKECONFIGDIR_REL_LIBDIR  = ( 'cmake/OpenSSL' );
our $VERSION                    = '3.5.8';
our @LDLIBS                     =
    # Unix and Windows use space separation, VMS uses comma separation
    $^O eq 'VMS'
    ? split(/ *, */, '-ldl ')
    : split(/ +/, '-ldl ');

1;
