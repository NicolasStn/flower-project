let cardClientsAdd = document.getElementById('cardClientsAdd');
let cardClientsPlus = document.getElementById('cardClientsPlus');

cardClientsAdd.addEventListener('click', function () {
    
    cardClientsAdd.style.transitionDuration = '0.4s';
    cardClientsPlus.style.display = 'none';
    cardClientsAdd.style.height= 'auto';
    cardClientsAdd.style.display = 'flex';
    cardClientsAdd.style.alignItems = 'center';
    cardClientsAdd.style.marginBottom = '5vh';
    cardClientsAdd.style.position = 'relative';
    cardClientsAdd.style.gap = '5px';
    cardClientsAdd.style.flexDirection= 'column';

});

function showDeleteForm(button) {
    // Get the parent div of the clicked button
    var cardDiv = button.closest('.cardFournisseurs');
    
    // Find the delete form within the card
    var deleteForm = cardDiv.querySelector('.deleteClientFinalForm');
    
    // Show the delete form
    deleteForm.style.display = 'flex';
}
