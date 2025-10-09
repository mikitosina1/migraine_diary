<?php

return [
	'start_time_required' => 'Start time is required',
	'end_time_after_start' => 'End time must be after start time',
	'pain_level_required' => 'Pain level is required',
	'pain_level_min' => 'Pain level must be at least 1',
	'pain_level_max' => 'Pain level must be at most 10',
	'symptom_name_max' => 'Symptom name must be at most 255 characters',
	'med_name_max' => 'Med name must be at most 255 characters',
	'trigger_name_max' => 'Trigger name must be at most 255 characters',

	// Validation errors for the sending email request
	'recipient_type_required' => 'Recipient type is required.',
	'recipient_type_invalid' => 'Invalid recipient type selected.',
	'period_required' => 'Period is required.',
	'period_invalid' => 'Invalid period selected.',
	'doctor_email_required' => 'Doctor email is required when sending to doctor.',
	'doctor_email_invalid' => 'Please enter a valid doctor email address.',
	'formats_array' => 'Formats must be an array.',
	'formats_invalid' => 'Invalid format selected.',

	'attributes' => [
		'recipient_type' => 'recipient type',
		'period' => 'period',
		'doctor_email' => 'doctor email',
		'formats' => 'formats',
	],
];
