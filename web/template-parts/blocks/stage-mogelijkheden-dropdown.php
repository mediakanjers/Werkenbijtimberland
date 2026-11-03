<?php
/**
 * Block: Stage - Mogelijkheden dropdown
 *
 * @param array $block The block settings and attributes.
 */

$titel         = get_field( 'stageopt_mog_titel', 'option' );
$tekst         = get_field( 'stageopt_mog_tekst', 'option' );
$mogelijkheden = get_field( 'stageopt_mogelijkheden', 'option' );

if ( ! $mogelijkheden ) {
    return;
}

$wrapper_class = 'mk-stage-dropdown mk-block-spacing';

if ( ! empty( $block['className'] ) ) {
    $wrapper_class .= ' ' . $block['className'];
}
?>
<section class="<?php echo esc_attr( $wrapper_class ); ?>">
    <div class="container">
        <div class="mk-stage-dropdown__grid">
            <div class="mk-stage-dropdown__intro">
                <?php if ( $titel ) : ?>
                    <h2 class="mk-stage-dropdown__title"><?php echo esc_html( $titel ); ?></h2>
                <?php endif; ?>
                <?php if ( $tekst ) : ?>
                    <div class="mk-stage-dropdown__text"><?php echo wp_kses_post( $tekst ); ?></div>
                <?php endif; ?>
            </div>

            <div class="mk-stage-dropdown__list">
                <?php foreach ( $mogelijkheden as $index => $mogelijkheid ) :
                    $item_titel  = $mogelijkheid['titel'];
                    $item_inhoud = $mogelijkheid['inhoud'];
                    $item_id     = 'stage-mogelijkheid-' . $block['id'] . '-' . $index;

                    if ( ! $item_titel ) {
                        continue;
                    }

                    // Alleen een titel, geen uitleg: rustig kaartje met vinkje,
                    // niet uitklapbaar (er valt niets uit te klappen).
                    if ( ! $item_inhoud ) :
                ?>
                    <div class="mk-stage-dropdown__item mk-stage-dropdown__item--tag">
                        <span class="mk-stage-dropdown__tag__icon" aria-hidden="true">
                            <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/images/icons/' . rawurlencode( 'checkmark (2).svg' ) ); ?>" alt="">
                        </span>
                        <span class="mk-stage-dropdown__toggle__title"><?php echo esc_html( $item_titel ); ?></span>
                    </div>
                <?php
                        continue;
                    endif;
                ?>
                    <div class="mk-stage-dropdown__item">
                        <button type="button" class="mk-stage-dropdown__toggle" aria-expanded="false" aria-controls="<?php echo esc_attr( $item_id ); ?>">
                            <span class="mk-stage-dropdown__toggle__title"><?php echo esc_html( $item_titel ); ?></span>
                            <span class="mk-stage-dropdown__arrow">
                                <?php get_template_part( 'template-parts/blocks/partials/arrow-icon', null, array( 'color' => 'blue' ) ); ?>
                            </span>
                        </button>

                        <?php if ( $item_inhoud ) : ?>
                            <div class="mk-stage-dropdown__panel" id="<?php echo esc_attr( $item_id ); ?>">
                                <div class="mk-stage-dropdown__panel__inner">
                                    <div class="mk-stage-dropdown__panel__text"><?php echo wp_kses_post( $item_inhoud ); ?></div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
