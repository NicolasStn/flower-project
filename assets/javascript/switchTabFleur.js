let add = document.getElementById('addFleur');
let variete = document.getElementById('addVariete');
let couleur = document.getElementById('addCouleur');

let addBtn = document.getElementById('addFleurBtn');
let varieteBtn = document.getElementById('addVarieteBtn');
let couleurBtn = document.getElementById('addCouleurBtn');

addBtn.addEventListener('click', function () {
    add.style.display = 'flex';
    variete.style.display = 'none';
    couleur.style.display = 'none';
})

varieteBtn.addEventListener('click', function () {
    add.style.display = 'none';
    variete.style.display = 'flex';
    couleur.style.display = 'none';
})

couleurBtn.addEventListener('click', function () {
    add.style.display = 'none';
    variete.style.display = 'none';
    couleur.style.display = 'flex';
})