<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('individual_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('public_id')->unique();
            $table->foreignId('creator_id')->references("id")->on('users')->onDelete('cascade');
            $table->foreignId('host_id')->references("id")->on('users')->onDelete('cascade');
            $table->foreignId('deleted_by')->nullable()->references("id")->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('individual_conversations');
    }
};
