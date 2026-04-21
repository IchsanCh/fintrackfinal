<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained()->restrictOnDelete();
            $table->string('order_id')->unique();             // ID unik untuk Midtrans
            $table->unsignedBigInteger('amount');             // snapshot harga saat order
            $table->enum('status', ['pending', 'paid', 'failed', 'expired'])->default('pending');
            $table->string('snap_token')->nullable();         // token dari Midtrans Snap
            $table->json('midtrans_payload')->nullable();     // raw response dari webhook
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
