<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSavedRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('saved_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('hash', 8)->unique();
            $table->string('title');
            $table->string('tags')->nullable();
            $table->bigInteger('user_id')->unsigned();
            $table->string('url');
            $table->string('method', 10)->nullable()->default('GET');
            $table->string('auth_type')->nullable();
            $table->string('auth_token')->nullable();
            $table->string('auth_username')->nullable();
            $table->string('auth_password')->nullable();
            $table->string('body_type')->nullable();
            $table->text('headers')->nullable();
            $table->longText('body')->nullable();
            $table->text('extras')->nullable();
            $table->tinyInteger('scope')->nullable()->default(1)->comment('1:private, 2:shareable, 3:public');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDeleteCascade();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('saved_requests');
    }
}
