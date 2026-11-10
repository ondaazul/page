<?php
/**
 * Onda Azul: sirve el sitio estático (index.html) desde WordPress.
 * Las rutas relativas a img/ se reescriben a la carpeta del tema.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

$dir  = __DIR__;
$base = trailingslashit( get_template_directory_uri() );
$path = trim( (string) parse_url( $_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH ), '/' );

// Archivos de texto en la raíz del dominio (llms.txt, sitemap.xml) si existen en el tema.
$raiz = array( 'llms.txt' => 'text/plain', 'sitemap.xml' => 'application/xml' );
if ( isset( $raiz[ $path ] ) && is_file( "$dir/$path" ) ) {
	status_header( 200 );
	header( 'Content-Type: ' . $raiz[ $path ] . '; charset=utf-8' );
	readfile( "$dir/$path" );
	exit;
}

// Cualquier otra URL (páginas viejas del WordPress) va a la portada.
if ( $path !== '' ) {
	wp_safe_redirect( home_url( '/' ), 301 );
	exit;
}

$html = file_get_contents( "$dir/index.html" );
$html = str_replace(
	array( '"img/', 'https://hostelondaazul.com/img/' ),
	array( '"' . $base . 'img/', $base . 'img/' ),
	$html
);

status_header( 200 );
header( 'Content-Type: text/html; charset=utf-8' );
echo $html;
exit;
