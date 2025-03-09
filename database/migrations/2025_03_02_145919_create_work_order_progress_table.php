<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('work_order_progress', function (Blueprint $table) {
      $table->id();
      $table->foreignId('work_order_id')->constrained('work_orders')->onDelete('cascade');
      $table->foreignId('operator')->constrained('users')->onDelete('cascade');
      $table->enum('status', ['pending', 'in_progress', 'completed', 'canceled']);
      $table->integer('quantity')->default(0);
      $table->text('progress_note')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('work_order_progress');
  }
};
