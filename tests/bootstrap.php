<?php

copy(
    dirname( __DIR__ ) . '/vendor/automattic/wordbless/src/dbless-wpdb.php',
    dirname( __DIR__ ) . '/wordpress/wp-content/db.php'
);

require_once dirname( __DIR__ ) . '/vendor/autoload.php';

\WorDBless\Load::load();
