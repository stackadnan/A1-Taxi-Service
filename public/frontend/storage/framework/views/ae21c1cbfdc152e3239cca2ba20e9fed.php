<?php
$headTitle = 'Manage Booking';
?>

<!DOCTYPE html>
<html lang="en">

<?php echo $__env->make('partials.head', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<style>
    @media (min-width: 992px) {
        .sticky-panel {
            position: sticky;
            top: 120px;
        }
    }
</style>

<body>

<?php echo $__env->make('partials.preloader', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('partials.scroll-up', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('partials.offcanvas', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('partials.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<section class="fix section-padding" style="background-color: #0D152F;">
    <div class="container">

        <div class="row g-4">

            <!-- TOP: LOOKUP FORM -->
            <div class="col-12">

                <div class="mt-5 pt-5 mb-4">
                    <h2 class="text-light">Manage Your Booking</h2>
                    <div class="border-bottom border-3 border-warning mb-4 w-25"></div>
                    <p class="text-light mb-4">Enter your details to retrieve your booking.</p>

                    <?php if(!empty($lookupSuccess ?? null)): ?>
                        <div class="alert alert-success"><?php echo e($lookupSuccess); ?></div>
                    <?php endif; ?>

                    <?php if(!empty($lookupError ?? null)): ?>
                        <div class="alert alert-danger"><?php echo e($lookupError); ?></div>
                    <?php endif; ?>

                    <div class="card border-0 shadow-lg rounded-4">
                        <div class="card-body p-4">

                            <form action="<?php echo e(route('manage-booking.lookup')); ?>" method="POST">
                                <?php echo csrf_field(); ?>

                                <div class="row g-3">

                                    <div class="col-md-6">
                                        <label class="fw-semibold mb-2">Email Address</label>
                                        <input type="email" name="booking_email"
                                               class="form-control form-control-lg"
                                               placeholder="e.g. johndoe@gmail.com"
                                               value="<?php echo e(old('booking_email', $lookupInput['booking_email'] ?? '')); ?>"
                                               required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="fw-semibold mb-2">Booking Reference</label>
                                        <input type="text" name="booking_reference"
                                               class="form-control form-control-lg"
                                               placeholder="e.g. A123456"
                                               value="<?php echo e(old('booking_reference', $lookupInput['booking_reference'] ?? '')); ?>"
                                               required>
                                    </div>

                                    <div class="col-12">
                                        <button class="btn btn-warning w-100 fw-bold py-3">
                                            Manage Booking
                                        </button>
                                    </div>

                                </div>
                            </form>

                        </div>
                    </div>

                </div>

            </div>

            <!-- ONLY SHOW BELOW IF BOOKING EXISTS -->
            <?php if(!empty($booking ?? null)): ?>

                <?php ($formData = $bookingFormData ?? []); ?>

                <!-- LEFT: EDIT FORM -->
                <div class="col-lg-7">

                    <div class="card border-0 shadow-lg rounded-4">
                        <div class="card-body p-4">

                            <h4 class="mb-3">Edit Booking</h4>

                            <?php if(!empty($updateErrors ?? null)): ?>
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        <?php $__currentLoopData = $updateErrors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $errorMessage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li><?php echo e($errorMessage); ?></li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <form action="<?php echo e(route('manage-booking.update')); ?>" method="POST">
                                <?php echo csrf_field(); ?>

                                <input type="hidden" name="booking_id" value="<?php echo e($booking->id); ?>">
                                <input type="hidden" name="booking_code" value="<?php echo e($booking->booking_code); ?>">
                                <input type="hidden" name="booking_email_lookup" value="<?php echo e(old('booking_email_lookup', $lookupInput['booking_email'] ?? $booking->email ?? '')); ?>">

                                <div class="row g-3">

                                    <div class="col-md-6">
                                        <label>Passenger Name</label>
                                        <input type="text" name="passenger_name" class="form-control"
                                               value="<?php echo e(old('passenger_name', $formData['passenger_name'] ?? $booking->passenger_name)); ?>">
                                    </div>

                                    <div class="col-md-6">
                                        <label>Email</label>
                                        <input type="email" name="email" class="form-control"
                                               value="<?php echo e(old('email', $formData['email'] ?? $booking->email)); ?>">
                                    </div>

                                    <div class="col-md-6">
                                        <label>Phone</label>
                                        <input type="text" name="phone" class="form-control"
                                               value="<?php echo e(old('phone', $formData['phone'] ?? $booking->phone)); ?>">
                                    </div>

                                    <div class="col-md-6">
                                        <label>Passengers</label>
                                        <input type="number" name="passengers_count" class="form-control"
                                               value="<?php echo e(old('passengers_count', $formData['passengers_count'] ?? $booking->passengers_count)); ?>">
                                    </div>

                                    <div class="col-md-6">
                                        <label>Luggage</label>
                                        <input type="number" name="luggage_count" class="form-control"
                                               value="<?php echo e(old('luggage_count', $formData['luggage_count'] ?? $booking->luggage_count)); ?>">
                                    </div>

                                    <div class="col-md-6">
                                        <label>Pickup Time</label>
                                        <input type="time" name="pickup_time" class="form-control"
                                               value="<?php echo e(old('pickup_time', $booking->pickup_time ? substr($booking->pickup_time,0,5) : '')); ?>">
                                    </div>

                                    <div class="col-md-6">
                                        <label>Flight Number</label>
                                        <input type="text" name="flight_number" class="form-control"
                                               value="<?php echo e(old('flight_number', $booking->flight_number)); ?>">
                                    </div>

                                    <div class="col-12">
                                        <label>Message to Driver</label>
                                        <textarea name="message_to_driver" class="form-control" rows="3">
<?php echo e(old('message_to_driver', $booking->message_to_driver)); ?>

                                        </textarea>
                                    </div>

                                    <div class="col-12">
                                        <button class="btn btn-warning w-100 fw-bold py-3">
                                            Update Booking
                                        </button>
                                    </div>

                                </div>
                            </form>

                        </div>
                    </div>

                </div>

                <!-- RIGHT: INFO PANEL -->
                <div class="col-lg-5 sticky-panel">

                    <div class="card border-0 shadow-lg rounded-4 h-100">
                        <div class="card-body p-4">

                            <h5 class="fw-bold mb-3">What Can You Update?</h5>

                            <ul class="list-unstyled mb-4">
                                <li class="text-success mb-2">✔ Passengers</li>
                                <li class="text-success mb-2">✔ Contact number</li>
                                <li class="text-success mb-2">✔ Email</li>
                                <li class="text-success mb-2">✔ Flight details</li>
                                <li class="text-success mb-2">✔ Luggage</li>
                                <li class="text-success mb-2">✔ Driver notes</li>
                            </ul>

                            <h6 class="fw-bold">Need Bigger Changes?</h6>

                            <ul class="list-unstyled">
                                <li class="text-danger mb-2">✖ Pickup / Drop</li>
                                <li class="text-danger mb-2">✖ Date & Time</li>
                                <li class="text-danger mb-2">✖ Via points</li>
                                <li class="text-danger mb-2">✖ Vehicle type</li>
                            </ul>

                            <div class="mt-4 p-3 rounded bg-light">
                                <small>Contact support:</small><br>
                                <strong>info@a1airporttransfers.co.uk</strong>
                            </div>

                        </div>
                    </div>

                </div>

            <?php endif; ?>

        </div>

    </div>
</section>

<?php echo $__env->make('partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('partials.script', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

</body>
</html><?php /**PATH /home/executiveairport/public_html/frontend/resources/views/manage-booking.blade.php ENDPATH**/ ?>