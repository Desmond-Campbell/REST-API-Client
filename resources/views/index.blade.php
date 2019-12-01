@extends('layouts.app')

@section('content')

<div class="row" ng-controller="HttpController">

	<div class="col-md-12">
		<a href="" ng-click="switchMode()"><span ng-if="expanded">Hide</span><span ng-if="normal">Show</span> Sidebar</a> 
		<span>@{{ status }}</span>
	</div>

	<div ng-class="{ 'hidden' : normal, 'col-md-2' : expanded }">
		<ul>
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
			&nbsp; <button class="btn btn-warning" ng-click="clear(request)">Clear</button>
		</div>

		<hr />

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
			&nbsp; <button class="btn btn-primary" ng-click="save(request)">Save</button>
			&nbsp; <button class="btn btn-warning" ng-click="clear(request)">Clear</button>
		</div>

	</div>

	<div ng-class="{ 'col-md-7' : normal, 'col-md-6' : expanded }">
		<div id="result" style="max-height: 90%; overflow-y: auto">
			<pre id="json_result">@{{result}}</pre>
		</div>
	</div>

	<input type="hidden" id="requestid" value="{{ $requestid }}" />

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

	$scope.save = function() {
		
		id = jQuery('#requestid').val();

		url = '/saved/' + id + '/store';

		$scope.status = 'Saving request';

		$http.post(url, { request: $scope.request }).success(
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

	$scope.send = function (request) {

		url = '/request/send';

		$scope.status = 'Sending request';

		$http.post(url, { request : request }).success(
			function(response){
				$scope.result = response;
				document.getElementById("json_result").innerHTML = JSON.stringify(response, undefined, 2);
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