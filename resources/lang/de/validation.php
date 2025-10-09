<?php

return [
	'start_time_required' => 'Startzeit ist erforderlich',
	'end_time_after_start' => 'Endzeit muss nach der Startzeit liegen',
	'pain_level_required' => 'Schmerzstufe ist erforderlich',
	'pain_level_min' => 'Schmerzstufe muss mindestens 1 betragen',
	'pain_level_max' => 'Schmerzstufe darf höchstens 10 betragen',
	'symptom_name_max' => 'Symptomname darf maximal 255 Zeichen lang sein',
	'med_name_max' => 'Mediumname darf maximal 255 Zeichen lang sein',
	'trigger_name_max' => 'Triggername darf maximal 255 Zeichen lang sein',

	// Validation errors for the sending email request
	'recipient_type_required' => 'Empfängertyp erforderlich.',
	'recipient_type_invalid' => 'Ungültiger Empfängertyp ausgewählt.',
	'period_required' => 'Zeitraum erforderlich.',
	'period_invalid' => 'Ungültiger Zeitraum ausgewählt.',
	'doctor_email_required' => 'Beim Senden an den Arzt ist die E-Mail-Adresse des Arztes erforderlich.',
	'doctor_email_invalid' => 'Bitte geben Sie eine gültige E-Mail-Adresse des Arztes ein.',
	'formats_array' => 'Formate müssen ein Array sein.',
	'formats_invalid' => 'Ungültiges Format ausgewählt.',

	'attributes' => [
		'recipient_type' => 'Empfängertyp',
		'period' => 'Zeitraum',
		'doctor_email' => 'E-Mail-Adresse des Arztes',
		'formats' => 'Formate',
	],
];
