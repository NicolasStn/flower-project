let showPasswordLogin = document.getElementById('showPasswordLogin');
let shownPasswordLogin = document.getElementById('shownPasswordLogin');
let hidePasswordLogin = document.getElementById('hidePasswordLogin');

showPasswordLogin.addEventListener('click', function () {
    showPasswordLogin.style.display = "none";
    hidePasswordLogin.style.display = "block";
    shownPasswordLogin.type = "text";
})

hidePasswordLogin.addEventListener('click', function () {
    hidePasswordLogin.style.display = "none";
    showPasswordLogin.style.display = "block";
    shownPasswordLogin.type = "password";
})

let shownPasswordConfirm = document.getElementById('shownPasswordConfirm');
let showPasswordConfirm = document.getElementById('showPasswordConfirm');
let hidePasswordConfirm = document.getElementById('hidePasswordConfirm');

showPasswordConfirm.addEventListener('click', function () {
    showPasswordConfirm.style.display = "none";
    hidePasswordConfirm.style.display = "block";
    shownPasswordConfirm.type = "text";
})

hidePasswordConfirm.addEventListener('click', function () {
    hidePasswordConfirm.style.display = "none";
    showPasswordConfirm.style.display = "block";
    shownPasswordConfirm.type = "password";
})