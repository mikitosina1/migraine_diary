<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>@yield('title', 'Migraine Report')</title>
	<style>
		body {
			font-family: Arial, sans-serif;
			background-color: #f7f7f7;
			margin: 0;
			padding: 0;
			color: #333;
		}
		.email-wrapper {
			max-width: 600px;
			margin: 30px auto;
			background: #fff;
			border-radius: 10px;
			overflow: hidden;
			box-shadow: 0 2px 8px rgba(0,0,0,0.1);
		}
		.email-header {
			background-color: #4a90e2;
			color: white;
			padding: 20px;
			text-align: center;
		}
		.email-content {
			padding: 25px;
			line-height: 1.6;
		}
		.report-summary {
			background-color: #f0f4f8;
			border-left: 4px solid #4a90e2;
			padding: 10px 15px;
			margin: 15px 0;
			border-radius: 6px;
		}
		.email-footer {
			font-size: 12px;
			color: #777;
			text-align: center;
			padding: 15px;
			background: #f9f9f9;
		}
	</style>
</head>
<body>
<div class="email-wrapper">
	<div class="email-header">
		<h2>@yield('header', 'Migraine Diary')</h2>
	</div>

	<div class="email-content">
		@yield('content')
	</div>

	<div class="email-footer">
		<p>&copy; {{ date('Y.m.d') }} mikitosina migraine diary. @lang('migrainediary::emails.footer_disclaimer')</p>
	</div>
</div>
</body>
</html>
