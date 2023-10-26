(function( $ ) {
	'use strict';

	$(function () {
		$('.js-chat-with-gbl').on('click', function (e) {
			e.preventDefault();
			var $this = $(this);
			var href = $this.attr('href');
			var action = 'addCoinsChat';
			var dataToSend = {};
			$.ajax({
				url: '/wp-admin/admin-ajax.php',
				method: 'POST',
				data: Object.assign({}, dataToSend, {
					action: action
				}),
				success: function () {
					setTimeout(function () {
						window.location.href = href;
					}, 500);
				},
				error: function () {
					setTimeout(function () {
						window.location.href = href;
					}, 500);
				}
			});
		});
	});

	$(function () {
		$('.js-coins-to-login').each(function() {
			$(this).on('click', function(){
				window.location.href = window.location.origin + '/sign-in/?refer=coins';
			});
		});
	});

	$(function () {
		$('.js-coins-checkin-button').on('click', function (e) {
			e.preventDefault();
			var $this = $(this);
			var step = $this.data('step');
			var reward = $this.data('reward');
			var nextReward = $this.data('nextreward');
			var currentBalance = $this.data('balance');
			var action = 'addCoinCheckin';
			var dataToSend = {};
			dataToSend['step'] = step;
			dataToSend['reward'] = reward;
			$this.find('.js-coins-checkin-button_text').hide();
			$this.find('.spinner').show();
			$.ajax({
				url: '/wp-admin/admin-ajax.php',
				method: 'POST',
				data: Object.assign({}, dataToSend, {
					action: action
				}),
				success: function() {
					$this.addClass('inactive');
					$('.day-' + step ).removeClass('active').addClass('completed');
					$('.day-' + step).find('span').hide();
					$('.day-' + step).find('.coins-checkin-schedule__day__number').show();
					$('.day-' + step).append('<img src="' + window.location.origin + '/wp-content/plugins/virtual-coins/public/images/icon_double_tick_brown.svg"/>');
					$this.find('.js-coins-checkin-button_text').html('Come back tomorrow to get <strong>' + nextReward + ' tokens</strong>');
					$this.find('.spinner').hide();
					$this.find('.js-coins-checkin-button_text').show();
					$('.section-coins-head').find('h3').text(currentBalance + reward);
					console.log('success');
				},
				error: function() {
					$this.addClass('inactive');
					$('.day-' + step).removeClass('active').addClass('completed');
					$('.day-' + step).find('span').hide();
					$('.day-' + step).append('<img src="' + window.location.origin + '/wp-content/plugins/virtual-coins/public/images/icon_double_tick_brown.svg"/>');
					$this.find('.js-coins-checkin-button_text').html('Come back tomorrow to get <strong>' + nextReward + ' tokens</strong>');
					$this.find('.spinner').hide();
					$this.find('.js-coins-checkin-button_text').show();
					$('.section-coins-head').find('h3').text(currentBalance + reward);
					console.log('error');
				}
			});
		});
	});

	$(function () {
		$('#cart-coins-redeem').each( function () {
			var $this = $(this);
			var $button = $this.find('button');
			$button.on( 'click', function(e) {
				e.preventDefault();
				var action = 'operateCoinsInCart';
				var operation = ''
				if ($button.hasClass('clicked')) {
					operation = 'revert';
				} else {
					operation = 'redeem';
				}

				var dataToSend = {};
				$this.find('[name]').each(function (indx) {
					dataToSend[$(this).attr('name')] = $(this).val();
				});
				if ($button.hasClass('clicked')) {
					$button.removeClass('clicked');
				} else {
					$button.addClass('clicked');
				}

				$.ajax({
					url: '/wp-admin/admin-ajax.php',
					method: 'POST',
					data: Object.assign({}, dataToSend, {
						operation: operation,
						action: action,
					}),
					success: function () {
						console.log('OK');
						location.reload(true);
					},
					error: function () {
						console.log('error');
						location.reload(true);
					}
				});

			});
		});
	});

	$(function() {
		$('.js-cr-nav').each(function() {
			var $this = $(this);
			var $target = $('.' + $this.attr('href'));
			$this.on('click', function(e) {
				e.preventDefault();
				$('.js-cr-nav').removeClass('active');
				$this.addClass('active');
				$('.c-report__item').hide();
				$target.show();
			});
		});
	});

	$(function() {
		$('.js-get-register-tokens').on('click', function(e) {
			e.preventDefault();
			var $this = $(this);
			var action = 'addRegisterCoins';
			var dataToSend = {};
			$this.find('span').hide();
			$this.find('.spinner').show();
			$.ajax({
				url: '/wp-admin/admin-ajax.php',
				method: 'POST',
				data: Object.assign({}, dataToSend, {
					action: action
				}),
				success: function () {
					setTimeout(function () {
						location.reload(true);
					}, 500);
				},
				error: function () {
					setTimeout(function () {
						location.reload(true);
					}, 500);
				}
			});
		});
	});

})( jQuery );
