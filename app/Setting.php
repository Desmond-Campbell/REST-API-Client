<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{

	protected $table = 'settings';
	protected $fillable = ['key', 'value', 'tags'];

  	public static function set($key, $value, $tags = '')
    {
        $data = [ 'key' => $key, 'value' => $value, 'tags' => $tags ];

        return self::updateOrCreate(['key' => $key], $data);
    }

    public static function stepUp($key)
    {
        return self::step($key, 1);
    }

    public static function step($key, $value)
    {
        $option = self::where('key', $key)->first();
        $value = 0;

        if ($option) {
            $value = intval($option->value);
        }

        return self::set($key, $value + 1);
    }
    
    public static function get_raw($key)
    {
        return self::where('key', $key)->first();
    }

    public static function get($key, $default = '')
    {
        $option = self::get_raw($key);

        if ($option) {
            return $option->value;
        } else {
            return $default;
        }
    }

    public static function kill($key, $tags = null)
    {
        
        if ( $tags ) return self::where( 'key', $key )->where( 'tags', $tags )->delete();
        
        return self::where( 'key', $key )->delete();

    }
}
