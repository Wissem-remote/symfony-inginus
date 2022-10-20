const register = document.querySelector('.register');
let i = 0;
setTimeout(()=>register.classList.add('registerShow'),10)
setInterval(() => {
    
    i++;
    if (i > 4) {
        register.classList.remove('registerShow');
    }
},1000)