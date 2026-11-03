<?php
/**
 * Block: Sectoren
 *
 * @param array $block The block settings and attributes.
 */

$title       = get_field( 'sec_title' );
$sector_ids  = get_field( 'sec_sectoren' );
$edge        = get_field( 'sec_edge' ) ?: 'vol';

if ( $sector_ids ) {
    $terms = get_terms( array(
        'taxonomy'   => 'vacature_categorie',
        'include'    => $sector_ids,
        'hide_empty' => false,
    ) );
} else {
    $terms = get_terms( array(
        'taxonomy'   => 'vacature_categorie',
        'hide_empty' => false,
    ) );
}

if ( is_wp_error( $terms ) || ! $terms ) {
    return;
}

$archive_url   = get_post_type_archive_link( 'vacature' );
$slider_id     = 'sectoren-' . $block['id'];
$wrapper_class = 'mk-sectoren mk-block-spacing mk-sectoren--edge-' . $edge;

if ( $title ) {
    $wrapper_class .= ' mk-intro-spacing mk-intro-spacing--s';
}

if ( ! empty( $block['className'] ) ) {
    $wrapper_class .= ' ' . $block['className'];
}
?>
<section class="<?php echo esc_attr( $wrapper_class ); ?>">
    <div class="container">
        <?php if ( $title ) : ?>
            <h2 class="mk-sectoren__title"><?php echo esc_html( $title ); ?></h2>
        <?php endif; ?>
    </div>

    <div class="mk-sectoren__viewport">
        <div class="swiper" id="<?php echo esc_attr( $slider_id ); ?>" data-slider-type="sectoren" data-slider-edge="<?php echo esc_attr( $edge ); ?>"<?php echo 'rechts' === $edge ? ' dir="rtl"' : ''; ?>>
            <div class="swiper-wrapper">
                <?php foreach ( $terms as $term ) :
                    $afbeelding = get_field( 'sector_afbeelding', $term );
                    $count      = $term->count;
                    $filter_url = $archive_url ? add_query_arg( 'sector', $term->slug, $archive_url ) : '#';
                ?>
                    <div class="swiper-slide">
                        <a class="mk-sectoren__card" href="<?php echo esc_url( $filter_url ); ?>" dir="ltr">
                            <?php if ( $afbeelding ) : ?>
                                <img class="mk-sectoren__card__image" src="<?php echo esc_url( $afbeelding['url'] ); ?>" alt="<?php echo esc_attr( $afbeelding['alt'] ?: $term->name ); ?>">
                            <?php endif; ?>
                            <span class="mk-sectoren__card__footer">
                                <span class="mk-sectoren__card__name"><?php echo esc_html( $term->name ); ?></span>
                                <span class="mk-sectoren__card__count">
                                    <?php echo esc_html( $count ); ?> vacatures
                                    <?php get_template_part( 'template-parts/blocks/partials/arrow-icon', null, array( 'color' => 'copper' ) ); ?>
                                </span>
                            </span>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="mk-sectoren__progressbar swiper-scrollbar" data-progressbar-for="<?php echo esc_attr( $slider_id ); ?>"></div>
    </div>
</section>
