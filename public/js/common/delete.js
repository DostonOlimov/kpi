$(document).ready(function () {
    $('body').on('click', '.sa-warning', function () {
        var url = $(this).attr('url');
        swal({
            title: translations.title,
            text: translations.text,
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#297FCA",
            confirmButtonText: translations.confirmButtonText,
            cancelButtonText: translations.cancelButtonText,
            closeOnConfirm: false
        }).then((result) => {
            window.location.href = url;

        });
    });
});
