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
				<div class="panel-heading">Steve Thinker</div>
				<div class="panel-body">
					<div id="response_cnt">
					</div>
					<form action method="post">
						<input type="text" id="text_input" class="form-control" autocomplete="off" />
						<input type="submit" id="submit_input" class="btn btn-success" />
					</form>
					<h2>Language Key</h2>
					<div class="row">
						<div class="col-md-3">
							<p>join words <strong> _ </strong></p>
							<p>nouns <strong> - </strong></p>
							<p>verbs <strong> > </strong></p>
							<p>adjectives <strong> : </strong></p>
						</div>
						<div class="col-md-3">
							<p>prepositions <strong> + </strong></p>
							<p>conjunction <strong> & </strong></p>
							<p>Determiner <strong> @ </strong></p>
							<p>Exclamations <strong> # </strong></p>
						</div>
						<div class="col-md-3">
							<p>Adverbs <strong> ; </strong></p>
							<p>Pronouns <strong> = </strong></p>
							<p>Interjections <strong> $ </strong></p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('/js') }}/interface_script.js"></script>
@endsection