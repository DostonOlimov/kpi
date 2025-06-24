$(document).ready(function () {
    // Initialize Select2
    initializeSelect2();

    // Event listener for crop name selection
    $('select.name_of_corn').on('change', function () {
        const $this = $(this);
        handlePreNameDisplay($this);
        updateCornTypeOptions($this);
    });

    // Initial trigger if values are preset
    if ($('select.type_of_corn').attr('val') || $('select.type_of_corn2').attr('val')) {
        const $nameOfCorn = $('select.name_of_corn');
        updateCornTypeOptions($nameOfCorn);
    }

    // Event listener for crop name change to get kodtnved
    const stateDropdown = $('#crops_name');
    const kodtnved = $('#kodtnved');
    stateDropdown.on('change', function () {
        const stateId = $(this).val();
        fetchKodtnved(stateId, kodtnved);
    });
});

function initializeSelect2() {
    $('.states').select2({
        minimumResultsForSearch: Infinity
    });
}

function updateCornTypeOptions($element) {
    const cornId = $element.val();
    const url = $element.attr('url');

    if (!cornId || !url) return;

    // Fetch types based on corn's ID
    fetchData(url, { name_id: cornId }, 'select.type_of_corn');

    // Fetch generation data based on corn's ID
    const generationUrl = getGenerationUrl;
    fetchData(generationUrl, { name_id: cornId }, 'select.type_of_corn2');
}

function fetchData(url, data, targetSelector) {
    $.ajax({
        type: 'GET',
        url: url,
        data: data,
        success: function (response) {
            const $targetMenu = $(targetSelector);
            const customerType = $targetMenu.attr('val');
            $targetMenu.html(response);

            if (customerType) {
                $targetMenu.find(`option[value="${customerType}"]`).attr('selected', 'selected');
            }
        }
    });
}

function fetchKodtnved(stateId, $targetElement) {
    if (!stateId) return;

    fetch(`/getkodtnved/${stateId}`)
        .then(response => response.json())
        .then(data => $targetElement.val(data.code))
        .catch(error => console.error('Error fetching kodtnved:', error));
}

function handlePreNameDisplay($element) {
    const cornId = $element.val();
    const preNameElement = $("#pre_name");

    if (cornId == 21 || cornId == 100) {
        preNameElement.show();
    } else {
        preNameElement.hide();
    }
}
