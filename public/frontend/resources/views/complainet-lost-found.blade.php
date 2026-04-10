<?php
$headTitle = 'Complainet / Lost Found';
$img = \App\Support\GalleryPath::path('i/149');
$Title = 'Home';
$Title2 = 'Complainet / Lost Found';
$SubTitle = 'Complainet / Lost Found';

$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
if (str_contains($host, 'executiveairportcars.com')) {
    $apiUrl = 'https://admin.executiveairportcars.com/api/complaint-lost-found';
} else {
    $apiUrl = 'http://localhost/AirportServices/public/api/complaint-lost-found';
}
?>

@include('partials.layouts.layoutsTop')

<section class="contact-section-1 fix section-padding pb-0">
    <div class="container">
        <div class="contact-wrapper-area">
            <div class="row g-4 justify-content-center">
                <div class="col-lg-8">
                    <div class="contact-content">
                        <div class="section-title">
                            <img src="{{ \App\Support\GalleryPath::path('i/2') }}" alt="icon-img" class="wow fadeInUp">
                            <span class="wow fadeInUp" data-wow-delay=".2s">support request</span>
                            <h2 class="wow fadeInUp" data-wow-delay=".4s">Complainet / Lost Found</h2>
                        </div>

                        <div id="complaint-status" class="alert alert-info mt-4" role="alert">
                            Fill out the form below and submit your request.
                        </div>

                        <form id="complaint-form" class="contact-form-items mt-4 mt-md-0">
                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <div class="form-clt">
                                        <input type="text" name="name" id="name" placeholder="Name" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-clt">
                                        <input type="email" name="email" id="email" placeholder="Email" required>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-clt">
                                        <input type="text" name="booking_id" id="booking_id" placeholder="Booking Id">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-clt">
                                        <textarea name="concern" id="concern" placeholder="Conern" required></textarea>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-clt">
                                        <textarea name="lost_found" id="lost_found" placeholder="Lost Product Info" required></textarea>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <button type="submit" id="complaint-submit" class="theme-btn">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    (function () {
        var apiUrl = @json($apiUrl);
        var form = document.getElementById('complaint-form');
        var statusBox = document.getElementById('complaint-status');
        var submitButton = document.getElementById('complaint-submit');

        form.addEventListener('submit', function (event) {
            event.preventDefault();

            var payload = {
                name: form.name.value.trim(),
                email: form.email.value.trim(),
                booking_id: form.booking_id.value.trim(),
                concern: form.concern.value.trim(),
                lost_found: form.lost_found.value.trim(),
                source_url: window.location.href
            };

            submitButton.disabled = true;
            statusBox.className = 'alert alert-info mt-4';
            statusBox.textContent = 'Submitting your request...';

            fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(function (response) {
                return response.json().then(function (data) {
                    return { status: response.status, data: data };
                });
            })
            .then(function (result) {
                if (result.status >= 200 && result.status < 300 && result.data && result.data.success) {
                    form.reset();
                    statusBox.className = 'alert alert-success mt-4';
                    statusBox.textContent = result.data.message || 'Submitted successfully.';
                    return;
                }

                var message = (result.data && (result.data.message || firstValidationMessage(result.data.errors))) || 'Could not submit your request.';
                statusBox.className = 'alert alert-danger mt-4';
                statusBox.textContent = message;
            })
            .catch(function () {
                statusBox.className = 'alert alert-danger mt-4';
                statusBox.textContent = 'Request failed. Please try again.';
            })
            .finally(function () {
                submitButton.disabled = false;
            });
        });

        function firstValidationMessage(errors) {
            if (!errors || typeof errors !== 'object') {
                return '';
            }

            var keys = Object.keys(errors);
            if (!keys.length) {
                return '';
            }

            var first = errors[keys[0]];
            return Array.isArray(first) && first.length ? first[0] : '';
        }
    })();
</script>

@include('partials.layouts.layoutsBottom')
