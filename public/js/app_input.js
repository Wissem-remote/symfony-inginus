const input = document.querySelector('.input');
const parentInput = input.parentElement;

let div = document.createElement('div');
let p = document.createElement('p');
div.setAttribute('class', ' notValid col-12 mt-3 text-center m-auto rounded bg-light');
p.setAttribute('class','p-1')
p.textContent = ' Le mot-passe doit contenir au moins 14 Caractères et une Majuscule et une Minuscule un Chiffre';
div.appendChild(p);

const show = () => {
    if (!parentInput.querySelector('.valid')) {
        div.classList.replace('notValid', 'valid');
            parentInput.appendChild(div);
        }
}
const fade = () => {
    div.classList.remove('valid');
    div.classList.add('notValid');
}
input.addEventListener('keyup', e => {
    
    
    let value = input.value;
    let match = value.match(/(?=[0-9a-zA-Z.*]+$)^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.{14,}).*$/g);
    console.log(match);
    
    if (input.value.length == 0) {
        fade();
        p.classList.remove('text-success')
    } else {
        show();
    }

    if (match) {
        p.classList.add('text-success')
    }
})