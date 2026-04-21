<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->enum('tier', ['free', 'premium', 'sultan'])->unique();
            $table->string('name');                          // "Free", "Premium", "Sultan"
            $table->unsignedBigInteger('price')->default(0); // dalam rupiah, 0 = gratis
            $table->unsignedInteger('duration_days')->default(30);
            $table->unsignedInteger('max_accounts')->nullable();      // null = unlimited
            $table->unsignedInteger('max_saving_goals')->nullable();  // null = unlimited
            $table->unsignedInteger('max_budgets')->nullable();       // null = unlimited
            $table->boolean('can_export')->default(false);
            $table->unsignedInteger('ai_rate_limit')->nullable();     // per bulan, null = unlimited
            $table->boolean('has_priority_support')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
