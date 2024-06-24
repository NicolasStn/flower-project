let cookieContainer = document.getElementById('cookieContainer');
let cookieDelete = document.getElementById('cookieDelete');

cookieDelete.addEventListener('click', function () {
    cookieContainer.style.transitionDuration = '0.4s';
    cookieContainer.style.bottom = '-50vh';
})

let cookieImg = document.getElementById('cookieImg');

cookieImg.addEventListener('click', function () {
    cookieContainer.style.transitionDuration = '0.4s';
    cookieContainer.style.bottom = '0px';
})