<?php
/**
 * PHPUnit bootstrap: the core logic has no WordPress dependency.
 */

define( 'ABSPATH', __DIR__ . '/' );
error_reporting( E_ALL );

require dirname( __DIR__ ) . '/includes/class-language.php';
