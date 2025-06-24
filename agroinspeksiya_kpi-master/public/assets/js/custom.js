let toggleIcon = document.querySelector('#toggle-icon');
let mainPanel = document.querySelector('#main-panel');
let sidebar = document.querySelector('#sidebar');
toggleIcon.addEventListener('click', () => {
    sidebar.classList.toggle('d-none');
    mainPanel.classList.toggle('main-panel');
    mainPanel.classList.toggle('container-fluid');
})



$(document).ready(function(){

    $('.input-daterange').datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true,
        calendarWeeks : true,
        clearBtn: true,
        disableTouchKeyboard: true
    });

});
