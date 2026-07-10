document.addEventListener('DOMContentLoaded', () => {

    document.addEventListener('click', (e) => {

        const el = e.target.closest('button, a, .native-tap, .card-app');

        if (!el) {
            return;
        }

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

            // No interceptar enlaces manejados por Livewire
            if (
                el.hasAttribute('wire:click') ||
                el.hasAttribute('wire:navigate')
            ) {
                return;
            }

            e.preventDefault();

            const page = document.getElementById('app-page');

            if (page) {

                // Dejamos que el botón vuelva antes de iniciar la salida
                setTimeout(() => {

                    page.classList.remove('page-enter');
                    page.classList.add('page-leave');

                    setTimeout(() => {
                        window.location.href = el.href;
                    }, 170);

                }, 100);

            } else {
                window.location.href = el.href;
            }
        }

    });

});











































