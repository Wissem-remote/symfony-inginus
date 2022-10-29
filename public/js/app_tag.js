const u = document.querySelector('.tag');
let e = 0;
if (u) {
    setTimeout(() => u.classList.add('registerShow'), 10)
    
    setInterval(() => {
        e++;
        if (e > 2) {
            u.classList.remove('registerShow');
        }
    },1000) 
}


