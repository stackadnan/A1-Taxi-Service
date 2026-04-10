<?php
$headTitle = 'Manage Booking';
?>

<!DOCTYPE html>
<html lang="en">

@include('partials.head')

<body>
	@include('partials.preloader')

	@include('partials.scroll-up')

	@include('partials.offcanvas')

	@include('partials.header')

	<section class="fix section-padding" style="background-color: #0D152F;">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-xl-8 col-lg-9 col-md-11">
					<div class="contact-content mt-5 pt-5 mb-5 pb-4">
						<h2 class="mb-1 mt-3 text-light">Manage Your Booking</h2>
						<div class="border-bottom border-3 border-warning mb-4 w-25"></div>
						<p class="mb-4 text-light">To amend your booking please provide the details below:</p>

						@if (!empty($lookupSuccess ?? null))
							<div class="alert alert-success">{{ $lookupSuccess }}</div>
						@endif

						@if (!empty($lookupError ?? null))
							<div class="alert alert-danger">{{ $lookupError }}</div>
						@endif

						<form action="{{ route('manage-booking.lookup') }}" method="POST" class="contact-form-items mt-0">
							@csrf
							<div class="row g-4">
								<div class="col-lg-12">
									<label for="booking_email" class="fw-semibold text-light mb-2">Email Address Used for Booking</label>
									<input id="booking_email" type="email" name="booking_email" class="form-control form-control-lg" placeholder="e.g. jhondoe@gmail.com" value="{{ old('booking_email', $lookupInput['booking_email'] ?? '') }}" required>
									@error('booking_email')
										<small class="text-warning">{{ $message }}</small>
									@enderror
								</div>
								<div class="col-lg-12">
									<label for="booking_reference" class="fw-semibold text-light mb-2">Booking Reference Number / ID</label>
									<input id="booking_reference" type="text" name="booking_reference" class="form-control form-control-lg" placeholder="e.g. A123456 or 27" value="{{ old('booking_reference', $lookupInput['booking_reference'] ?? '') }}" required>
									@error('booking_reference')
										<small class="text-warning">{{ $message }}</small>
									@enderror
								</div>
								<div class="col-lg-12 pt-1">
									<button type="submit" class="btn btn-light border border-secondary px-4 py-3 fw-semibold">Manage My Booking</button>
								</div>
							</div>
						</form>

						@if (!empty($booking ?? null))
							@php($formData = $bookingFormData ?? [])
							<div class="card mt-5 border-0 shadow-sm">
								<div class="card-body p-4">
									<h4 class="mb-3">Booking Details (Editable)</h4>
									@if (!empty($updateErrors ?? null))
										<div class="alert alert-danger">
											<ul class="mb-0 ps-3">
												@foreach (($updateErrors ?? []) as $errorMessage)
													<li>{{ $errorMessage }}</li>
												@endforeach
											</ul>
										</div>
									@endif
									<form action="{{ route('manage-booking.update') }}" method="POST">
										@csrf
										<input type="hidden" name="booking_id" value="{{ $booking->id }}">
										<input type="hidden" name="booking_code" value="{{ $booking->booking_code }}">
										<input type="hidden" name="booking_email_lookup" value="{{ $lookupInput['booking_email'] ?? $booking->email }}">

										<div class="row g-3">
											<div class="col-md-6">
												<label class="form-label">Booking Code</label>
												<input type="text" class="form-control" value="{{ $booking->booking_code }}" readonly>
											</div>
											<div class="col-md-6">
												<label class="form-label">Passenger Name</label>
												<input type="text" name="passenger_name" class="form-control" value="{{ old('passenger_name', $formData['passenger_name'] ?? $booking->passenger_name) }}">
											</div>
											<div class="col-md-6">
												<label class="form-label">Email</label>
												<input type="email" name="email" class="form-control" value="{{ old('email', $formData['email'] ?? $booking->email) }}">
											</div>
											<div class="col-md-6">
												<label class="form-label">Phone</label>
												<input type="text" name="phone" class="form-control" value="{{ old('phone', $formData['phone'] ?? $booking->phone) }}">
											</div>
											<div class="col-md-6">
												<label class="form-label">Alternate Phone</label>
												<input type="text" name="alternate_phone" class="form-control" value="{{ old('alternate_phone', $formData['alternate_phone'] ?? $booking->alternate_phone) }}">
											</div>
											<div class="col-md-6">
												<label class="form-label">Passengers</label>
												<input type="number" min="1" max="20" name="passengers_count" class="form-control" value="{{ old('passengers_count', $formData['passengers_count'] ?? $booking->passengers_count) }}">
											</div>
											<div class="col-md-6">
												<label class="form-label">Luggage</label>
												<input type="number" min="0" max="50" name="luggage_count" class="form-control" value="{{ old('luggage_count', $formData['luggage_count'] ?? $booking->luggage_count) }}">
											</div>
											<div class="col-md-6">
												<label class="form-label">Pickup Date</label>
												<input type="date" name="pickup_date" class="form-control" value="{{ old('pickup_date', $formData['pickup_date'] ?? $booking->pickup_date) }}">
											</div>
											<div class="col-md-6">
												<label class="form-label">Pickup Time</label>
												<input type="time" name="pickup_time" class="form-control" value="{{ old('pickup_time', $formData['pickup_time'] ?? ($booking->pickup_time ? substr((string) $booking->pickup_time, 0, 5) : '')) }}">
											</div>

											<div class="col-md-6">
												<label class="form-label">From</label>
												<input type="text" class="form-control" value="{{ $booking->pickup_address }}" readonly>
											</div>
											<div class="col-md-6">
												<label class="form-label">To</label>
												<input type="text" class="form-control" value="{{ $booking->dropoff_address }}" readonly>
											</div>
											<div class="col-md-6">
												<label class="form-label">Vehicle Type</label>
												<input type="text" class="form-control" value="{{ $booking->vehicle_type }}" readonly>
											</div>
											<div class="col-md-6">
												<label class="form-label">Payment Type</label>
												<input type="text" class="form-control" value="{{ $booking->payment_type }}" readonly>
											</div>

											<div class="col-md-6">
												<label class="form-label">Flight Number</label>
												<input type="text" name="flight_number" class="form-control" value="{{ old('flight_number', $formData['flight_number'] ?? $booking->flight_number) }}">
											</div>
											<div class="col-md-3">
												<label class="form-label">Total Price</label>
												<input type="number" step="0.01" min="0" name="total_price" class="form-control" value="{{ old('total_price', $formData['total_price'] ?? $booking->total_price) }}">
											</div>
											<div class="col-md-3">
												<label class="form-label">Currency</label>
												<input type="text" maxlength="5" name="currency" class="form-control" value="{{ old('currency', $formData['currency'] ?? $booking->currency) }}">
											</div>
											<div class="col-12">
												<div class="form-check form-check-inline">
													<input type="hidden" name="meet_and_greet" value="0">
													<input class="form-check-input" type="checkbox" id="meet_and_greet" name="meet_and_greet" value="1" {{ old('meet_and_greet', $formData['meet_and_greet'] ?? $booking->meet_and_greet) ? 'checked' : '' }}>
													<label class="form-check-label" for="meet_and_greet">Meet and Greet</label>
												</div>
												<div class="form-check form-check-inline">
													<input type="hidden" name="baby_seat" value="0">
													<input class="form-check-input" type="checkbox" id="baby_seat" name="baby_seat" value="1" {{ old('baby_seat', $formData['baby_seat'] ?? $booking->baby_seat) ? 'checked' : '' }}>
													<label class="form-check-label" for="baby_seat">Baby Seat</label>
												</div>
											</div>
											<div class="col-md-6">
												<label class="form-label">Baby Seat Age</label>
												<input type="text" name="baby_seat_age" class="form-control" value="{{ old('baby_seat_age', $formData['baby_seat_age'] ?? $booking->baby_seat_age) }}">
											</div>
											<div class="col-12">
												<label class="form-label">Message To Driver</label>
												<textarea name="message_to_driver" rows="3" class="form-control">{{ old('message_to_driver', $formData['message_to_driver'] ?? $booking->message_to_driver) }}</textarea>
											</div>
											<div class="col-12 d-flex justify-content-end">
												<button type="submit" class="theme-btn bg-color">Update Booking</button>
											</div>
										</div>
									</form>
								</div>
							</div>
						@endif
					</div>
				</div>
			</div>
		</div>
	</section>

	@include('partials.footer')

	@include('partials.script')
</body>

</html>
