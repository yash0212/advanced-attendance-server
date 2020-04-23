<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('applied_by');
            $table->date('date');
            $table->time('out_time');
            $table->time('in_time');
            $table->string('visit_to');
            $table->string('reason');
            $table->integer('status')->default(0);
            $table->integer('approved_by')->nullable();
            $table->timestamp('campus_in_time')->nullable();
            $table->timestamp('campus_out_time')->nullable();
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
        Schema::dropIfExists('outings');
    }
}
