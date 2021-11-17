<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentstoragesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    
    public function up()
    {
        Schema::create('documentstorages', function (Blueprint $table) {
            $table->integer('doc_id')->length(10)->unsigned();
            $table->string('file_path');
            $table->timestamps();
            $table->foreign('doc_id')->references('id')->on('document_mains');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documentstorages');
    }
}
