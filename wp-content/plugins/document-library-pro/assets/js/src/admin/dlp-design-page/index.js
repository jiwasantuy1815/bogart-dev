/**
 * Handle the reset button click to re-apply template defaults
 */
jQuery(document).ready(function ($) {
	// Reset button for table template
	$(document).on(
		'click',
		'.forminp-reset_table_settings button',
		function (event) {
			event.preventDefault();

			const $selectedTemplate = $(
				'input[name="default_table_template"]:checked'
			);
			if ($selectedTemplate.length) {
				const $templateLabel = $selectedTemplate.closest('label');
				const $otherTemplates = $(
					'input[name="default_table_template"]:not(:checked)'
				);
				if ($otherTemplates.length) {
					$otherTemplates.first().closest('label').click();
					$templateLabel.click();
				} else {
					$templateLabel.click();
				}
			}
		}
	);

	$(document).on(
		'click',
		'.forminp-reset_grid_settings button',
		function (event) {
			event.preventDefault();

			const $selectedTemplate = $(
				'input[name="default_grid_template"]:checked'
			);
			if ($selectedTemplate.length) {
				const $templateLabel = $selectedTemplate.closest('label');
				const $otherTemplates = $(
					'input[name="default_grid_template"]:not(:checked)'
				);
				if ($otherTemplates.length) {
					$otherTemplates.first().closest('label').click();
					setTimeout(function () {
						$templateLabel.click();
					}, 50);
				} else {
					$templateLabel.click();

					setTimeout(function () {
						$templateLabel.click();
					}, 50);
				}
			}
		}
	);

	// Select the first template when there is no template selected
	$(document).on('change', '.forminp-table_design input', function () {
		if ($(this).val() === 'custom') {
			const $selectedTemplate = $(
				'input[name="default_table_template"]:checked'
			);
			if (!$selectedTemplate.length) {
				const $firstTemplate = $(
					'input[name="default_table_template"]'
				).first();
				$firstTemplate.closest('label').click();
			}
		}
	});

	// Click on the first template as the default one
	$(document).on('change', '.forminp-grid_design input', function () {
		if ($(this).val() === 'custom') {
			const $selectedTemplate = $(
				'input[name="default_grid_template"]:checked'
			);

			if (!$selectedTemplate.length) {
				const $firstTemplate = $(
					'input[name="default_grid_template"]'
				).first();
				$firstTemplate.closest('label').click();
			}
		}
	});
});
