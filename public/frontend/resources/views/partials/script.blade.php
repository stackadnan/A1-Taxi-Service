
    <!--<< All JS Plugins >>-->
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <!--<< Viewport Js >>-->
    <script src="{{ asset('assets/js/viewport.jquery.js') }}"></script>
    <!--<< Bootstrap Js >>-->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <!--<< Nice Select Js >>-->
    <script src="{{ asset('assets/js/jquery.nice-select.min.js') }}"></script>
    <!--<< Waypoints Js >>-->
    <script src="{{ asset('assets/js/jquery.waypoints.js') }}"></script>
    <!--<< Counterup Js >>-->
    <script src="{{ asset('assets/js/jquery.counterup.min.js') }}"></script>
    <!--<< Datepicker Js >>-->
    <script src="{{ asset('assets/js/bootstrap-datepicker.js') }}"></script>
    <!--<< Swiper Slider Js >>-->
    <script src="{{ asset('assets/js/swiper-bundle.min.js') }}"></script>
    <!--<< MeanMenu Js >>-->
    <script src="{{ asset('assets/js/jquery.meanmenu.min.js') }}"></script>
    <!--<< Magnific Popup Js >>-->
    <script src="{{ asset('assets/js/jquery.magnific-popup.min.js') }}"></script>
    <!--<< GSAP Animation Js >>-->
    <script src="{{ asset('assets/js/animation.js') }}"></script>
    <!--<< Wow Animation Js >>-->
    <script src="{{ asset('assets/js/wow.min.js') }}"></script>
    
    {!! $script ?? '' !!}

    <script>
        window.GALLERY_IMAGE_ID_MAP = @json($galleryImageIdMap ?? []);
        window.GALLERY_IMAGE_PATH_MAP = @json($galleryImagePathMap ?? []);

        (function () {
            var idMap = window.GALLERY_IMAGE_ID_MAP || {};
            var pathMap = window.GALLERY_IMAGE_PATH_MAP || {};

            function normalizePath(raw) {
                if (!raw) {
                    return '';
                }

                var value = String(raw).trim().replace(/^['\"]|['\"]$/g, '');
                if (!value) {
                    return '';
                }

                if (value.indexOf('data:') === 0) {
                    return '';
                }

                try {
                    if (/^https?:\/\//i.test(value)) {
                        value = new URL(value, window.location.origin).pathname;
                    }
                } catch (e) {
                    // Ignore malformed URL and continue with raw value.
                }

                value = value.split('?')[0].split('#')[0];
                value = value.replace(/\\\\/g, '/');

                var assetsIndex = value.indexOf('/assets/');
                if (assetsIndex !== -1) {
                    value = value.substring(assetsIndex + 1);
                }

                return value.replace(/^\/+/, '');
            }

            function resolveRenderPath(rawPath) {
                var normalized = normalizePath(rawPath);
                if (!normalized) {
                    return null;
                }

                if (Object.prototype.hasOwnProperty.call(pathMap, normalized)) {
                    return String(pathMap[normalized]);
                }

                return null;
            }

            function applyIdAttribute(element, rawPath) {
                var normalized = normalizePath(rawPath);
                if (!normalized) {
                    return;
                }

                var id = idMap[normalized];
                if (id) {
                    element.setAttribute('data-gallery-id', String(id));
                }
            }

            document.querySelectorAll('img[src]').forEach(function (img) {
                var currentSrc = img.getAttribute('src') || '';
                var renderPath = resolveRenderPath(currentSrc);

                if (renderPath && normalizePath(renderPath) !== normalizePath(currentSrc)) {
                    img.setAttribute('src', renderPath);
                }

                applyIdAttribute(img, img.getAttribute('src') || currentSrc);
            });

            document.querySelectorAll('[style*="background-image"]').forEach(function (el) {
                var style = el.getAttribute('style') || '';
                var match = style.match(/background-image\s*:\s*url\(\s*['\"]?([^'\")]+)['\"]?\s*\)/i);
                if (match && match[1]) {
                    var originalPath = match[1];
                    var renderPath = resolveRenderPath(originalPath);

                    if (renderPath && normalizePath(renderPath) !== normalizePath(originalPath)) {
                        var replacedStyle = style.replace(match[0], 'background-image: url(\'' + renderPath + '\')');
                        el.setAttribute('style', replacedStyle);
                    }

                    applyIdAttribute(el, renderPath || originalPath);
                }
            });
        })();
    </script>

    <!--<< Main.js >>-->
    <script src="{{ asset('assets/js/main.js') }}?v={{ @filemtime(public_path('assets/js/main.js')) }}"></script>
