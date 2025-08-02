let switchCtn = document.querySelector("#switch-cnt");
let switchC1 = document.querySelector("#switch-c1");
let switchC2 = document.querySelector("#switch-c2");
let switchCircle = document.querySelectorAll(".switch__circle");
let switchBtn = document.querySelectorAll(".switch-btn");
let aContainer = document.querySelector("#a-container_log-reg");
let bContainer = document.querySelector("#b-container_log-reg");
let allButtons = document.querySelectorAll(".submit");

let getButtons = (e) => e.preventDefault();

let changeForm = (e) => {
    switchCtn.classList.add("is-gx");
    setTimeout(function() {
        switchCtn.classList.remove("is-gx");
    }, 1500);

    switchCtn.classList.toggle("is-txr");
    switchCircle[0].classList.toggle("is-txr");
    switchCircle[1].classList.toggle("is-txr");

    switchC1.classList.toggle("is-hidden");
    switchC2.classList.toggle("is-hidden");
    aContainer.classList.toggle("is-txl");
    bContainer.classList.toggle("is-txl");
    bContainer.classList.toggle("is-z200");
};

let mainF = (e) => {
    for (var i = 0; i < allButtons.length; i++)
        allButtons[i].addEventListener("click", getButtons);
    for (var i = 0; i < switchBtn.length; i++)
        switchBtn[i].addEventListener("click", changeForm);

    // Click the switch button programmatically
    switchBtn[0].click();
};

window.addEventListener("load", mainF);

// JavaScript
function togglePasswordVisibility() {
    var passwordField = document.getElementById("password");
    var visibleIcon = document.getElementById("visiblePasswordIcon");
    var hiddenIcon = document.getElementById("hiddenPasswordIcon");

    if (passwordField.type === "password") {
        passwordField.type = "text";
        visibleIcon.classList.remove("hide");
        hiddenIcon.classList.add("hide");
    } else {
        passwordField.type = "password";
        visibleIcon.classList.add("hide");
        hiddenIcon.classList.remove("hide");
    }
}

// ---------------------------------------------------------------------------------- //
