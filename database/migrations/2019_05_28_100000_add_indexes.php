<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexes extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('events', function (Blueprint $table) {
      $table->foreign('narrative_id')->references('id')->on('narratives')->onDelete('cascade');
      $table->index('lat');
      $table->index('lng');
      $table->index('is_start');
    });

    Schema::table('plots', function (Blueprint $table) {
      $table->foreign('previous_event_id')->references('id')->on('events')->onDelete('cascade');
      $table->foreign('next_event_id')->references('id')->on('events')->onDelete('cascade');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('plots', function (Blueprint $table) {
      $table->dropForeign(['previous_event_id']);
      $table->dropForeign(['next_event_id']);
    });

    Schema::table('events', function (Blueprint $table) {
      $table->dropForeign(['narrative_id']);
      $table->dropForeign(['lat']);
      $table->dropForeign(['lng']);
      $table->dropForeign(['is_start']);
    });
  }
}
