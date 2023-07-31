<?php

use WorDBless\Load;

if (! file_exists( dirname(__DIR__) . '/wordpress/wp-content')) {
	mkdir(dirname(__DIR__) . '/wordpress/wp-content');
}

if (! file_exists(dirname(__DIR__) . '/wordpress/wp-content/themes')) {
	mkdir(dirname(__DIR__) . '/wordpress/wp-content/themes');
}

copy(
    dirname( __DIR__ ) . '/vendor/automattic/wordbless/src/dbless-wpdb.php',
    dirname( __DIR__ ) . '/wordpress/wp-content/db.php'
);

$theme_base_name = basename( dirname( __DIR__ ) );
$src = realpath( dirname( dirname( __DIR__ ) ) . '/' . $theme_base_name );
$dest = dirname( __DIR__ ) . '/wordpress/wp-content/themes/' . $theme_base_name;

if ( is_dir($src) && ! file_exists($dest) ) {
	symlink($src, $dest);
}

require_once dirname( __DIR__ ) . '/vendor/autoload.php';

Load::load();
