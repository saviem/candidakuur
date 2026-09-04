if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(() => {});
    });
}

const isStandalone = window.matchMedia('(display-mode: standalone)').matches
    || window.navigator.standalone === true;

document.addEventListener('DOMContentLoaded', () => {
    if (isStandalone) {
        document.documentElement.classList.add('is-standalone');
        document.querySelectorAll('.js-install-card').forEach((card) => card.classList.add('hidden'));
        return;
    }

    const androidButtons = document.querySelectorAll('.js-install-android');
    let deferredPrompt = null;

    window.addEventListener('beforeinstallprompt', (event) => {
        event.preventDefault();
        deferredPrompt = event;
        androidButtons.forEach((button) => button.classList.remove('hidden'));
    });

    androidButtons.forEach((button) => {
        button.addEventListener('click', async () => {
            if (! deferredPrompt) {
                return;
            }
            deferredPrompt.prompt();
            await deferredPrompt.userChoice;
            deferredPrompt = null;
            androidButtons.forEach((item) => item.classList.add('hidden'));
        });
    });
});
