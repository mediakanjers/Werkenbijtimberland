<?php
/**
 * Archive: Vacatures
 */

$sectoren = get_terms( array(
    'taxonomy'   => 'vacature_categorie',
    'hide_empty' => true,
) );

$actieve_sector = isset( $_GET['sector'] ) ? sanitize_text_field( wp_unslash( $_GET['sector'] ) ) : '';

$archief_intro = get_field( 'vacopt_intro', 'option' ) ?: 'Werken bij Timberland is jezelf, samen met je collega\'s dagelijks inzetten voor de verbetering en groei van onze organisatie.';

$os_titel = get_field( 'vacopt_os_titel', 'option' ) ?: 'Open sollicitatie';
$os_tekst = get_field( 'vacopt_os_tekst', 'option' ) ?: '<p>Staat jouw droombaan er nog niet tussen? Solliciteer dan open en laat ons weten wat jij zoekt. We nemen graag contact met je op zodra er een passende vacature beschikbaar is.</p>';
$os_cta   = get_field( 'vacopt_os_cta', 'option' );

// Geen knop-link ingesteld? Dan een mail naar de vaste contactpersoon i.p.v.
// een "#"-link (die sprong naar de bovenkant van de pagina).
if ( empty( $os_cta['url'] ) ) {
    $os_mail = get_field( 'vacopt_contact_email', 'option' );
    $os_cta  = $os_mail ? array(
        'url'    => 'mailto:' . $os_mail . '?subject=' . rawurlencode( 'Open sollicitatie' ),
        'title'  => 'Open sollicitatie versturen',
        'target' => '',
    ) : null;
}

$ja_titel = get_field( 'vacopt_ja_titel', 'option' ) ?: 'Nieuwe vacatures als eerste in je mailbox?';
$ja_tekst = get_field( 'vacopt_ja_tekst', 'option' ) ?: 'Sta jouw vacature er nog niet tussen? Geen zorgen, we groeien snel! Maak een job alert aan en ontvang de nieuwste vacatures bij Timberland Europe B.V. direct in je mailbox. Afmelden kan op elk moment.';

$stages_titel = get_field( 'vacopt_stages_titel', 'option' ) ?: 'Stages & afstuderen';
$stages_tekst = get_field( 'vacopt_stages_tekst', 'option' ) ?: 'Op zoek naar een leerzame stage of afstudeerplek? Bekijk alle stagemogelijkheden bij Timberland en ontdek waar jij het verschil kunt maken.';
$stages_cta   = get_field( 'vacopt_stages_cta', 'option' );

// Geen knop-link ingesteld? Dan naar de pagina "Stages" (slug stages).
if ( empty( $stages_cta['url'] ) ) {
    $stages_pagina = get_page_by_path( 'stages' );
    $stages_cta    = $stages_pagina ? array(
        'url'    => get_permalink( $stages_pagina ),
        'title'  => 'Bekijk alle stages',
        'target' => '',
    ) : null;
}

// Alle vacatures in één keer: filteren en "Bekijk meer" gebeuren direct in de
// browser (vacature-archief.js), zonder AJAX. De begintoestand (sector uit
// ?sector= en de eerste $per_pagina resultaten) zetten we hier al goed.
$per_pagina = 12;

$alle_vacatures = new WP_Query( array(
    'post_type'      => 'vacature',
    'posts_per_page' => -1,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'no_found_rows'  => true,
) );

$vacature_cards = array();
$aantal_match   = 0;

foreach ( $alle_vacatures->posts as $vacature ) {
    $termen       = get_the_terms( $vacature->ID, 'vacature_categorie' );
    $vac_sectoren = ( $termen && ! is_wp_error( $termen ) ) ? wp_list_pluck( $termen, 'slug' ) : array();
    $match        = '' === $actieve_sector || in_array( $actieve_sector, $vac_sectoren, true );

    if ( $match ) {
        $aantal_match++;
    }

    $vacature_cards[] = array(
        'vacature_id' => $vacature->ID,
        'sectoren'    => $vac_sectoren,
        'verborgen'   => ! $match || $aantal_match > $per_pagina,
    );
}

get_header();
?>
<div id="main-content" class="vervolgpagina">
    <section class="mk-vacature-archief">
        <div class="container">
            <nav class="mk-vacature-archief__breadcrumb">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> &gt;
                <span>Vacatures</span>
            </nav>

            <h1 class="mk-vacature-archief__title">Vacatures</h1>
            <div class="mk-vacature-archief__intro"><?php echo wp_kses_post( $archief_intro ); ?></div>

            <?php if ( $sectoren && ! is_wp_error( $sectoren ) ) : ?>
                <div class="mk-vacature-archief__filters">
                    <span class="mk-vacature-archief__filters__label">Snel filteren tussen sectoren:</span>
                    <div class="mk-vacature-archief__filters__pills" data-vacature-filters>
                        <button type="button" class="mk-vacature-archief__pill<?php echo '' === $actieve_sector ? ' is-active' : ''; ?>" data-sector="alle">Alle vacatures</button>
                        <?php foreach ( $sectoren as $sector ) : ?>
                            <button type="button" class="mk-vacature-archief__pill<?php echo $actieve_sector === $sector->slug ? ' is-active' : ''; ?>" data-sector="<?php echo esc_attr( $sector->slug ); ?>"><?php echo esc_html( $sector->name ); ?></button>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <p class="mk-vacature-archief__count" data-vacature-count>Resultaten: <?php echo (int) $aantal_match; ?> vacatures</p>

            <div class="mk-vacature-archief__grid" data-vacature-grid data-per-pagina="<?php echo (int) $per_pagina; ?>" data-sector="<?php echo esc_attr( $actieve_sector ?: 'alle' ); ?>">
                <?php foreach ( $vacature_cards as $card_args ) : ?>
                    <?php get_template_part( 'template-parts/vacature-card', null, $card_args ); ?>
                <?php endforeach; ?>
            </div>

            <p class="mk-vacature-archief__empty<?php echo $aantal_match ? ' is-hidden' : ''; ?>" data-vacature-empty>Er zijn op dit moment geen vacatures gevonden.</p>

            <div class="mk-vacature-archief__load-more<?php echo $aantal_match > $per_pagina ? '' : ' is-hidden'; ?>">
                <button type="button" class="mk-vacature-archief__load-more__btn" data-vacature-load-more>
                    Bekijk meer
                    <?php get_template_part( 'template-parts/blocks/partials/arrow-icon', null, array( 'color' => 'blue' ) ); ?>
                </button>
            </div>
        </div>
    </section>

    <section class="mk-open-sollicitatie mk-block-spacing">
        <div class="container mk-open-sollicitatie__grid">
            <div class="mk-open-sollicitatie__media">
                <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/images/' . rawurlencode( 'CV 2.png' ) ); ?>" alt="">
            </div>
            <div class="mk-open-sollicitatie__content">
                <h2 class="mk-open-sollicitatie__title"><?php echo esc_html( $os_titel ); ?></h2>
                <div class="mk-open-sollicitatie__text"><?php echo wp_kses_post( $os_tekst ); ?></div>
                <?php if ( $os_cta && ! empty( $os_cta['url'] ) ) : ?>
                    <a class="btn-primary" href="<?php echo esc_url( $os_cta['url'] ); ?>" target="<?php echo esc_attr( $os_cta['target'] ?: '_self' ); ?>">
                        <?php echo esc_html( $os_cta['title'] ?: 'Open sollicitatie versturen' ); ?>
                        <?php get_template_part( 'template-parts/blocks/partials/arrow-icon', null, array( 'color' => 'white' ) ); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php get_template_part( 'template-parts/reistijd' ); ?>

    <section class="mk-vacature-cta-rijen mk-block-spacing">
        <div class="container mk-vacature-cta-rijen__grid">
            <div class="mk-vacature-cta-rij">
                <h3 class="mk-vacature-cta-rij__title"><?php echo esc_html( $ja_titel ); ?></h3>
                <div class="mk-vacature-cta-rij__text"><?php echo wp_kses_post( $ja_tekst ); ?></div>
                <?php // Stuurt het adres door naar de MailerLite-aanmelding in de Job alert-popup (jobalert-popup.js). ?>
                <form class="mk-vacature-cta-rij__form" data-jobalert-inline novalidate>
                    <label class="screen-reader-text" for="mk-jobalert-inline-email">Jouw e-mailadres</label>
                    <input id="mk-jobalert-inline-email" type="email" name="email" placeholder="Jouw e-mailadres" autocomplete="email" required>
                    <button type="submit" class="btn-primary">
                        Aanmelden
                        <?php get_template_part( 'template-parts/blocks/partials/arrow-icon', null, array( 'color' => 'white' ) ); ?>
                    </button>
                </form>
            </div>
            <div class="mk-vacature-cta-rij mk-vacature-cta-rij--blauw">
                <h3 class="mk-vacature-cta-rij__title"><?php echo esc_html( $stages_titel ); ?></h3>
                <div class="mk-vacature-cta-rij__text"><?php echo wp_kses_post( $stages_tekst ); ?></div>
                <?php if ( $stages_cta && ! empty( $stages_cta['url'] ) ) : ?>
                    <a class="btn-secondary" href="<?php echo esc_url( $stages_cta['url'] ); ?>" target="<?php echo esc_attr( $stages_cta['target'] ?: '_self' ); ?>">
                        <?php echo esc_html( $stages_cta['title'] ?: 'Bekijk alle stages' ); ?>
                        <?php get_template_part( 'template-parts/blocks/partials/arrow-icon', null, array( 'color' => 'white' ) ); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>
<?php get_footer(); ?>
