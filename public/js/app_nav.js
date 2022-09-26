const list = document.querySelector('.drop');
const button = document.querySelector('.login');

button.addEventListener('click', () => {
    list.classList.toggle('show');
});