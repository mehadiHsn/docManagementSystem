<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentMainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        Schema::enableForeignKeyConstraints();
        Schema::create('document_mains', function (Blueprint $table) {
            $table->increments('id');
            $table->string('doc_code', 50);
            $table->string('doc_rev', 10)->default(0);
            $table->string('desc1');
            $table->string('doc_group', 50);
            $table->string('doc_type', 50);
            $table->string('doc_class', 50);
            $table->string('handler', 20);
            $table->string('doc_info', 255)->nullable();
            $table->string('doc_status', 50);
            //  $table->integer('storage_id')->length(10)->unsigned;
            $table->unique(['doc_code', 'doc_rev']);
            $table->timestamps();
        });


        Schema::table('document_mains', function ($table) {
            $table->foreign('handler')
                ->references('usercode')
                ->on('users')
                ->onDelete('cascade')->onUpdate('cascade');
            // $table->foreign('doc_group')->references('group_code')->on('documentgroups')->onDelete('cascade')->onUpdate('cascade');
            // $table->foreign('doc_type')->references('doc_type')->on('documenttypes')->onDelete('cascade')->onUpdate('cascade');
            // $table->foreign('doc_status')->references('status_code')->on('documentstatuses')->onDelete('cascade')->onUpdate('cascade');
        });
        // Schema::table('document_mains',function($table)
        // {
        //     $table->foreign('doc_group')->references('id')->on('documentgroups')->onDelete('cascade')->onUpdate('cascade');
        // });

    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('document_mains');
    }
}
