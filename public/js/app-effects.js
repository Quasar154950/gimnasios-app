document.addEventListener('DOMContentLoaded', () => {

    document.addEventListener('click', (e) => {

        const el = e.target.closest('button, a, .native-tap, .card-app');

        if (!el) {
            return;
        }

        if (el.classList.contains('card-app')) {
            const rect = el.getBoundingClientRect();

            const rippleX = e.clientX - rect.left;
            const rippleY = e.clientY - rect.top;

            el.style.setProperty('--ripple-x', `${rippleX}px`);
            el.style.setProperty('--ripple-y', `${rippleY}px`);

            el.classList.remove('ripple-active');

            void el.offsetWidth;

            el.classList.add('ripple-active');

            setTimeout(() => {
                el.classList.remove('ripple-active');
            }, 420);
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

                }, 200);

            } else {
                window.location.href = el.href;
            }
        }

    });

});











































