function disableButton() {
    var button = document.getElementById('submitter');
    button.disabled = true;
    button.innerText = translations.yuklanmoqdaButton; // Optionally, change the text to indicate processing
    setTimeout(function() {
        button.disabled = false;
        button.innerText = translations.saqlashButton; // Restore the button text
    }, 1000);
}
