<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestBreakTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_break_times', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_request_id')
                ->constrained('attendance_requests')
                ->onDelete('cascade'); // 修正申請削除時に関連休憩も削除
            $table->foreignId('attendance_id')
                ->constrained('attendances')
                ->onDelete('cascade'); // 勤怠削除時に休憩リクエストも削除
            $table->dateTime('break_started_at');
            $table->dateTime('break_ended_at')->nullable();
            $table->text('reason');
            $table->string('status', 255);
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('admins')
                ->nullOnDelete(); // 管理者削除時にNULL
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
        Schema::dropIfExists('request_break_times');
    }
}
