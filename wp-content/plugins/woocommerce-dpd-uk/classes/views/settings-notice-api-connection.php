<?php
if ( ! defined( 'ABSPATH' ) ) exit; /* Exit if accessed directly */
$class = 'notice notice-error';
$message = sprintf( __( 'DPD UK API Message: %s', 'woocommerce-dpd-uk' ), $message );
printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
