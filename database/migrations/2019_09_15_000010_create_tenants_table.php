<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->string('id')->primary();

            // Datos de la empresa

            $table->timestamps();
            $table->json('data')->nullable();
        });
        Schema::create('tenant_user', function (Blueprint $table) {
            $table->string("tenant_id");
            $table->unsignedBigInteger("user_id");
            $table->unique(["user_id", "tenant_id"]);
           // $table->boolean('is_owner')->default(false);
        });
        Schema::table('users', function (Blueprint $table) {
            $table->string("current_tenant_id")->nullable();
            $table->foreign("current_tenant_id")->references("id")->on("tenants");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
        Schema::dropIfExists('tenant_user');
    }
}
