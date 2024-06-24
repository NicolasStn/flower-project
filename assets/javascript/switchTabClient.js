let infos = document.getElementById('cardClientsContainerRight');
let edit = document.getElementById('cardClientContainerRightEdit');
let deleteClient = document.getElementById('cardClientsContainerRightDelete');
let editPassword = document.getElementById('editPasswordContainer');

let btnInfos = document.getElementById('cardClientsContainerLeftBtnInfos');
let btnDelete = document.getElementById('cardClientsContainerLeftBtnDelete');
let btnEdit = document.getElementById('cardClientsContainerLeftBtnEdit');
let btnEditPassword = document.getElementById('cardClientsContainerLeftBtnEditPassword');

btnInfos.addEventListener('click', function () {
    infos.style.display = 'flex';
    edit.style.display = 'none';
    deleteClient.style.display = 'none';
    editPassword.style.display = 'none';
})

btnEdit.addEventListener('click', function () {
    infos.style.display = 'none';
    edit.style.display = 'flex';
    deleteClient.style.display = 'none';
    editPassword.style.display = 'none';
})

btnDelete.addEventListener('click', function () {
    infos.style.display = 'none';
    edit.style.display = 'none';
    deleteClient.style.display = 'flex';
    editPassword.style.display = 'none';
})

btnEditPassword.addEventListener('click', function () {
    infos.style.display = 'flex';
    editPassword.style.display = 'flex';
    edit.style.display = 'none';
    deleteClient.style.display = 'none';
})