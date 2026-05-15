<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTurniketEventsTable extends Migration
{
    public function up()
    {
        Schema::create('turniket_events', function (Blueprint $table) {
            $table->id();

            // Device this event came from
            $table->string('port', 16)->index();                  // '8003' / '8002'
            $table->enum('direction', ['in', 'out'])->index();    // entry / exit

            // Person identification (only minor=75 events have this)
            $table->string('external_id')->nullable()->index();   // employeeNoString
            $table->string('name')->nullable();
            $table->string('user_type')->nullable();              // normal / visitor

            // Event metadata from Hikvision AcsEvent
            $table->unsignedBigInteger('serial_no');              // device-side unique serial
            $table->dateTime('event_time')->index();
            $table->date('event_date')->index();                  // denormalized for fast daily lookups
            $table->time('event_clock');

            $table->unsignedSmallInteger('major')->nullable();
            $table->unsignedSmallInteger('minor')->nullable();
            $table->unsignedSmallInteger('door_no')->nullable();
            $table->unsignedSmallInteger('card_reader_no')->nullable();
            $table->unsignedSmallInteger('card_type')->nullable();
            $table->string('verify_mode')->nullable();            // currentVerifyMode
            $table->string('mask', 8)->nullable();
            $table->string('picture_url', 1024)->nullable();

            // Full raw payload for forensic / future use
            $table->json('raw')->nullable();

            $table->timestamps();

            // Same serial can repeat across the two devices, so dedupe on (port, serial_no)
            $table->unique(['port', 'serial_no'], 'turniket_events_port_serial_unique');

            // Common query: per employee per day per direction
            $table->index(['external_id', 'event_date', 'direction'], 'turniket_events_emp_date_dir_idx');
        });
    }

    public function down()
    {
        Schema::dropIfExists('turniket_events');
    }
}
