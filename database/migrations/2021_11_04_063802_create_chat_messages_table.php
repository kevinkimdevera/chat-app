<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('chat_messages', function (Blueprint $table) {
          $table->id();
          $table->unsignedBigInteger('chat_id');
          $table->unsignedBigInteger('sender_id');
          $table->longText('message');
          $table->timestamps();
          $table->softDeletes();
      });

      Schema::table('chat_messages', function (Blueprint $table) {
        $table->foreign('chat_id')
          ->references('id')
          ->on('chats')
          ->onDelete('cascade')
          ->onUpdate('cascade');

        $table->foreign('sender_id')
          ->references('id')
          ->on('users')
          ->onDelete('cascade')
          ->onUpdate('cascade');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chat_messages');
    }
}
