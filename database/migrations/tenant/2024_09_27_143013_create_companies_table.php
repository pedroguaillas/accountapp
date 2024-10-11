<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('economic_activities', function (Blueprint $table) {
            $table->id();
            $table->char('name'); // Diseño, construcción, comercio

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('contributor_types', function (Blueprint $table) {
            $table->id();
            $table->char('name'); // General, Negocio Popular, Emprendedor

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->char('ruc', 13)->unique(); //Constraint
            $table->string('company', 300);
            $table->bigInteger('economic_activity_id');
            $table->bigInteger('contributor_type_id');
            $table->date('date')->nullable();   // Inicio de activiades
            $table->date('restart_activities')->nullable();   // Reinicio de activiades
            $table->integer('special')->nullable(); // Contribuyente especial
            $table->boolean('accounting')->default(false);
            $table->integer('retention_agent')->default(null);
            $table->string('declaration_type')->nullable(); // mensual o semestral
            $table->string('certificate_path', 30)->nullable();
            $table->string('certificate_password')->nullable();
            $table->timestamp('sign_valid_from')->nullable();
            $table->timestamp('sign_valid_to')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign("economic_activity_id")->references("id")->on("economic_activities");
            $table->foreign("contributor_type_id")->references("id")->on("contributor_types");
        });

        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("company_id");
            $table->string('name')->nullable(); // Nombre comercial
            $table->smallInteger('number');
            $table->string('city');
            $table->string('phone', 15)->nullable();
            $table->string('address');
            $table->string('logo_path', 30)->nullable();
            $table->boolean('is_matriz')->default(false);
            $table->integer('enviroment_type')->default(1);
            $table->string('email')->nullable();
            $table->string('pass_email')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign("company_id")->references("id")->on("companies");
            $table->unique(['company_id', 'number']);
        });

        Schema::create('emision_points', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("branch_id");
            $table->string('name')->nullable(); // Un reconicimiento
            $table->integer('number');
            $table->integer('invoice')->default(1);
            $table->integer('credit_note')->default(1);
            $table->integer('debit_note')->default(1);
            $table->integer('retention')->default(1);
            $table->integer('referral_guide')->default(1);
            $table->integer('purchase_settlement')->default(1);
            $table->integer('lot')->default(1);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign("branch_id")->references("id")->on("branches");
            $table->unique(['branch_id', 'number']);
        });

        // Migraciones de Contabilidad
        Schema::create('cost_centers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("company_id");
            $table->string('code');
            $table->string('name');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('type');
            $table->string("state");

            $table->timestamps();
            $table->softDeletes();

            $table->foreign("company_id")->references("id")->on("companies");
        });

        Schema::create('chart_accounts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("company_id");
            $table->string('code');
            $table->string('name');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('type');
            $table->string("state");

            $table->timestamps();
            $table->softDeletes();

            $table->foreign("company_id")->references("id")->on("companies");
        });

        Schema::create('account_entries', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("chart_account_id");
            $table->timestamp(column: 'date');
            $table->string('gloss');
            $table->string('not_deductible');
            $table->string('prefix')->nullable();
            $table->string('document_id')->nullable();
            $table->string('table');
            $table->bigInteger("user_id");

            $table->timestamps();
            $table->softDeletes();

            $table->foreign("chart_account_id")->references("id")->on("chart_accounts");
        });

        Schema::create('account_entry_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('account_entry_id'); //references table "account_entries"
            $table->bigInteger('chart_account_id'); //references table "chart_accounts"
            $table->bigInteger('cost_center_id'); //references table "chart_accounts"
            $table->decimal('debit', 14, 6)->default(0); //mount debit
            $table->decimal('have', 14, 6)->default(0);  //mount have

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('account_entry_id')->references('id')->on('account_entries');
            $table->foreign('chart_account_id')->references('id')->on('chart_accounts');
            $table->foreign('cost_center_id')->references('id')->on('cost_centers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('economic_activities');
        Schema::dropIfExists('contributor_types');
        Schema::dropIfExists('companies');
        Schema::dropIfExists('branches');
        Schema::dropIfExists('emision_points');
        Schema::dropIfExists('cost_centers');
        Schema::dropIfExists('chart_accounts');
        Schema::dropIfExists('account_entries');
        Schema::dropIfExists('account_entry_items');
    }
};
