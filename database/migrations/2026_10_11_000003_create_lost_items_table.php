<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lost_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('category_id')->constrained()->restrictOnDelete();
            $t->string('title', 120);
            $t->text('description');
            $t->string('location', 150);
            $t->date('event_date');
            $t->string('contact', 30);
            $t->string('photo')->nullable();
            $t->string('status', 20)->default('hilang');
            $t->timestamps();
            $t->index(['status', 'event_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lost_items');
    }
};
