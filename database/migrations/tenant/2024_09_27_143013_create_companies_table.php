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

        Schema::create('pay_methods', function (Blueprint $table) {
            $table->id();
            $table->integer('code'); //
            $table->string('name'); // 

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('active_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); //
            $table->integer('depresiation_time'); // 

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
            $table->integer('retention_agent')->nullable();
            $table->string('declaration_type')->nullable(); // mensual o semestral
            $table->string('certificate_path', 30)->nullable();
            $table->string('certificate_pass')->nullable();
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
            $table->integer('order_note')->default(1);
            $table->integer('proforma')->default(1);

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
            $table->boolean("is_active")->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign("company_id")->references("id")->on("companies");
        });

        // Plan de cuentas
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger(column: "company_id");
            $table->string('code');
            $table->string('name');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('type'); // activo, pasivo, patrimonio, ingreso, gasto y costos
            $table->boolean("is_active")->default(true);
            $table->boolean("is_detail")->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign("company_id")->references("id")->on("companies");
            // $table->unique(['code', 'company_id']);
        });

        // Asiento contable
        Schema::create('journals', function (Blueprint $table) {
            $table->id();
            $table->date(column: 'date');
            $table->string('reference')->nullable(); // Numero de asiento contable de cada compania
            $table->string('description')->nullable();
            $table->boolean('is_deductible')->default(false);
            $table->string('prefix')->nullable();
            $table->string('document_id')->nullable(); // Ej. ID compra
            $table->string('table')->nullable(); // shops
            $table->bigInteger("user_id");
            $table->bigInteger("company_id");
            $table->bigInteger('cost_center_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign("company_id")->references("id")->on("companies");
            $table->foreign('cost_center_id')->references('id')->on('cost_centers');
        });

        // Item de asiento contables
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('journal_id');
            $table->bigInteger('account_id');

            $table->decimal('debit', 14, 2)->default(0);
            $table->decimal('have', 14, 2)->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('journal_id')->references('id')->on('journals');
            $table->foreign('account_id')->references('id')->on('accounts');

        });



        // activos fijos
        Schema::create('fixed_assets', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('pay_method_id');
            $table->bigInteger('company_id');
            $table->bigInteger('type_id');

            $table->boolean('is_depretation_a')->default(false);
            $table->boolean('is_legal')->default(false);
            $table->string('vaucher')->nullable();
            $table->date('date_acquisition');
            $table->string('detail');
            $table->string('code');

            $table->string('address');
            $table->integer('period');
            $table->decimal('value', 14, 2)->default(0);
            $table->decimal('residual_value', 14, 2)->default(0);
            $table->date('date_end');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('pay_method_id')->references('id')->on('pay_methods');
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('type_id')->references('id')->on('active_types');

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
        Schema::dropIfExists('accounts');
        Schema::dropIfExists('journals');
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('pay_methods');
        Schema::dropIfExists('fixed_assets');
        Schema::dropIfExists(' active_types');

    }
};
