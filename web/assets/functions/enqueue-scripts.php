<?php

/**
 * Front-end styles en scripts.
 */
function mediakanjers_enqueue_assets() {
    $theme_uri   = get_stylesheet_directory_uri();
    $theme_path  = get_stylesheet_directory();
    $style_path  = '/dist/css/style-main.css';
    $script_path = '/dist/scripts/scripts.min.js';

    // Google Fonts: Strichpunkt Sans (met preconnect voor performance)
    wp_enqueue_style(
        'mediakanjers-fonts',
        'https://fonts.googleapis.com/css2?family=Strichpunkt+Sans:wght@400..900&display=swap',
        array(),
        null
    );

    wp_enqueue_style(
        'mediakanjers-style',
        $theme_uri . $style_path,
        array( 'mediakanjers-fonts' ),
        file_exists( $theme_path . $style_path ) ? filemtime( $theme_path . $style_path ) : null
    );

    $swiper_path = '/dist/scripts/vendor/swiper-bundle.min.js';
    wp_enqueue_script(
        'swiper',
        $theme_uri . $swiper_path,
        array(),
        file_exists( $theme_path . $swiper_path ) ? filemtime( $theme_path . $swiper_path ) : null,
        true
    );

    $fancybox_path = '/dist/scripts/vendor/fancybox.umd.js';
    wp_enqueue_script(
        'fancybox',
        $theme_uri . $fancybox_path,
        array(),
        file_exists( $theme_path . $fancybox_path ) ? filemtime( $theme_path . $fancybox_path ) : null,
        true
    );

    wp_enqueue_script(
        'mediakanjers-scripts',
        $theme_uri . $script_path,
        array( 'jquery', 'swiper', 'fancybox' ),
        file_exists( $theme_path . $script_path ) ? filemtime( $theme_path . $script_path ) : null,
        true
    );

    wp_localize_script( 'mediakanjers-scripts', 'mediakanjersJobalert', array(
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
    ) );

    wp_localize_script( 'mediakanjers-scripts', 'mediakanjersReistijd', array(
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
    ) );

    wp_localize_script( 'mediakanjers-scripts', 'mediakanjersNieuws', array(
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'mediakanjers_nieuws' ),
    ) );
}
add_action( 'wp_enqueue_scripts', 'mediakanjers_enqueue_assets' );

/**
 * mkbase (parent theme) registreert eigen vendor-scripts/styles (fancybox,
 * swiper, owl-carousel) die dubbel op de dezelfde functionaliteit zitten als
 * onze eigen vendor-bundel, en enqueuet daarnaast dezelfde dist/scripts/scripts.min.js
 * nogmaals onder een andere handle ('mk-main-script'). Dat zorgde voor dubbele
 * script-uitvoering en 404's op vendor-paden die in het child theme niet bestaan.
 * We dequeuen die parent-registraties hier zodat alleen onze eigen bundel draait.
 */
function mediakanjers_dequeue_parent_duplicates() {
    wp_dequeue_script( 'mk-main-script' );
    wp_deregister_script( 'mk-main-script' );

    wp_dequeue_script( 'fancybox-js' );
    wp_deregister_script( 'fancybox-js' );

    wp_dequeue_script( 'swiper-js' );
    wp_deregister_script( 'swiper-js' );

    wp_dequeue_style( 'fancybox-css' );
    wp_deregister_style( 'fancybox-css' );

    wp_dequeue_style( 'swiper-css' );
    wp_deregister_style( 'swiper-css' );
}
add_action( 'wp_enqueue_scripts', 'mediakanjers_dequeue_parent_duplicates', 99 );

/**
 * Preconnect voor Google Fonts, scheelt een DNS/TLS round-trip.
 */
function mediakanjers_resource_hints( $urls, $relation_type ) {
    if ( 'preconnect' === $relation_type ) {
        $urls[] = array(
            'href' => 'https://fonts.gstatic.com',
            'crossorigin',
        );
    }

    return $urls;
}
add_filter( 'wp_resource_hints', 'mediakanjers_resource_hints', 10, 2 );

/**
 * Editor styles: zodat de editors de front-end typografie/kleuren tonen.
 * style-editor.css laat de WYSIWYG-velden (TinyMCE) de tekst precies zo
 * tonen als op de voorkant (lettertype, opsommingen, regelafstand).
 */
function mediakanjers_editor_assets() {
    add_editor_style( array(
        'https://fonts.googleapis.com/css2?family=Strichpunkt+Sans:wght@400..900&display=swap',
        'dist/css/style-main.css',
        'dist/css/style-editor.css',
    ) );
}
add_action( 'admin_init', 'mediakanjers_editor_assets' );

/**
 * Per ACF-WYSIWYG-veld een class op de editor-body (mk-veld-{naam}), zodat
 * style-editor.css velden met een afwijkende voorkant-opmaak kan nadoen.
 */
function mediakanjers_acf_wysiwyg_script() {
    $path = '/assets/admin/acf-wysiwyg.js';

    wp_enqueue_script(
        'mediakanjers-acf-wysiwyg',
        get_stylesheet_directory_uri() . $path,
        array( 'acf-input' ),
        file_exists( get_stylesheet_directory() . $path ) ? filemtime( get_stylesheet_directory() . $path ) : null,
        true
    );
}
add_action( 'acf/input/admin_enqueue_scripts', 'mediakanjers_acf_wysiwyg_script' );

/**
 * Admin-CSS voor de block editor zelf (buiten de content-iframe): maakt het
 * ACF-instellingenpaneel in de zijbalk breder en de velden overzichtelijker,
 * zodat blocks met veel/complexe velden (repeaters, WYSIWYG, tabs) prettig
 * invulbaar blijven. Heeft geen effect op de front-end.
 */
function mediakanjers_block_editor_assets() {
    $theme_uri  = get_stylesheet_directory_uri();
    $theme_path = get_stylesheet_directory();
    $style_path = '/dist/css/style-admin.css';

    wp_enqueue_style(
        'mediakanjers-block-editor',
        $theme_uri . $style_path,
        array(),
        file_exists( $theme_path . $style_path ) ? filemtime( $theme_path . $style_path ) : null
    );
}
add_action( 'enqueue_block_editor_assets', 'mediakanjers_block_editor_assets' );
