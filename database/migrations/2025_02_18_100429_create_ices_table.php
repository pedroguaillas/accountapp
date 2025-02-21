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
        Schema::create('pay_methods', function (Blueprint $table) {
            $table->id();
            $table->integer('code')->unique('code');;
            $table->string('name');
            $table->decimal('max')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('ivas', function (Blueprint $table) {
            $table->id();
            $table->integer('code')->unique();
            $table->string('name');
            $table->decimal('percentage');
            $table->timestamps();
        });
        
        Schema::create('ices', function (Blueprint $table) {
            $table->id();
            $table->integer('code')->unique();
            $table->string('name');
            $table->decimal('percentage')->nullable();
            $table->timestamps();
        });

        //retenciones generales
        Schema::create('withholdings', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('percentage');
            $table->string('type');
            $table->timestamps();
        });

        Schema::create('iesses', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('name');
            $table->decimal('percentage');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pay_methods');
        Schema::dropIfExists('ices');
        Schema::dropIfExists('ivas');
        Schema::dropIfExists('withholdings');
        Schema::dropIfExists('iesses');
    }
};