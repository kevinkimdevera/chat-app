<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatParticipantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('chat_participants', function (Blueprint $table) {
          $table->id();
          $table->unsignedBigInteger('chat_id');
          $table->unsignedBigInteger('user_id');
          $table->boolean('accepted')->default(false);
          $table->timestamps();
          $table->softDeletes();
      });

      Schema::table('chat_participants', function (Blueprint $table) {
        $table->foreign('chat_id')
          ->references('id')
          ->on('chats')
          ->onDelete('cascade')
          ->onUpdate('cascade');

        $table->foreign('user_id')
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
        Schema::dropIfExists('chat_participants');
    }
}
