const tag = document.querySelector('.tag');
console.log(tag)
let e = 0;
if (tag) {
    setTimeout(() => tag.classList.add('registerShow'), 10)
    
    setInterval(() => {
        e++;
        if (e > 4) {
            tag.classList.remove('registerShow');
        }
    },1000) 
}


