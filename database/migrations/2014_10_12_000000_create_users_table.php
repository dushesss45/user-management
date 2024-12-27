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
        Schema::create('users', function (Blueprint $table) {
            $table->id()->comment('Уникальный идентификатор пользователя');
            $table->string('name')->comment('Имя пользователя');
            $table->string('email')->unique()->comment('Уникальный адрес электронной почты пользователя');
            $table->string('password')->comment('Хэшированный пароль пользователя');
            $table->string('ip')->nullable()->comment('IP-адрес пользователя');
            $table->text('comment')->nullable()->comment('Комментарий к пользователю');
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
        Schema::dropIfExists('users');
    }
};
