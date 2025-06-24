$(document).ready(function() {
    $('input[name="password_confirmation"]').change(function() {
        if ($('input[name="password_confirmation"]').val() !== $('input[name="password"]').val()) {
            swal({
                title: "Parollar bir xil emas",
                type: "warning",
                text: "Tasdiqlovchi parol kiritilgan parolga mos kelmadi",
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Qaytadan tasdiqlash",
                closeOnConfirm: true
            }).then((isConfirm) => {
                $('input[name="password_confirmation"]').val('').focus();
                // Remove 'title' from the line below if it's not needed
                $('input[name="password_confirmation"]').attr('title', '');
            });
        }
    });
});
