$("input.dob").datetimepicker({
    format: "dd-mm-yyyy",
    autoclose: 1,
    minView: 2,
    startView:'decade',
    endDate: new Date(),
});

$(document).ready(function(){

    $('.datepicker1').datetimepicker({
        format: "<?php echo getDatepicker(); ?>",
        autoclose: 1,
        minView: 2,
        endDate: new Date(),
    });

    $(".datepicker,.input-group-addon").click(function(){
        var dateend = $('#left_date').val('');

    });

    $(".datepicker").datetimepicker({
        format: "<?php echo getDatepicker(); ?>",
        minView: 2,
        autoclose: 1,
    }).on('changeDate', function (selected) {
        var startDate = new Date(selected.date.valueOf());

        $('.datepicker2').datetimepicker({
            format: "<?php echo getDatepicker(); ?>",
            minView: 2,
            autoclose: 1,

        }).datetimepicker('setStartDate', startDate);
    })
        .on('clearDate', function (selected) {
            $('.datepicker2').datetimepicker('setStartDate', null);
        })

    $('.datepicker2').click(function(){

        var date = $('#join_date').val();
        if(date == '')
        {
            swal('First Select Join Date');
        }
        else{
            $('.datepicker2').datetimepicker({
                format: "<?php echo getDatepicker(); ?>",
                minView: 2,
                autoclose: 1,
            })

        }
    });
});
