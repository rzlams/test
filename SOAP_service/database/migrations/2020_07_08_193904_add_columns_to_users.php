<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	Schema::table('users', function (Blueprint $table) {
    	    $table->string('documento', 20)->nullable(false);
    	    $table->string('celular', 20)->nullable(false);
    	    $table->string('user_agent')->nullable();
    	    $table->json('geoip')->nullable();
    	    $table->boolean('confirmed');

            $table->softDeletes();
    	    $table->unique(['documento', 'celular']);
	});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
    	    $table->dropColumn('documento');
    	    $table->dropColumn('celular');
    	    $table->dropColumn('user_agent');
    	    $table->dropColumn('geoip');

    	    $table->dropUnique(['documento', 'celular']);
        });
    }
}
