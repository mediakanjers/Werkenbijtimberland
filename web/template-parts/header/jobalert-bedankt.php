<?php
/**
 * Bedankt-popup na aanmelden via het aanmeldveld op het vacature-overzicht.
 * Zelfde opbouw en stijl als de Job alert-popup (jobalert-popup.php).
 */

$afbeelding     = get_field( 'vacopt_ja_afbeelding', 'option' );
$afbeelding_url = $afbeelding ? $afbeelding['url'] : get_stylesheet_directory_uri() . '/assets/images/' . rawurlencode( 'jobalert 4.png' );
$afbeelding_alt = $afbeelding ? $afbeelding['alt'] : '';
?>
<div class="mk-jobalert-popup mk-jobalert-popup--bedankt" id="mk-jobalert-bedankt" aria-hidden="true">
    <div class="mk-jobalert-popup__overlay" data-jobalert-close></div>
    <div class="mk-jobalert-popup__dialog" role="dialog" aria-modal="true" aria-labelledby="mk-jobalert-bedankt-title">
        <button type="button" class="mk-jobalert-popup__close" data-jobalert-close aria-label="Sluiten">
            <span></span>
            <span></span>
        </button>

        <div class="mk-jobalert-popup__media">
            <img src="<?php echo esc_url( $afbeelding_url ); ?>" alt="<?php echo esc_attr( $afbeelding_alt ); ?>">
        </div>

        <div class="mk-jobalert-popup__content">
            <span class="mk-jobalert-popup__vinkje" aria-hidden="true">
                <svg viewBox="0 0 52 52" fill="none">
                    <circle class="mk-jobalert-popup__vinkje__cirkel" cx="26" cy="26" r="24"/>
                    <path class="mk-jobalert-popup__vinkje__check" d="M15 27l7 7 15-16"/>
                </svg>
            </span>

            <h2 class="mk-jobalert-popup__title" id="mk-jobalert-bedankt-title">Bedankt voor je aanmelding!</h2>
            <div class="mk-jobalert-popup__text">
                <p>Je ontvangt zo een e-mail om je aanmelding te bevestigen. Daarna krijg je nieuwe vacatures als eerste in je mailbox.</p>
                <p>Geen mail gezien? Kijk ook even in je spam of ongewenste e-mail.</p>
            </div>

            <button type="button" class="btn-primary" data-jobalert-close>
                Sluiten
            </button>
        </div>
    </div>
</div>
