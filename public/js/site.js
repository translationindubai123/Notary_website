document.addEventListener('click', function (event) {
    const link = event.target.closest('[data-contact-method]');
    if (link && typeof window.gtag === 'function') {
        window.gtag('event', 'contact_click', {
            method: link.dataset.contactMethod
        });
    }
});

document.documentElement.classList.add('has-js');

const scrollRevealElements = document.querySelectorAll('[data-scroll-reveal]');
const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

if (scrollRevealElements.length) {
    if (prefersReducedMotion || !('IntersectionObserver' in window)) {
        scrollRevealElements.forEach(function (element) {
            element.classList.add('is-visible');
        });
    } else {
        const scrollRevealObserver = new IntersectionObserver(function (entries, observer) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -32px 0px' });

        scrollRevealElements.forEach(function (element) {
            scrollRevealObserver.observe(element);
        });
    }
}

const navigationToggle = document.querySelector('[data-nav-toggle]');
const navigation = document.getElementById('main-navigation');

if (navigationToggle && navigation) {
    navigationToggle.addEventListener('click', function () {
        const isExpanded = navigationToggle.getAttribute('aria-expanded') === 'true';
        navigationToggle.setAttribute('aria-expanded', String(!isExpanded));
        navigation.classList.toggle('is-open', !isExpanded);
    });

    navigation.addEventListener('click', function (event) {
        const submenuToggle = event.target.closest('[data-submenu-toggle]');
        if (submenuToggle) {
            const item = submenuToggle.closest('.nav-services, .nav-category-item');
            const isExpanded = submenuToggle.getAttribute('aria-expanded') === 'true';
            submenuToggle.setAttribute('aria-expanded', String(!isExpanded));
            item.classList.toggle('is-open', !isExpanded);
            return;
        }

        if (event.target.closest('a')) {
            navigation.classList.remove('is-open');
            navigation.querySelectorAll('.nav-services.is-open, .nav-category-item.is-open').forEach(function (item) {
                item.classList.remove('is-open');
                const toggle = item.querySelector(':scope > .nav-services-heading [data-submenu-toggle], :scope > .nav-category-heading [data-submenu-toggle]');
                if (toggle) {
                    toggle.setAttribute('aria-expanded', 'false');
                }
            });
            navigationToggle.setAttribute('aria-expanded', 'false');
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && navigation.classList.contains('is-open')) {
            const expandedItems = Array.from(navigation.querySelectorAll('.nav-category-item.is-open'));
            if (expandedItems.length) {
                const item = expandedItems[expandedItems.length - 1];
                item.classList.remove('is-open');
                const toggle = item.querySelector(':scope > .nav-category-heading [data-submenu-toggle]');
                toggle.setAttribute('aria-expanded', 'false');
                toggle.focus();
                return;
            }
            const servicesItem = navigation.querySelector('.nav-services.is-open');
            if (servicesItem) {
                servicesItem.classList.remove('is-open');
                servicesItem.querySelector(':scope > .nav-services-heading [data-submenu-toggle]').setAttribute('aria-expanded', 'false');
                servicesItem.querySelector(':scope > .nav-services-heading [data-submenu-toggle]').focus();
                return;
            }
            navigation.classList.remove('is-open');
            navigationToggle.setAttribute('aria-expanded', 'false');
            navigationToggle.focus();
        }
    });

    document.addEventListener('click', function (event) {
        if (!navigation.contains(event.target)) {
            navigation.querySelectorAll('.nav-services.is-open, .nav-category-item.is-open').forEach(function (item) {
                item.classList.remove('is-open');
                const toggle = item.querySelector(':scope > .nav-services-heading [data-submenu-toggle], :scope > .nav-category-heading [data-submenu-toggle]');
                if (toggle) {
                    toggle.setAttribute('aria-expanded', 'false');
                }
            });
        }
    });
}

document.querySelectorAll('[data-submit-form]').forEach(function (form) {
    form.addEventListener('submit', function () {
        const button = form.querySelector('button[type="submit"]');
        if (button) {
            button.disabled = true;
            button.setAttribute('aria-disabled', 'true');
            button.dataset.originalLabel = button.textContent.trim();
            button.textContent = 'Sending…';
        }
    });
});
