<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->id();
            $table->string('uid')->unique();
            $table->string('public_uid')->uniqid();
            $table->string('username')->unique()->fullText();
            $table->string('fname')->fullText();
            $table->string('sname')->fullText();
            $table->string('email')->unique()->fullText();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('gender');
            $table->date('bdate');
            $table->string('avatar')->default('default.png');
            $table->string('descriptions', 500)->nullable();
            $table->boolean('is_locked')->default(false);
            $table->boolean('is_active')->default(false);
            $table->string('status')->nullable();
            $table->rememberToken();
            $table->boolean('is_restorable')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->index(['created_at', 'updated_at']);
        });
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
