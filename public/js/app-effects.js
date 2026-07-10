document.addEventListener('DOMContentLoaded', () => {

    const tappables = document.querySelectorAll(
        'button, a, .native-tap, .card-app'
    );

    tappables.forEach((el) => {

        el.addEventListener('click', (e) => {

            if (navigator.vibrate) {
                navigator.vibrate(12);
            }

            // Solo enlaces internos
            if (
                el.tagName === 'A' &&
                el.href &&
                el.origin === window.location.origin &&
                !el.target &&
                !el.hasAttribute('download')
            ) {

                // No animar enlaces Livewire
                if (el.hasAttribute('wire:click')) {
                    return;
                }

                e.preventDefault();

                const page = document.getElementById('app-page');

                if (page) {

                    page.classList.remove('page-enter');
                    page.classList.add('page-leave');

                    setTimeout(() => {
                        window.location = el.href;
                    }, 170);

                } else {

                    window.location = el.href;

                }
            }

        });

    });

});











































