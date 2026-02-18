(function () {
    var carousels = document.querySelectorAll('[data-special-carousel]');

    carousels.forEach(function (carousel) {
        var slides = carousel.querySelectorAll('.hero__slide');
        var prev = carousel.querySelector('[data-carousel-prev]');
        var next = carousel.querySelector('[data-carousel-next]');
        var dots = carousel.querySelectorAll('[data-carousel-dot]');

        if (slides.length <= 1) {
            return;
        }

        var index = 0;
        var timer = null;

        var show = function (newIndex) {
            index = (newIndex + slides.length) % slides.length;

            slides.forEach(function (slide, idx) {
                var active = idx === index;
                slide.hidden = !active;
                slide.classList.toggle('is-active', active);
            });

            dots.forEach(function (dot, idx) {
                var active = idx === index;
                dot.classList.toggle('is-active', active);
                dot.setAttribute('aria-pressed', active ? 'true' : 'false');
            });
        };

        var resetAuto = function () {
            if (timer !== null) {
                window.clearInterval(timer);
            }

            timer = window.setInterval(function () {
                show(index + 1);
            }, 4500);
        };

        if (prev) {
            prev.addEventListener('click', function () {
                show(index - 1);
                resetAuto();
            });
        }

        if (next) {
            next.addEventListener('click', function () {
                show(index + 1);
                resetAuto();
            });
        }

        dots.forEach(function (dot) {
            dot.addEventListener('click', function () {
                var dotIndex = parseInt(dot.getAttribute('data-carousel-dot') || '0', 10);
                show(dotIndex);
                resetAuto();
            });
        });

        show(0);
        resetAuto();
    });
})();
