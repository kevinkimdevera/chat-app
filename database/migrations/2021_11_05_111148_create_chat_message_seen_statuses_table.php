<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatMessageSeenStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_message_seen_statuses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('message_id');
            $table->unsignedBigInteger('user_id');
            $table->dateTime('seen_at')->nullable();
            $table->timestamps();
        });

        Schema::table('chat_message_seen_statuses', function (Blueprint $table) {
          $table->foreign('message_id')
            ->references('id')
            ->on('chat_messages')
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
        Schema::dropIfExists('chat_message_seen_statuses');
    }
}
