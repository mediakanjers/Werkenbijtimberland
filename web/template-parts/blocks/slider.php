<?php
/**
 * Block: Slider (gallerij of merken)
 *
 * @param array $block The block settings and attributes.
 */

$type          = get_field( 'sl_type' ) ?: 'gallerij';
$edge          = get_field( 'sl_edge' ) ?: 'vol';
$background    = get_field( 'sl_background' ) ?: 'wit';
$intro_layout  = get_field( 'sl_intro_layout' ) ?: 'links-rechts';
$intro_ratio   = get_field( 'sl_intro_ratio' ) ?: '60-40';
$title         = get_field( 'sl_title' );
$text          = get_field( 'sl_text' );
$cta_type      = get_field( 'sl_cta_type' ) ?: 'geen';
$cta_button    = get_field( 'sl_cta_button' );
$cta_link_1    = get_field( 'sl_cta_link_1' );
$cta_link_2    = get_field( 'sl_cta_link_2' );

$gallerij_items = 'gallerij' === $type ? get_field( 'sl_gallerij_items' ) : null;
$merk_ids       = 'merken' === $type ? get_field( 'sl_merken' ) : null;

if ( ( 'gallerij' === $type && ! $gallerij_items ) || ( 'merken' === $type && ! $merk_ids ) ) {
    return;
}

$slider_id      = 'slider-' . $block['id'];
$show_intro     = 'geen' !== $intro_layout;
$has_intro_content = $show_intro && ( $title || $text || 'geen' !== $cta_type );

// Padding boven de slider schaalt mee met het aantal intro-elementen dat
// daadwerkelijk getoond wordt (titel/tekst/CTA) — hoe meer content erboven
// staat, hoe meer ademruimte nodig is.
$intro_element_count = (int) (bool) $title + (int) (bool) $text + (int) ( 'geen' !== $cta_type );
$intro_size          = 0 === $intro_element_count ? '' : ( $intro_element_count >= 3 ? 'l' : ( 2 === $intro_element_count ? 'm' : 's' ) );

$wrapper_class  = 'mk-slider mk-block-spacing mk-slider--bg-' . $background . ' mk-slider--' . $type . ' mk-slider--edge-' . $edge;

if ( $has_intro_content ) {
    $wrapper_class .= ' mk-intro-spacing mk-intro-spacing--' . $intro_size;
}

if ( ! empty( $block['className'] ) ) {
    $wrapper_class .= ' ' . $block['className'];
}
?>
<section class="<?php echo esc_attr( $wrapper_class ); ?>">
    <?php if ( $has_intro_content ) : ?>
    <div class="container">
        <div class="mk-slider__intro mk-slider__intro--<?php echo esc_attr( $intro_layout ); ?><?php echo 'links-rechts' === $intro_layout ? ' mk-slider__intro--ratio-' . esc_attr( $intro_ratio ) : ''; ?>">
            <div class="mk-slider__intro__text-col">
                <?php if ( $title ) : ?>
                    <h2 class="mk-slider__title"><?php echo esc_html( $title ); ?></h2>
                <?php endif; ?>

                <?php if ( $text ) : ?>
                    <div class="mk-slider__text"><?php echo wp_kses_post( $text ); ?></div>
                <?php endif; ?>
            </div>

            <?php if ( 'geen' !== $cta_type ) : ?>
            <div class="mk-slider__intro__cta-col">
                <?php if ( 'knop-koper' === $cta_type && $cta_button && ! empty( $cta_button['url'] ) ) : ?>
                    <a class="btn-secondary" href="<?php echo esc_url( $cta_button['url'] ); ?>" target="<?php echo esc_attr( $cta_button['target'] ?: '_self' ); ?>">
                        <?php echo esc_html( $cta_button['title'] ); ?>
                        <?php get_template_part( 'template-parts/blocks/partials/arrow-icon', null, array( 'color' => 'white' ) ); ?>
                    </a>
                <?php elseif ( 'knop-primary' === $cta_type && $cta_button && ! empty( $cta_button['url'] ) ) : ?>
                    <a class="btn-primary" href="<?php echo esc_url( $cta_button['url'] ); ?>" target="<?php echo esc_attr( $cta_button['target'] ?: '_self' ); ?>">
                        <?php echo esc_html( $cta_button['title'] ); ?>
                        <?php get_template_part( 'template-parts/blocks/partials/arrow-icon', null, array( 'color' => 'white' ) ); ?>
                    </a>
                <?php elseif ( 'links' === $cta_type ) : ?>
                    <div class="mk-slider__intro__links">
                        <?php if ( $cta_link_1 && ! empty( $cta_link_1['url'] ) ) : ?>
                            <a class="mk-slider__intro__link" href="<?php echo esc_url( $cta_link_1['url'] ); ?>" target="<?php echo esc_attr( $cta_link_1['target'] ?: '_self' ); ?>">
                                <?php echo esc_html( $cta_link_1['title'] ); ?>
                                <?php get_template_part( 'template-parts/blocks/partials/arrow-icon', null, array( 'color' => 'copper' ) ); ?>
                            </a>
                        <?php endif; ?>

                        <?php if ( $cta_link_2 && ! empty( $cta_link_2['url'] ) ) : ?>
                            <a class="mk-slider__intro__link" href="<?php echo esc_url( $cta_link_2['url'] ); ?>" target="<?php echo esc_attr( $cta_link_2['target'] ?: '_self' ); ?>">
                                <?php echo esc_html( $cta_link_2['title'] ); ?>
                                <?php get_template_part( 'template-parts/blocks/partials/arrow-icon', null, array( 'color' => 'copper' ) ); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if ( 'gallerij' === $type ) : ?>
        <div class="mk-slider__viewport">
            <div class="swiper" id="<?php echo esc_attr( $slider_id ); ?>" data-slider-type="gallerij" data-slider-edge="<?php echo esc_attr( $edge ); ?>"<?php echo 'rechts' === $edge ? ' dir="rtl"' : ''; ?>>
                <div class="swiper-wrapper">
                    <?php foreach ( $gallerij_items as $index => $image ) :
                        // Om en om verticaal/liggend, alleen voor de gallerij-variant.
                        $formaat = 0 === $index % 2 ? 'verticaal' : 'liggend';
                    ?>
                        <div class="swiper-slide mk-slider__gallery-slide mk-slider__gallery-slide--<?php echo esc_attr( $formaat ); ?>" dir="ltr">
                            <a href="<?php echo esc_url( $image['url'] ); ?>" data-fancybox="mk-gallerij" data-caption="<?php echo esc_attr( $image['alt'] ); ?>">
                                <img src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>">
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php else : ?>
        <div class="mk-slider__viewport">
            <div class="swiper" id="<?php echo esc_attr( $slider_id ); ?>" data-slider-type="merken" data-slider-edge="<?php echo esc_attr( $edge ); ?>"<?php echo 'rechts' === $edge ? ' dir="rtl"' : ''; ?>>
                <div class="swiper-wrapper">
                    <?php foreach ( $merk_ids as $merk_id ) :
                        $logo         = get_field( 'merk_slider_logo', $merk_id ) && ! empty( get_field( 'merk_slider_logo', $merk_id )['url'] ) ? get_field( 'merk_slider_logo', $merk_id ) : get_field( 'merk_logo', $merk_id );
                        $achtergrond  = get_field( 'merk_achtergrond', $merk_id );
                        $externe_link = get_field( 'merk_externe_link', $merk_id );

                        if ( ! $logo ) {
                            continue;
                        }

                        $link_url = $externe_link && ! empty( $externe_link['url'] ) ? $externe_link['url'] : '';
                        $tag      = $link_url ? 'a' : 'div';
                    ?>
                        <div class="swiper-slide" dir="ltr">
                            <<?php echo esc_html( $tag ); ?>
                                class="mk-slider__brand-slide"
                                <?php if ( $link_url ) : ?>
                                    href="<?php echo esc_url( $link_url ); ?>"
                                    target="<?php echo esc_attr( $externe_link['target'] ?: '_blank' ); ?>"
                                    rel="noopener noreferrer"
                                <?php endif; ?>
                            >
                                <?php if ( $achtergrond ) : ?>
                                    <img class="mk-slider__brand-slide__bg" src="<?php echo esc_url( $achtergrond['url'] ); ?>" alt="">
                                <?php endif; ?>
                                <img class="mk-slider__brand-slide__logo" src="<?php echo esc_url( $logo['url'] ); ?>" alt="<?php echo esc_attr( $logo['alt'] ?: get_the_title( $merk_id ) ); ?>">
                            </<?php echo esc_html( $tag ); ?>>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="mk-slider__progressbar swiper-scrollbar" data-progressbar-for="<?php echo esc_attr( $slider_id ); ?>"></div>
        </div>
    <?php endif; ?>
</section>
