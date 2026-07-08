document.addEventListener('DOMContentLoaded', () => {
    const tappables = document.querySelectorAll('button, a, .native-tap, .card-app');

    tappables.forEach((el) => {
        el.addEventListener('click', () => {
            if (navigator.vibrate) {
                navigator.vibrate(12);
            }
        });
    });
});











































