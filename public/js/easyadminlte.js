/*
 * AdminLTE-style light/dark toggle in the header. EasyAdmin's page-color-scheme.js already applies,
 * persists and syncs the color scheme when one of its appearance items is clicked
 * (".dropdown-settings a.dropdown-appearance-item[data-ea-color-scheme]"), so the toggle just clicks
 * the item for the opposite of the current scheme.
 */
document.addEventListener('DOMContentLoaded', () => {
    // the appearance items are href="#" links and EasyAdmin doesn't prevent their default action
    document.querySelectorAll('a.dropdown-appearance-item[data-ea-color-scheme]').forEach((item) => {
        item.addEventListener('click', (event) => event.preventDefault());
    });

    document.querySelectorAll('[data-easyadminlte-color-scheme-toggle]').forEach((toggle) => {
        toggle.addEventListener('click', (event) => {
            event.preventDefault();

            const nextScheme = 'dark' === document.body.getAttribute('data-bs-theme') ? 'light' : 'dark';
            document
                .querySelector(`.dropdown-settings a.dropdown-appearance-item[data-ea-color-scheme="${nextScheme}"]`)
                ?.click();
        });
    });
});
