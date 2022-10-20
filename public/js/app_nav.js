
const button = document.querySelectorAll('.login');



button.forEach(e => {
        e.addEventListener('click', () => {
        const parent = e.parentElement;
        const list = parent.querySelector('.drop');
        list.classList.toggle('show');
});
})
