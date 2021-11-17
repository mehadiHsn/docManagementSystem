<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateViewdocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('viewdocuments', function (Blueprint $table) {
            $table->Integer('doc_id')->unsigned()->unique();
            $table->string('desc1');
            $table->string('doc_info', 255)->nullable();
            $table->string('doc_code', 50);
            $table->string('doc_rev', 10);
            $table->string('group_desc');
            $table->string('type_code');
            $table->string('status_desc');
            $table->string('usercode');
            $table->string('handler');
            $table->string('file_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('viewdocuments');
    }
}
