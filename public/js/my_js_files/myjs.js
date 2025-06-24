// some validators
    $(document).ready(function () {
        moment().format();
        $('.card-body').removeClass('p-6');
        $('#datatable-1_length label').css('visibility', 'hidden');
        $('#datatable-1_wrapper #datatable-1_info').css('visibility', 'hidden');
        $('#datatable-1_wrapper #datatable-1_paginate').css('visibility', 'hidden');
        $('#example-3_length label,#example-3_wrapper #example-3_info, #example-3_wrapper #example-3_paginate, #example-3_wrapper #example-3_filter').hide();

        $('select, input[type!="password"]').attr("autocomplete", "off").attr("title", "");
        $('input, select').attr("data-pattern-mismatch", "Kerakli shaklda to'ldiring").attr("data-original-title", "Maydonni to'ldiring");
        $('input[type="checkbox"]').attr("data-pattern-mismatch", "").attr("data-value-missing", "");
    })
// js codes for filter
    $(document).ready(function () {
        $('#city').change(function () {
            var selectedCity = $(this).val();

            var currentUrl = window.location.href;
            var url = new URL(currentUrl);

            // Set the new query parameter
            url.searchParams.set('city', selectedCity);

            // Modify the URL and trigger an AJAX request
            var newUrl = url.toString();
            window.history.pushState({ path: newUrl }, '', newUrl);

            $.ajax({
                url:  newUrl,
                method: "GET",
                success: function (response) {
                    window.location.reload(true);
                }
            });
        });

        $('#crop').change(function () {
            var crop = $(this).val();

            var currentUrl = window.location.href;
            var url = new URL(currentUrl);

            // Set the new query parameter
            url.searchParams.set('crop', crop);

            // Modify the URL and trigger an AJAX request
            var newUrl = url.toString();
            window.history.pushState({ path: newUrl }, '', newUrl);

            $.ajax({
                url:  newUrl,
                method: "GET",
                success: function (response) {
                    window.location.reload(true);
                }
            });
        });
        $('#app_type_selector').change(function () {
            var type = $(this).val();

            var currentUrl = window.location.href;
            var url = new URL(currentUrl);

            // Set the new query parameter
            url.searchParams.set('app_type_selector', type);

            // Modify the URL and trigger an AJAX request
            var newUrl = url.toString();
            window.history.pushState({ path: newUrl }, '', newUrl);

            $.ajax({
                url:  newUrl,
                method: "GET",
                success: function (response) {
                    window.location.reload(true);
                }
            });
        });

        $('#year').change(function () {
            var selectedCity = $(this).val();

            var currentUrl = window.location.href;
            var url = new URL(currentUrl);

            // Set the new query parameter
            url.searchParams.set('year', selectedCity);

            // Modify the URL and trigger an AJAX request
            var newUrl = url.toString();
            window.history.pushState({path: newUrl}, '', newUrl);

            $.ajax({
                url: newUrl,
                method: "GET",
                success: function (response) {
                    window.location.reload(true);
                }
            });
        });
    });
