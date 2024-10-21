jQuery(document).ready(function($) {

	const forms = document.querySelectorAll('.hub-booking-form');

	forms.forEach(form => {
		const datepicker = form.querySelector('input[name="datepicker"]');
		const options = JSON.parse(datepicker.getAttribute('data-options'));

		if ( form.querySelector('[name="booking_type"]').value === 'slot' ) {
			var onSelect = ({ date, datepicker }) => {
				var new_date = new Date(date);
				var days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
				var dayName = days[new_date.getDay()];
				get_booking_slots_data(form, dayName);
			};
		} else {
			var onSelect = ({ date, datepicker }) => {
				if ( date.length == 2 ){
					get_booking_data(form);
				}
			};
		}
		options.onSelect = onSelect;
		options.minDate = new Date();
		new AirDatepicker(datepicker, options);

		// Form submit
		form.addEventListener('submit', function(e) {
            e.preventDefault();

			$.ajax({
				type: 'POST',
				url: liquidTheme.uris.ajax,
				data: {
					action: 'booking_set_data',
					first_name: form.querySelector('[name="first_name"]')?.value || '',
					last_name: form.querySelector('[name="last_name"]')?.value || '',
					phone: form.querySelector('[name="phone"]')?.value || '',
					email: form.querySelector('[name="email"]')?.value || '',
					adult: form.querySelector('[name="adult"]')?.value || '',
					child: form.querySelector('[name="child"]')?.value || '',
					booking_date: form.querySelector('[name="datepicker"]')?.value || '',
					time: form.querySelector('[name="time"]')?.value || '',
					message: form.querySelector('[name="message"]')?.value || '',
					disabled_inputs: form.querySelector('[name="disabled_inputs"]')?.value || '',
					booking_type: form.querySelector('[name="booking_type"]')?.value || '',
					nonce: form.querySelector('[name="nonce"]')?.value || ''
				},
				beforeSend: function(){
					form.classList.add('form-loading');
				},
				success: function(response){
					form.classList.remove('form-loading');
					if ( response.error && response.alert ) {
						alert( response.alert );
					} else {
						form.classList.add('form-done');
						form.querySelector('.hub-booking-form-message').innerHTML = response.out;
					}
				},
			});
		});

		form.querySelectorAll('[name="adult"], [name="child"]').forEach(input => {
			input.addEventListener('change', () => get_booking_data(form))
		});

	});


	/**
	 *
	 * @param {HTMLFormElement} form
	 */
    function get_booking_data(form){
        $.ajax({
            type: 'POST',
            url: liquidTheme.uris.ajax,
            data: {
                action: 'booking_get_data',
                adult: form.querySelector('[name="adult"]')?.value || '',
                child: form.querySelector('[name="child"]')?.value || '',
                booking_date: form.querySelector('[name="datepicker"]')?.value || '',
                nonce: form.querySelector('[name="nonce"]')?.value || ''
            },
            beforeSend: function(){
                form.classList.remove('form-init');
				form.classList.add('form-loading');
                form.querySelector('p.error')?.remove();
            },
            success: function(response) {
                form.classList.remove('form-loading');
                if ( response.out ) {
                    form.classList.remove('form-loaded');
                    if (form.querySelector('span.price') ) {
                        form.querySelector('span.price').innerHTML = response.out;
                    }
                }

                if ( response.error && response.alert ) {
                    console.log( response.alert );
                }
            }
        });
    }

	/**
	 *
	 * @param {HTMLFormElement} form
	 * @param {string} dayName
	 */
    function get_booking_slots_data(form, dayName){
        $.ajax({
            type: 'POST',
            url: liquidTheme.uris.ajax,
            data: {
                action: 'get_available_slots',
                booking_date: form.querySelector('[name="datepicker"]')?.value || '',
                time: form.querySelector('[name="time"]')?.value || '',
                day_name: dayName,
                nonce: form.querySelector('[name="nonce"]')?.value || ''
            },
            beforeSend: function(){
                form.classList.remove('form-init');
				form.classList.add('form-loading');
                form.querySelector('p.error')?.remove();
                if ( form.querySelector('[name="time"]') ) {
                    form.querySelector('[name="time"]').innerHTML = '';
                }
            },
            success: function(response) {
                form.classList.remove('form-loading');
                if ( response.out ) {
                    form.classList.remove('form-loaded');
					form.querySelector('[name="time"]').innerHTML = response.out;
                }

                if ( response.error && response.alert ) {
                    alert( response.alert );
					if ( response.out ) {
						form.querySelector('[name="time"]').innerHTML = response.out;
					}
                }
            }
        });
    }

});
