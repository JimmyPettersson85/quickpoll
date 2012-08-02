@layout('templates.main')

@section('title')
	QuickPoll - view
@endsection

@section('content')
	<div class="hero-unit">
		<h1>{{ $poll->question }}</h1><br />
		<h3 style="color: grey;">{{ $count.' '.Str::plural('vote', $count) }}<h3><br />

		{{ Form::open() }}
			@if (Session::has('errors'))
				@foreach($errors as $error)
					<span class="alert-error">{{ $error }}</span><br />
				@endforeach
			@endif

			{{ Form::hidden('poll_id', $poll->id) }}

			@foreach ($poll->alternatives as $alt)
				<?php $percent = $alt->votes ? round($alt->votes/$count, 2) : 0; ?>
				<div style="margin-bottom: 10px;">
					<p style="margin-bottom: 0px;">{{ Form::radio('alternative', $alt->id) }} {{ $alt->alternative }} ({{ 100 * $percent }}%)</p>
					<div style="width: 200px; height: 15px; border: 1px solid #ccc;">
						<div style=" width: {{ 200 * $percent }}px; height:100%; background: grey;"></div>
					</div>
				</div>
			@endforeach

			<p>{{ Form::submit('Vote!', array('class' => 'btn btn-primary')) }}</p>
		{{ Form::close() }}
	</div>
@endsection