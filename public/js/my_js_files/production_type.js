$(document).ready(function () {
    // Initialize Select2 for crop production and requirements
    initializeSelect2('select.crop_production', translations.placeholderProduction);
    initializeSelect2('select.requirements', translations.placeholderRequired);

    // Event listener for crop production change
    $('body').on('change', '.crop_production', function () {
        handleCropProductionChange($(this));
    });
});

function initializeSelect2(selector, placeholderText) {
    $(selector).select2({
        placeholder: placeholderText,
        minimumResultsForSearch: Infinity,
        language: {
            inputTooShort: function () {
                return translations.interData;
            },
            searching: function () {
                return translations.searching;
            },
            noResults: function () {
                return translations.noResults;
            }
        }
    });
}

function handleCropProductionChange($element) {
    const corn_id = $element.val();
    const url = $element.attr('stateurl');
    // Add your AJAX call or any other logic here
}
