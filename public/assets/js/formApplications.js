let fileInputs = document.querySelectorAll('input[type="file"]');
fileInputs.forEach(function (fileInput) {
    let wrapper = fileInput.closest(".file-upload-wrapper");
    let fileName = wrapper.querySelector(".file-name");
    let cancelUpload = wrapper.querySelector(".cancel-upload");
    cancelUpload.disabled = true;
    fileInput.addEventListener("change", function (e) {
        fileName.textContent = e.target.files[0].name;
        cancelUpload.disabled = false;
    });
    cancelUpload.addEventListener("click", function (e) {
        e.stopPropagation(); // Stop the propagation of the click event
        e.preventDefault();
        fileInput.value = "";
        fileName.textContent = "Asos hujjatni yuklang...";
        cancelUpload.disabled = true;
    });
});
