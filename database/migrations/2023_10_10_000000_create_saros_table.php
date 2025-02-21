<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSarosTable extends Migration
{
    public function up()
    {
        Schema::create('saros', function (Blueprint $table) {
            $table->id();
            $table->string('saro_number');
            $table->decimal('budget', 15, 2);
            $table->integer('year');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('saros');
    }
}
