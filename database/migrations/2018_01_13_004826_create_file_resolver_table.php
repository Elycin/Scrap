<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFileResolverTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_resolver', function (Blueprint $table) {
            $table->increments('id');
            $table->string("hash");
            $table->string("mime")->default("application/octet-stream");
            $table->unsignedInteger("size")->default(0);
            $table->unsignedInteger("encrypted_size")->default(0);
            $table->boolean("encrypted")->deafault(false);
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
        Schema::dropIfExists('file_resolver');
    }
}
