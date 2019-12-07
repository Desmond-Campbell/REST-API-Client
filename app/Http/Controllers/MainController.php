<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\SavedRequest;
use Auth;

class MainController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->http_endpoint = env('HTTP_API_ENDPOINT', 'http://localhost:7007');
    }

    public function index(Request $R) {

        $hash = $R->route('hash');
        $requestid = 0;

        $user_id = Auth::user()->id;

        if ( $hash ) {

            $request = SavedRequest::where('user_id', $user_id)->where( 'hash', $hash )->first();

            if ( $request ) {

                $requestid = $request->id;

            }

        }

        return view('index', compact('requestid'));

    }

    public function request(Request $R) {

        $payload = [ 'request' => $R->input( 'request' ) ];

        $client = new Client( [ 'base_uri' => $this->http_endpoint, 'http_errors' => false, 'stream' => true ] );

        $args = [ 'form_params' => $payload ];

        $response = $client->request( 'POST', '/request/send', $args );

        $result = $response->getBody();

        while (!$result->eof()) {
            echo $result->read(1024);
        }

        if ( json_decode( $result ) ?? null ) {

            $result = json_decode( $result );

        }

        return $result;

    }

    public function getSavedRequest(Request $R){
        
        $id = $R->route('id');

        $user_id = Auth::user()->id;

        $request = SavedRequest::where('user_id', $user_id)->find( $id );

        if ( $request ) {
            $request->options = json_decode( $request->options ?? '{}' );
        }

        return response()->json( $request );

    }

    public function getSavedRequests(Request $R){
        
        $user_id = Auth::user()->id;

        $requests = SavedRequest::where('user_id', $user_id)->get()->each(
            function ( $request ) {
            
                $request->options = json_decode( $request->options ?? '{}' );

            }
        );

        return response()->json( $requests );

    }

    public function storeSavedRequest(Request $R){
        
        $user_id = Auth::user()->id;
        $request = $R->input('request');
        $id = $R->route('id');

        $record = [];
        $record['url'] = $request['url'];
        $record['options'] = json_encode( $request['options'] ?? [] );
        $record['method'] = $request['method'] ?? 'GET';
        $record['user_id'] = $user_id;
        $record['hash'] = SavedRequest::generateHash( $record );
        $record['auth_type'] = $request['auth_type'] ?? '';
        $record['auth_token'] = $request['auth_token'] ?? '';
        $record['auth_username'] = $request['auth_username'] ?? '';
        $record['auth_password'] = $request['auth_password'] ?? '';
        $record['title'] = $request['title'] ?? 'Untitled ' . $record['method'] . ' Request at ' . substr( $record['url'], 0, 32 );
        $record['body_type'] = $request['body_type'] ?? '';
        $record['body'] = $request['body'] ?? '';
        $record['headers'] = $request['headers'] ?? '';
        $record['tags'] = $request['tags'] ?? '';

        if ( $R->input( 'copy') ) { $entry = null; }
        else { $entry = SavedRequest::where('user_id', $user_id)->find( $id ); }

        if ( $entry ) {

            unset( $record['user_id'] );
            unset( $record['hash'] );

            $entry->update( $record );

            $request = $entry;

        } else {

            $request = SavedRequest::create( $record );

        }

        $request->options = json_decode( $request->options );

        // return $request->options;

        return response()->json( $request );

    }

}
