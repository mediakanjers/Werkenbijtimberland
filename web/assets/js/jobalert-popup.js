jQuery(document).ready(function ($) {
    var $popup = $('#mk-jobalert-popup');
    var $bedankt = $('#mk-jobalert-bedankt');

    if (!$popup.length) {
        return;
    }

    function openPopup($el) {
        $('.mk-jobalert-popup.is-active').not($el).removeClass('is-active').attr('aria-hidden', 'true');
        $el.addClass('is-active').attr('aria-hidden', 'false');
        $('body').addClass('freeze');
    }

    function closePopup($el) {
        $el.removeClass('is-active').attr('aria-hidden', 'true');
        $('body').removeClass('freeze');
    }

    $(document).on('click', '[data-jobalert-open]', function (e) {
        e.preventDefault();
        openPopup($popup);
    });

    $(document).on('click', '[data-jobalert-close]', function (e) {
        e.preventDefault();
        closePopup($(this).closest('.mk-jobalert-popup'));
    });

    $(document).on('keydown', function (e) {
        var $actief = $('.mk-jobalert-popup.is-active');
        if ('Escape' === e.key && $actief.length) {
            closePopup($actief);
        }
    });

    // Aanmeldveld op het vacature-overzicht: we versturen het adres zelf naar
    // dezelfde MailerLite-koppeling als het formulier in de Job alert-popup
    // (zelfde form_id en beveiligingstoken), en tonen daarna de bedankt-popup.
    // Het formulier in de Job alert-popup zelf blijft zo onaangeroerd.
    $(document).on('submit', '[data-jobalert-inline]', function (e) {
        e.preventDefault();

        var form = this;
        var input = $(form).find('input[type="email"]')[0];
        var $button = $(form).find('button[type="submit"]');
        var mlForm = $popup.find('.mailerlite-form form')[0];

        if (!input || !input.checkValidity()) {
            if (input) {
                input.reportValidity();
            }
            return;
        }

        // Geen MailerLite-formulier gevonden: val terug op de Job alert-popup.
        if (!mlForm || typeof mediakanjersJobalert === 'undefined') {
            openPopup($popup);
            return;
        }

        var data = new FormData(mlForm);
        data.set('form_fields[email]', input.value);

        $button.prop('disabled', true).addClass('is-loading');

        fetch(mediakanjersJobalert.ajaxUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams(data).toString(),
            credentials: 'same-origin',
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Aanmelden mislukt');
                }
                form.reset();
                openPopup($bedankt.length ? $bedankt : $popup);
            })
            .catch(function () {
                // Iets misgegaan: laat de bezoeker het via de Job alert-popup proberen.
                openPopup($popup);
            })
            .finally(function () {
                $button.prop('disabled', false).removeClass('is-loading');
            });
    });
});
