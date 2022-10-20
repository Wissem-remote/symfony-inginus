const options = {
    root: null,
    rootMargin: '0px',
    threshold: .4
}

const handleSee = function (entries, observer) {
    entries.forEach(function(e) {
        if (e.intersectionRatio > .4) {
            e.target.classList.add('reveal-visible')
            observer.unobserve(e.target)
            } 
    });
}

const observer = new IntersectionObserver(handleSee, options);
const reveal = document.querySelectorAll('.reveal').forEach((r) => {
    observer.observe(r);
})
