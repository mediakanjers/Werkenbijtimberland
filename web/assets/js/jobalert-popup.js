jQuery(document).ready(function ($) {
    var $popup = $('#mk-jobalert-popup');

    if (!$popup.length) {
        return;
    }

    function openPopup() {
        $popup.addClass('is-active').attr('aria-hidden', 'false');
        $('body').addClass('freeze');
    }

    function closePopup() {
        $popup.removeClass('is-active').attr('aria-hidden', 'true');
        $('body').removeClass('freeze');
    }

    $(document).on('click', '[data-jobalert-open]', function (e) {
        e.preventDefault();
        openPopup();
    });

    $(document).on('click', '[data-jobalert-close]', function (e) {
        e.preventDefault();
        closePopup();
    });

    // Aanmeldveld op het vacature-overzicht: het adres gaat via het (werkende)
    // MailerLite-formulier in de popup, zodat de bezoeker dezelfde bevestiging
    // in de popup ziet als via de Job alert-knop.
    $(document).on('submit', '[data-jobalert-inline]', function (e) {
        e.preventDefault();

        var input = $(this).find('input[type="email"]')[0];
        if (!input || !input.checkValidity()) {
            if (input) {
                input.reportValidity();
            }
            return;
        }

        var $mlEmail = $popup.find('.mailerlite-form input[type="email"]');
        var $mlSubmit = $popup.find('.mailerlite-subscribe-submit');

        openPopup();

        if ($mlEmail.length && $mlSubmit.length) {
            $mlEmail.val(input.value).trigger('input').trigger('change');
            $mlSubmit[0].click();
            input.value = '';
        }
    });

    $(document).on('keydown', function (e) {
        if ('Escape' === e.key && $popup.hasClass('is-active')) {
            closePopup();
        }
    });
});
