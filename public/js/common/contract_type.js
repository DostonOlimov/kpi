$(document).ready(function () {
    $('.crop').select2({
        minimumResultsForSearch: Infinity
    });
    // get kod tn ved from corn's id crops_name
    const kodtnved = document.getElementById('measure-type');
    const stateDropdown = document.getElementById('category');

    stateDropdown.addEventListener('change', () => {
        const stateId = stateDropdown.value;

        fetch(`/getmeasure-type/${stateId}`)
            .then(response => response.json())
            .then(data => {
                kodtnved.value = data.code || ''; // Set value to data.code if not null, otherwise set to empty string
            })
            .catch(error => {
                console.error('Error fetching measure type:', error);
            });
    });
});
