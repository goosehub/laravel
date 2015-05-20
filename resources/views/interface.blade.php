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
						<div class="col-md-4">
							<div class="explain_bubble">
								<p><strong class="part_symbol"> = </strong>Nouns </p>
								<small>=Steve =Paris =water =people =time</small>
							</div>
							<div class="explain_bubble">
								<p><strong class="part_symbol"> _ </strong>Join Words </p>
								<small>=William_Shakespear</small>
							</div>
							<div class="explain_bubble">
								<p><strong class="part_symbol"> ; </strong>Verbs </p>
								<small>;do ;does ;make ;go ;is </small>
							</div>
							<div class="explain_bubble">
								<p><strong class="part_symbol"> * </strong>Adjectives </p>
								<small>*good *bad *beautiful *fourtytwo *7</small>
							</div>
						</div>
						<div class="col-md-4">
							<div class="explain_bubble">
								<p><strong class="part_symbol"> @ </strong>Prepositions of Time</p>
								<small>@at @on</small>
							</div>
							<div class="explain_bubble">
								<p><strong class="part_symbol"> # </strong>Prepositions of Space</p>
								<small>#at #on</small>
							</div>
							<div class="explain_bubble">
								<p><strong class="part_symbol"> $ </strong>Prepositions of Relation</p>
								<small>$of $with $like</small>
							</div>
							<div class="explain_bubble">
								<p><strong class="part_symbol"> ~ </strong>Inquiry </p>
								<small>~how ~why ~does ~will</small>
							</div>
						</div>
						<div class="col-md-4">
							<div class="explain_bubble">
								<strong class="part_symbol"> + or - </strong><p>Exclamation </p>
								<small>+yes -no +yay -boo</small>
							</div>
							<div class="explain_bubble">
								<strong class="part_symbol"> ` </strong><p>Article </p>
								<small>`the `a `an</small>
							</div>
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