@extends('migrainediary::emails.layout')

@section('header')
	@lang('migrainediary::emails.doctor_report.header')
@endsection

@section('content')
	<p>@lang('migrainediary::emails.doctor_report.introduction', ['name' => $userName .' '. $userLastname])</p>

	<div class="report-summary">
		<p><strong>@lang('migrainediary::emails.labels.period'):</strong> {{ $period }}</p>
		<p><strong>@lang('migrainediary::emails.labels.total_attacks'):</strong> {{ count($data ?? []) }}</p>
	</div>

	@if(!empty($data['summary']))
		<h3>@lang('migrainediary::emails.doctor_report.summary')</h3>
		<ul>
			@foreach($data['summary'] as $key => $value)
				<li><strong>{{ ucfirst($key) }}:</strong> {{ $value }}</li>
			@endforeach
		</ul>
	@endif

	<p>@lang('migrainediary::emails.doctor_report.closing')</p>
@endsection
