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
    Schema::create('work_orders', function (Blueprint $table) {
      $table->id();
      $table->string('reference');
      $table->string('product_name');
      $table->integer('quantity');
      $table->date('due_date');
      $table->enum('status', ['pending', 'in_progress', 'completed', 'canceled']);
      $table->foreignId('operator')->constrained('users')->onDelete('cascade');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('work_orders');
  }
};
