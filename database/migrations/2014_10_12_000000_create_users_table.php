<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('usercode', 20)->unique();
            $table->string('name');
            $table->string('password');
            $table->string('email')->unique();
            $table->boolean('is_owner')->default(false);
            $table->string('owner_group', 50)->nullable();
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });



        // Schema::table('users', function($table)
        // {
        //     $table->foreign('usercode')->references('handler')->on('document_mains')->onDelete('cascade'); 
        // });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
