jQuery(function ($) {
    var modal          = $('#masscie-feedback-modal');
    var form           = $('#masscie-feedback-form');
    var deactivateLink = null;
    var pluginSlug     = MASSCIE_FEEDBACK.plugin_slug;

    // Intercept the Deactivate link for this plugin
    $('#the-list tr[data-slug="' + pluginSlug + '"] .deactivate a').on('click', function (e) {
        e.preventDefault();
        deactivateLink = $(this).attr('href');
        $('#masscie-deactivate-url').val(deactivateLink);
        modal.show();
    });

    function closeModal() {
        modal.hide();
    }

    $('#masscie-feedback-cancel, .masscie-feedback-backdrop').on('click', function () {
        closeModal();
    });

    $('#masscie-feedback-skip').on('click', function () {
        // Just go to the original deactivate URL
        if (deactivateLink) {
            window.location.href = deactivateLink;
        } else {
            closeModal();
        }
    });

    form.on('submit', function (e) {
        e.preventDefault();

        var data = {
            action: 'masscie_deactivation_feedback',
            nonce: MASSCIE_FEEDBACK.nonce,
            reason: form.find('input[name="reason"]:checked').val() || '',
            details: form.find('textarea[name="details"]').val() || '',
            deactivate_url: deactivateLink
        };

        $.post(MASSCIE_FEEDBACK.ajax_url, data, function (response) {
            if (response && response.success && response.data && response.data.deactivate_url) {
                window.location.href = response.data.deactivate_url;
            } else if (deactivateLink) {
                // Fallback: deactivate even if email failed.
                window.location.href = deactivateLink;
            } else {
                closeModal();
            }
        });
    });
});