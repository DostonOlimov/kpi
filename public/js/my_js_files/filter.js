function changeDisplay(name) {
    //organization companies change
    var currentUrl = window.location.href;
    var url = new URL(currentUrl);

    // Set the new query parameter
    url.searchParams.set(name, '');

    // Modify the URL and trigger an AJAX request
    var newUrl = url.toString();
    window.history.pushState({
        path: newUrl
    }, '', newUrl);

    $.ajax({
        url: newUrl,
        method: "GET",
        success: function(response) {
            window.location.reload(true);
        }
    });
}
// get region
$(document).ready(function() {

    $('#region').change(function() {
        var selectedRegion = $(this).val();

        var currentUrl = window.location.href;
        var url = new URL(currentUrl);

        // Set the new query parameter
        url.searchParams.set('region', selectedRegion);

        // Modify the URL and trigger an AJAX request
        var newUrl = url.toString();
        window.history.pushState({
            path: newUrl
        }, '', newUrl);

        $.ajax({
            url: newUrl,
            method: "GET",
            success: function(response) {
                window.location.reload(true);
            }
        });
    });
    //organization companies change
    $('#organization').change(function() {
        var selectedRegion = $(this).val();

        var currentUrl = window.location.href;
        var url = new URL(currentUrl);

        // Set the new query parameter
        url.searchParams.set('organization', selectedRegion);

        // Modify the URL and trigger an AJAX request
        var newUrl = url.toString();
        window.history.pushState({
            path: newUrl
        }, '', newUrl);

        $.ajax({
            url: newUrl,
            method: "GET",
            success: function(response) {
                window.location.reload(true);
            }
        });
    });
    //status change
    $('#status').change(function() {
        var selectedRegion = $(this).val();

        var currentUrl = window.location.href;
        var url = new URL(currentUrl);

        // Set the new query parameter
        url.searchParams.set('status', selectedRegion);

        // Modify the URL and trigger an AJAX request
        var newUrl = url.toString();
        window.history.pushState({
            path: newUrl
        }, '', newUrl);

        $.ajax({
            url: newUrl,
            method: "GET",
            success: function(response) {
                window.location.reload(true);
            }
        });
    });
    //decison_status change
    $('#decision_status').change(function() {
        var selectedRegion = $(this).val();

        var currentUrl = window.location.href;
        var url = new URL(currentUrl);

        // Set the new query parameter
        url.searchParams.set('decision_status', selectedRegion);

        // Modify the URL and trigger an AJAX request
        var newUrl = url.toString();
        window.history.pushState({
            path: newUrl
        }, '', newUrl);

        $.ajax({
            url: newUrl,
            method: "GET",
            success: function(response) {
                window.location.reload(true);
            }
        });
    });
    //test_status change
    $('#test_status').change(function() {
        var selectedRegion = $(this).val();

        var currentUrl = window.location.href;
        var url = new URL(currentUrl);

        // Set the new query parameter
        url.searchParams.set('test_status', selectedRegion);

        // Modify the URL and trigger an AJAX request
        var newUrl = url.toString();
        window.history.pushState({
            path: newUrl
        }, '', newUrl);

        $.ajax({
            url: newUrl,
            method: "GET",
            success: function(response) {
                window.location.reload(true);
            }
        });
    });
    //crop names change
    $('#crops_name').change(function() {
        var selectedRegion = $(this).val();

        var currentUrl = window.location.href;
        var url = new URL(currentUrl);

        // Set the new query parameter
        url.searchParams.set('crop', selectedRegion);

        // Modify the URL and trigger an AJAX request
        var newUrl = url.toString();
        window.history.pushState({
            path: newUrl
        }, '', newUrl);

        $.ajax({
            url: newUrl,
            method: "GET",
            success: function(response) {
                window.location.reload(true);
            }
        });
    });
    //crop types change
    $('#type').change(function() {
        var selectedRegion = $(this).val();

        var currentUrl = window.location.href;
        var url = new URL(currentUrl);

        // Set the new query parameter
        url.searchParams.set('type', selectedRegion);

        // Modify the URL and trigger an AJAX request
        var newUrl = url.toString();
        window.history.pushState({
            path: newUrl
        }, '', newUrl);

        $.ajax({
            url: newUrl,
            method: "GET",
            success: function(response) {
                window.location.reload(true);
            }
        });
    });

    //crop generation change
    $('#generation').change(function() {
        var selectedRegion = $(this).val();

        var currentUrl = window.location.href;
        var url = new URL(currentUrl);

        // Set the new query parameter
        url.searchParams.set('generation', selectedRegion);

        // Modify the URL and trigger an AJAX request
        var newUrl = url.toString();
        window.history.pushState({
            path: newUrl
        }, '', newUrl);

        $.ajax({
            url: newUrl,
            method: "GET",
            success: function(response) {
                window.location.reload(true);
            }
        });
    });
});
