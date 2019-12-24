@extends('layouts.app')

@section('content')

<div class="container-fluid" ng-controller="HttpController">

	<div class="row">

		<div class="col-md-12">
			<button class="btn btn-sm btn-default" ng-click="switchMode()"><span ng-if="expanded">Hide</span><span ng-if="normal">Show</span> Sidebar</button> 
			<span>@{{ status }}</span>
		</div>

		<hr />

		<div ng-class="{ 'hidden' : normal, 'col-md-2' : expanded }">
			<ul class="request-list">
				<li ng-repeat="savedRequest in savedRequests">
					<a href="/r/@{{ savedRequest.hash }}">@{{savedRequest.title}}</a>
				</li>
			</ul>
		</div>

		<div ng-class="{ 'col-md-5' : normal, 'col-md-4' : expanded }">

			<div class="form-group">
			
				<p>End-point:</p>
				<input type="text" class="form-control" ng-model="request.url" />

			</div>

			<div class="form-group">

				<p>Body:</p>

				<select class="form-control" ng-model="request.body_type">
					<option value="">None</option>
					<option value="form">Form/Url Encoded</option>
					<option value="json">JSON</option>
				</select>
				
				<textarea ng-model="request.body" class="form-control" rows="6" ng-if="request.body_type"></textarea>

			</div>

			<div class="form-group">

				<p>Headers:</p>

				<textarea ng-model="request.headers" class="form-control" rows="6"></textarea>

			</div>

			<div class="form-group">
				<button class="btn btn-success" ng-click="send(request)">Send</button>
				&nbsp; <button class="btn btn-primary" ng-click="save(request)">Save</button>
				&nbsp; <button class="btn btn-deafult" ng-click="saveAs(request)">Save As</button>
				&nbsp; <button class="btn btn-warning" ng-click="clear(request)">Clear</button>
			</div>

			<hr />

			<div class="form-group">
				<p>
					<input type="checkbox" id="show_http_errors" ng-model="request.options.show_http_errors" value="true" /><label for="show_http_errors">Show HTTP Errors</label>
				</p>
			</div>

			<div class="form-group">
			
				<p>Method:</p>
				<select class="form-control" ng-model="request.method">
					<option value="GET">GET</option>
					<option value="POST">POST</option>
					<option value="PUT">PUT</option>
					<option value="PATCH">PATCH</option>
					<option value="DELETE">DELETE</option>
				</select>

			</div>

			<div class="form-group">

				<p>Authorisation:</p>

				<select class="form-control" ng-model="request.auth_type">
					<option value="">None</option>
					<option value="bearer">Bearer Token</option>
					<option value="basic">Basic Auth</option>
				</select>

			</div>

			<div class="form-group" ng-if="request.auth_type == 'bearer'">
				<p>Token:</p>
				<input type="text" class="form-control" ng-model="request.auth_token" />
			</div>
			
			<div class="form-group" ng-if="request.auth_type == 'basic'">
				<p>Username:</p>
				<input type="text" class="form-control" ng-model="request.auth_username" />
				<p>Password:</p>
				<input type="password" class="form-control" ng-model="request.auth_password" />
			</div>

			<div class="form-group">
			
				<p>Title:</p>
				<input type="text" class="form-control" ng-model="request.title" />

			</div>

			<div class="form-group">
			
				<p>Tags:</p>
				<input type="text" class="form-control" ng-model="request.tags" />

			</div>

			<div class="form-group">
				<button class="btn btn-success" ng-click="send(request)">Send</button>
				&nbsp; <button class="btn btn-primary" ng-click="save()">Save</button>
				&nbsp; <button class="btn btn-warning" ng-click="clear()">Clear</button>
			</div>

		</div>

		<div ng-class="{ 'col-md-7' : normal, 'col-md-6' : expanded }">
			<div id="result" style="max-height: 90%; overflow-y: auto; overflow-x: none; overflow-wrap: break-word;">
				<pre id="json_result" style="display: none">JSON @{{result}}</pre>
				<pre id="text_result" style="display: none">TEXT @{{result}}</pre>
				<div id="html_result" style="display: none">HTML @{{result}}</div>
			</div>
		</div>

		<input type="hidden" id="requestid" value="{{ $requestid }}" />

	</div>

</div>

@stop

@section('javascript')

<script>
app.controller("HttpController", [ '$scope', '$http', function ($scope, $http) {

	$scope.request = { 
	// 					'method': 'GET',
	// 					'url' : 'http://bayshore.grape.loancirrus.com:92/api/v1/clients/4471/loans',
	// 					'auth_type' : 'token',
	// 					'auth_token' : 'qtjJrpcnoskomQx0I62BXZuJcTwhkW6GGXzV6ecDS3V6Y1Zo9aFcShTOnfWN' 
						options : {}
					};

	$scope.normal = false;
	$scope.expanded = true;

	$scope.switchMode = function() {
		$scope.normal = !$scope.normal;
		$scope.expanded = !$scope.expanded;
	};

	$scope.getSavedRequests = function (request) {

		url = '/saved/get';

		$http.get(url).success(
			function(response){
				$scope.savedRequests = response;
			}
		).error(
			function(error){
				console.log(error)
			}
		);
	};

	$scope.getSavedRequests();

	$scope.loadSavedRequest = function() {
		
		id = jQuery('#requestid').val();

		if ( id ) {

			url = '/saved/' + id + '/get';

			$http.get(url).success(
				function(response){
					$scope.request = response;
				}
			).error(
				function(error){
					console.log(error)
				}
			);

		}

	};

	$scope.loadSavedRequest();

	$scope.saveAs = function () {
		$scope.copy = true;
		$scope.save();
	};

	$scope.save = function() {
		
		id = jQuery('#requestid').val();

		url = '/saved/' + id + '/store';

		$scope.status = 'Saving request';

		$payload = { request: $scope.request };

		if ( $scope.copy ) {
			$scope.copy = false;
			$payload.copy = true;
		}

		$http.post(url, $payload).success(
			function(response){
				$scope.request = response;
				$scope.status = 'Request saved';
				jQuery('#requestid').val(response.id);
				$scope.getSavedRequests();
			}
		).error(
			function(error){
				$scope.status = 'Save failed';
			}
		);

	};

	$scope.clearDisplayFor = function ( target ) {

		jQuery("#json_result").fadeOut('fast');
		jQuery("#html_result").fadeOut('fast');
		jQuery("#text_result").fadeOut('fast');

		jQuery('#' + target + "_result").fadeIn('fast');

	};

	$scope.send = function (request) {

		url = '/request/send';

		$scope.status = 'Sending request';

		$http.post(url, { request : request }).success(
			function(response){

				var hasResult = false;

				if ( typeof response.format !== 'undefined' ) {

					$scope.result = response.result;

					if ( response.format == 'json' ) {

						document.getElementById("json_result").innerHTML = JSON.stringify(response.result, undefined, 2);
						$scope.clearDisplayFor('json');

						hasResult = true;

					} else if ( response.format == 'html' ) {

						document.getElementById("html_result").innerHTML = $scope.result;
						$scope.clearDisplayFor('html');

						hasResult = true;

					} else if ( response.format == 'text' ) {

						document.getElementById("text_result").innerText = $scope.result;
						$scope.clearDisplayFor('text');

						hasResult = true;

					}

				} else {
				
					$scope.result = response;

				}

				if ( !hasResult ) {

					$scope.clearDisplayFor('html');
					document.getElementById("html_result").innerHTML = $scope.result;
					hasResult = true;

				}

				$scope.status = 'Request completed';
			}
		).error(
			function(error){
		
				$scope.status = 'Sending failed';
				
			}
		);
	};

	$scope.clear = function() {
		location.assign('/?fresh');
	};

}]);
</script>

@stop