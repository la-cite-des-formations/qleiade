// 1) On détermine une seule fois la « vraie » route principale
const basePath = window.location.pathname;

// 2) Initialisation du binding (au load et à chaque mise à jour)
document.addEventListener("DOMContentLoaded", initFilterEvents);

initFilterEvents(); // Appel initial immédiat (en cas de script chargé en bas de page)

function initFilterEvents() {
    bindDebounceInputs();
    rewriteSortLinks();
    observeAjaxContainer();
}

// 3) Lie le debounce aux champs <input type="search"> une seule fois chacun
function bindDebounceInputs() {
    document.querySelectorAll('input[type="search"]').forEach(input => {
        if (input.dataset.debounceBound) return;
        input.dataset.debounceBound = "1";

        let timer;
        input.addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(() => {
                input.dispatchEvent(new Event('change', { bubbles: true }));
            }, 300);
        });
    });
}

// 4) Corrige tous les liens de tri pour qu’ils utilisent basePath + queryString intacte
function rewriteSortLinks() {
    document.querySelectorAll('th[data-column] a').forEach(link => {
        try {
            const query = new URL(link.href).search;
            link.href = basePath + query;
        } catch {
            const idx = link.href.indexOf('?');
            if (idx !== -1) {
                link.href = basePath + link.href.slice(idx);
            }
        }
    });
}

// 5) Observe la zone AJAX Orchid et relance le binding après chaque injection
let ajaxObserver;
function observeAjaxContainer() {
    const container = document.querySelector('[data-controller="listener"] [data-async]');
    if (!container || ajaxObserver) return;

    ajaxObserver = new MutationObserver(() => {
        bindDebounceInputs();
        rewriteSortLinks();
    });

    ajaxObserver.observe(container, {
        childList: true,
        subtree: true,
    });
}
