(function () {
    var placeholderAttr = 'data-lazy-src';
    var placeholderSetAttr = 'data-lazy-srcset';

    var activateImage = function (image) {
        if (!(image instanceof HTMLImageElement)) {
            return;
        }

        var lazySrc = image.getAttribute(placeholderAttr);
        if (lazySrc) {
            image.src = lazySrc;
            image.removeAttribute(placeholderAttr);
        }
    };

    var activatePicture = function (picture) {
        if (!(picture instanceof HTMLPictureElement)) {
            return;
        }

        picture.querySelectorAll('source[' + placeholderSetAttr + ']').forEach(function (source) {
            var lazySet = source.getAttribute(placeholderSetAttr);
            if (!lazySet) {
                return;
            }

            source.srcset = lazySet;
            source.removeAttribute(placeholderSetAttr);
        });

        activateImage(picture.querySelector('img'));
        picture.removeAttribute('data-lazy-picture');
    };

    var activateNode = function (node) {
        if (node instanceof HTMLPictureElement) {
            activatePicture(node);

            return;
        }

        activateImage(node);
    };

    var targets = Array.prototype.slice.call(
        document.querySelectorAll('picture[data-lazy-picture], img[' + placeholderAttr + ']')
    );

    if (targets.length === 0) {
        return;
    }

    if (!('IntersectionObserver' in window)) {
        targets.forEach(activateNode);

        return;
    }

    var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (!entry.isIntersecting) {
                return;
            }

            activateNode(entry.target);
            observer.unobserve(entry.target);
        });
    }, {
        rootMargin: '300px 0px',
    });

    targets.forEach(function (target) {
        observer.observe(target);
    });
})();
