<?php

use Illuminate\Database\Seeder;
use App\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
    	$settings = [];
    	$settings[] = [ 'key' => 'strip_laravel_error_header', 'value' => 1 ];
    	$settings[] = [ 'key' => 'extract_html_body_from_response', 'value' => 1 ];

        foreach ( $settings as $setting ) {

        	Setting::create( $setting );

        }

    }
}
