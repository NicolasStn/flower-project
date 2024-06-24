
function showDeleteForm(button) {
    // Get the parent div of the clicked button
    var cardDiv = button.closest('.searchCommand');
    
    // Find the delete form within the card
    var deleteForm = cardDiv.querySelector('.deleteClientFinalForm');
    
    // Show the delete form
    deleteForm.style.display = 'flex';
}