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

    // Aanmelden voor de job alert verloopt op twee plekken via dezelfde route:
    // het formulier in de Job alert-popup en het aanmeldveld op het
    // vacature-overzicht. We versturen het adres zelf naar de MailerLite-
    // koppeling (form_id + beveiligingstoken uit het popup-formulier) en tonen
    // daarna overal dezelfde bedankt-popup met vinkje.
    function aanmelden(email, $button) {
        var mlForm = $popup.find('.mailerlite-form form')[0];

        // Geen MailerLite-formulier gevonden: val terug op de Job alert-popup.
        if (!mlForm || typeof mediakanjersJobalert === 'undefined') {
            openPopup($popup);
            return;
        }

        var data = new FormData(mlForm);
        data.set('form_fields[email]', email);

        $button.prop('disabled', true).addClass('is-loading');

        return fetch(mediakanjersJobalert.ajaxUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams(data).toString(),
            credentials: 'same-origin',
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Aanmelden mislukt');
                }
                openPopup($bedankt.length ? $bedankt : $popup);
                return true;
            })
            .catch(function () {
                // Iets misgegaan: laat de bezoeker het via de Job alert-popup proberen.
                openPopup($popup);
                return false;
            })
            .finally(function () {
                $button.prop('disabled', false).removeClass('is-loading');
            });
    }

    function geldigAdres(input) {
        if (!input || !input.checkValidity() || !input.value) {
            if (input) {
                input.reportValidity();
            }
            return false;
        }
        return true;
    }

    // Formulier in de Job alert-popup. Luisteren in de capture-fase op
    // document, zodat we vóór het script van de MailerLite-plugin zitten (dat
    // anders zijn eigen melding onderin het formulier toont).
    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!form || !$popup[0].contains(form) || !$(form).closest('.mailerlite-form').length) {
            return;
        }

        e.preventDefault();
        e.stopPropagation();

        var input = form.querySelector('input[type="email"]');
        if (!geldigAdres(input)) {
            return;
        }

        var $button = $(form).find('.mailerlite-subscribe-submit');
        var p = aanmelden(input.value, $button);
        if (p) {
            p.then(function (gelukt) {
                if (gelukt) {
                    input.value = '';
                }
            });
        }
    }, true);

    // Aanmeldveld op het vacature-overzicht.
    $(document).on('submit', '[data-jobalert-inline]', function (e) {
        e.preventDefault();

        var form = this;
        var input = $(form).find('input[type="email"]')[0];
        if (!geldigAdres(input)) {
            return;
        }

        var p = aanmelden(input.value, $(form).find('button[type="submit"]'));
        if (p) {
            p.then(function (gelukt) {
                if (gelukt) {
                    form.reset();
                }
            });
        }
    });
});
