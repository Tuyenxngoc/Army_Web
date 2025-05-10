document.addEventListener('DOMContentLoaded', function() {
    const helloContainer = document.getElementById('hello-container');
    const container = document.getElementById('container');
    const typedText = document.getElementById('typed-text');
    const hasSeenWelcome = localStorage.getItem('hasSeenWelcome');

    if (!hasSeenWelcome) {
        const typed = new Typed('#typed-text', {
            strings: [
                "Xin Chào Thiện Xạ!",
                "Chào mừng tới với thế giới của Mobi Army 2D!"
            ],
            typeSpeed: 25,
            backSpeed: 25,
            backDelay: 500,
            startDelay: 500,
            onComplete: function () {
                setTimeout(function() {
                    helloContainer.classList.add('d-none');
                    container.classList.remove('d-none');
                    localStorage.setItem('hasSeenWelcome', 'true');
                }, 1000); 
            }
        });
        helloContainer.classList.remove('d-none');
    } else {
        helloContainer.classList.add('d-none');
        container.classList.remove('d-none');
    }
});

