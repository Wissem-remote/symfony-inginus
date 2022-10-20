const input = document.querySelector('.input');
const parentInput = input.parentElement;

let div = document.createElement('div');
let p = document.createElement('p');
div.setAttribute('class', ' notValid col-12 mt-3 text-center m-auto rounded bg-light');
p.setAttribute('class','p-1')
p.textContent = ' Votre mot-passe doit contenir au moins 6 Caractères et une Majuscule';
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
    let match = value.match(/(?=[0-9a-zA-Z.*]+$)^(?=.*[A-Z])(?=.{6,}).*$/g);
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