<?php
$branch = ( isset( $_GET[ 'branch' ] ) ) ? $_GET[ 'branch' ] : '';

exec( '/usr/bin/sudo /srv/http/addonsdl.sh '.$branch, $output, $exit );

if ( $exit !== 0 ) die( 'failed' );

echo 1;

opcache_reset();
