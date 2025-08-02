$(document).ready(function () {
    $('.company-link').on('click', function(e) {
        e.preventDefault();

        var companyId = $(this).data('id');

        $.ajax({
            url: '/get_company_data',
            method: 'GET',
            data: { id: companyId },
            success: function(response) {
                swal({
                    title: response.name,
                    type: 'info',
                    html: `
                               <p><i class="fa fa-id-card text-danger"></i> <strong class="text-success">${labels.inn} :</strong><span class="text-info"> ${response.inn}</span></p>
                               <p><i class="fa fa-user text-danger"></i> <strong class="text-success">${labels.owner} :</strong><span class="text-info"> ${response.ownerName}</span></p>
                               <p><i class="fa fa-phone text-danger"></i> <strong class="text-success">${labels.phone} :</strong><span class="text-info"> ${response.phoneNumber}</span></p>
                               <p><i class="fa fa-globe text-danger"></i> <strong class="text-success">${labels.state} :</strong><span class="text-info"> ${response.stateName}</span></p>
                               <p><i class="fa fa-location-arrow text-danger"></i> <strong class="text-success">${labels.city} :</strong> <span class="text-info">${response.cityName}</span></p>
                               <p><i class="fa fa-map-marker text-danger"></i> <stron class="text-success">${labels.address} :</strong><span class="text-info"> ${response.address}</span></p>
                               `
                });
            }
        });
    });

});
