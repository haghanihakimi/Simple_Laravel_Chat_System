<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('public_id')->unique();
            $table->foreignId('first_user')->references("id")->on('users')->onDelete('cascade');
            $table->foreignId('second_user')->references("id")->on('users')->onDelete('cascade');
            $table->boolean('is_spam')->default(false);
            $table->enum('status', ['pending', 'accepted', 'cancelled', 'rejected', 'removed']);
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
        Schema::dropIfExists('contacts');
    }
}
