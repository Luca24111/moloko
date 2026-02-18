(function () {
    var menuRoots = document.querySelectorAll('[data-menu-filter]');

    menuRoots.forEach(function (root) {
        var buttons = root.querySelectorAll('[data-category-filter]');
        var sections = root.querySelectorAll('[data-category-section]');

        if (!buttons.length || !sections.length) {
            return;
        }

        var activeCategory = 'all';

        buttons.forEach(function (button) {
            if (button.classList.contains('is-active')) {
                activeCategory = button.getAttribute('data-category-filter') || 'all';
            }
        });

        var applyFilter = function (category) {
            sections.forEach(function (section) {
                var sectionCategory = section.getAttribute('data-category-section') || '';
                var show = category === 'all' || sectionCategory === category;
                section.hidden = !show;
            });

            buttons.forEach(function (button) {
                var selected = (button.getAttribute('data-category-filter') || '') === category;
                button.classList.toggle('is-active', selected);
                button.setAttribute('aria-pressed', selected ? 'true' : 'false');
            });
        };

        buttons.forEach(function (button) {
            button.addEventListener('click', function () {
                applyFilter(button.getAttribute('data-category-filter') || 'all');
            });
        });

        applyFilter(activeCategory);
    });
})();
