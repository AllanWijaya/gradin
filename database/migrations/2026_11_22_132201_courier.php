<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('courier', function (Blueprint $table) {
            $table->id();
            $table->uuid()->default(DB::raw('(UUID())'))->unique();
            $table->string('code');
            $table->string('parent_uuid')->nullable();
            $table->string('grouping');
            $table->string('name');
            $table->integer('level')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courier');
    }
};
