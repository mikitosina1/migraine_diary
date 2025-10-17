<?php

return [
	// Validation errors for creating or changing attack
	'attack' => [
		'start_time_required' => 'Время начала обязательно',
		'end_time_after_start' => 'Время окончания обязательно должно быть после времени начала',
		'pain_level_required' => 'Уровень боли обязательно',
		'pain_level_min' => 'Уровень боли должен быть не менее 1',
		'pain_level_max' => 'Уровень боли должен быть не более 10',
		'symptom_name_max' => 'Название симптома должно быть не более 255 символов',
		'med_name_max' => 'Название медикамента должно быть не более 255 символов',
		'trigger_name_max' => 'Название триггера должно быть не более 255 символов',
	],

	// Validation errors for the sending email request
	'email' => [
		'recipient_type_required' => 'Требуется указать тип получателя.',
		'recipient_type_invalid' => 'Выбран неверный тип получателя.',
		'period_required' => 'Требуется указать период.',
		'period_invalid' => 'Выбран неверный период.',
		'doctor_email_required' => 'При отправке врачу требуется указать адрес электронной почты врача.',
		'doctor_email_invalid' => 'Введите действительный адрес электронной почты врача.',
		'formats_array' => 'Форматы должны быть массивом.',
		'formats_invalid' => 'Выбран неверный формат.',
		'user_name_max' => 'Имя не должно превышать 64 символа.',
		'user_lastname_max' => 'Фамилия не должна превышать 64 символа.',
	],

	'attributes' => [
		'recipient_type' => 'тип получателя',
		'period' => 'период',
		'doctor_email' => 'электронная почта врача',
		'formats' => 'форматы',
	],
];
