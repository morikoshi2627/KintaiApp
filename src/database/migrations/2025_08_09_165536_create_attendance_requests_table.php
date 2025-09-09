<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendanceRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade'); // ユーザー削除時にリクエストも削除
            $table->foreignId('attendance_id')
                ->constrained('attendances')
                ->onDelete('cascade'); // 勤怠削除時にリクエストも削除
            $table->date('request_date');
            $table->time('requested_start_time')->nullable();
            $table->time('requested_end_time')->nullable();
            $table->string('request_type', 50);
            $table->text('request_reason');
            $table->string('status', 50);
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
        Schema::dropIfExists('attendance_requests');
    }
}
