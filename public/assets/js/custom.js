
//my custom js dsfsalfafjlsadjfaslkfdj

(function ($) {

    "use strict";

    // ______________ PAGE LOADING

    $(window).on("load", function (e) {

        $("#list-date-filter input.from, #list-date-filter input.till").datetimepicker({
            format: "dd-mm-yyyy",
            autoclose: 1,
            minView: 2,
            startView: 'decade',
            endDate: new Date(),
        });

        $('iframe').attr('frameBorder', '0');

        // capitalizing text of the element with class "text-capitalize"
        if ($('.text-capitalize').length) {
            $('.text-capitalize').each(function (index) {
                var t = $(this).text().trim();
                $(this).text(capitalize(t));
            });
        }


        if (localStorage.activeTabPane) {
            $('.tab-pane#' + localStorage.activeTabPane).addClass('active');
            $('.sidetab-menu li a[href="#' + localStorage.activeTabPane + '"]').addClass('active');
        }

        $('body').on('click', '#list-date-filter .show-date', function () {
            $('.date').animate({
                opacity: 1,
                marginLeft: '1%'
            }, 300, () => {
                $(this).removeClass('show-date').addClass('hide-date');
                $(this).find('i').addClass('fa-angle-left').removeClass('fa-angle-right');
            });
        });

        $('body').on('click', '#list-date-filter .hide-date', function () {
            $('.date').animate({
                opacity: 0,
                marginLeft: '-200%'
            }, 300, () => {
                $(this).removeClass('hide-date').addClass('show-date');
                $(this).find('i').removeClass('fa-angle-left').addClass('fa-angle-right');
            });
        });

        $('.amount').each(function (index) {
            $(this).text(amountMake($(this).text()));
        });

        $('.print-table-button').on('click', function () {
            var table = $('#' + $(this).attr('table'));

            table.print({
                NoPrintSelector: '.no-print',
                title: '',
                prepend: 'SOME text'
            });
        });

        $('body').on('click', '#cancel-date-filter', function () {
            var form = $(this).closest('form');
            form.find('input').removeAttr('required').val('');
            form.submit();
        });

        $('#list-date-filter input').on('change', function () {
            var button = $(this).closest('form').find('button');
            if (button.is('#cancel-date-filter')) {
                button.removeAttr('id').attr('type', 'submit').text('Filtrlash');
            }
        });
    });

    function capitalize(text) {
        var words = text.trim().split(' ');
        for (var i = 0; i < words.length; i++) {
            if (words[i]) {
                words[i] = words[i][0].toUpperCase() + words[i].substring(1).toLowerCase();
            }
        }
        return words.join(' ');
    }
//    my costum js codes


})(jQuery);

