<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SavedRequest extends Model
{
    protected $guarded = [ 'id' ];

    public static function generateHash( $record ) {

    	$salt = 1;
    	$redoHash = true;
    	$hash = '';

    	while( $redoHash ) {

	    	$hash = self::newHash( $record, $salt );

	    	$existing_hash = self::where('hash', $hash)->count();

	    	if ( !$existing_hash ) {
	    		
	    		$redoHash = false;

	    	} else {
	    		
	    		$salt++;

	    		if ( $salt > 10 ) {
	    			die("System error.");
	    		}

	    	}

	    }

	    return $hash;

    }

    public static function newHash( $record, $salt ) {

    	if ( $salt ) $record['salt'] = $salt;

    	$newhash = dechex( crc32( json_encode( $record ) ) );

    	if ( strlen( $newhash ) < 8 ) {

    		while( strlen( $newhash ) < 8 ) {

    			$newhash .= rand( 0, 9 );

    		}

    	} elseif ( strlen( $newhash ) > 8 ) {

    		$newhash = substr( $newhash, 0, 8 );

    	}

    	return $newhash;

    }

}
