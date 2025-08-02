$(document).ready(function() {
    $('select.owner_search').select2({
        ajax: {
            url: '/organization/search_by_name',
            delay: 300,
            dataType: 'json',
            data: function(params) {
                return {
                    search: params.term
                }
            },
            processResults: function(data) {
                data = data.map((name, index) => {
                    return {
                        id: name.id,
                        text: capitalize(name.name + (name.name ? ' - STiR:' + name
                            .inn : ''))
                    }
                });
                return {
                    results: data
                }
            }
        },
        language: {
            inputTooShort: function() {
                return translations.inputTooShort;
            },
            searching: function() {
                return translations.searching;
            },
            noResults: function() {
                return translations.noResults;
            },
            errorLoading: function() {
                return translations.errorLoading;
            }
        },
        placeholder: translations.placeholder,
        minimumInputLength: 2
    })
    $('select.owner_search2').select2({
        ajax: {
            url: '/prepared/search_by_name',
            delay: 300,
            dataType: 'json',
            data: function(params) {
                return {
                    search: params.term
                }
            },
            processResults: function(data) {
                data = data.map((name, index) => {
                    return {
                        id: name.id,
                        text: capitalize(name.name)
                    }
                });
                return {
                    results: data
                }
            }
        },
        language: {
            inputTooShort: function() {
                return translations.inputTooShort;
            },
            searching: function() {
                return translations.searching;
            },
            noResults: function() {
                return translations.noResults;
            },
            errorLoading: function() {
                return translations.errorLoading;
            }
        },
        placeholder: translations.placeholder,
        minimumInputLength: 2
    })

    function capitalize(text) {
        var words = text.split(' ');
        for (var i = 0; i < words.length; i++) {
            if (words[i][0] == null) {
                continue;
            } else {
                words[i] = words[i][0].toUpperCase() + words[i].substring(1).toLowerCase();
            }

        }
        return words.join(' ');
    }
});
