@extends('layout')

@section('styles')
<link href="{{ asset('/css/interface_style.css') }}" rel="stylesheet">
<link href='http://fonts.googleapis.com/css?family=Roboto:700' rel='stylesheet' type='text/css'>
@endsection

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">Interface</div>

				<div class="panel-body">
					<p>Steve Thinker</p>
					<div id="response_cnt">
					</div>
					<form action method="post">
						<input type="text" id="text_input" class="form-control" autocomplete="off" />
						<input type="submit" id="submit_input" class="btn btn-success" />
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('/js') }}/interface_script.js"></script>
@endsection