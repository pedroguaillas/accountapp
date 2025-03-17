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

        Schema::create('type_banks', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('name');
            $table->string('type');//ingreso o egreso

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
            $table->integer('code')->unique('code');
            ;
            $table->string('name');
            $table->decimal('max')->nullable();
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
            $table->boolean('state')->default(true); // Active/inactive state

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
            $table->json('data_additional')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign("branch_id")->references("id")->on("branches");
            $table->unique(['branch_id', 'number']);
        });

        Schema::create('cost_centers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("company_id");
            $table->string('code');
            $table->string('name');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->boolean('state')->default(true); // Active/inactive state

            $table->timestamps();
            $table->softDeletes();

            $table->foreign("company_id")->references("id")->on("companies");
            $table->unique(['company_id', 'code']);
        });

        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("company_id");
            $table->string('code');
            $table->string('name');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('type'); // activo, pasivo, patrimonio, ingreso, gasto y costos
            $table->boolean("state")->default(true);
            $table->boolean("is_detail")->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign("company_id")->references("id")->on("companies");
            $table->unique(['company_id', 'code']);
        });

        Schema::create('active_types', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("company_id");
            $table->string('name');
            $table->integer('depreciation_time');
            $table->bigInteger('account_id')->nullable();
            $table->bigInteger('account_dep_id')->nullable();
            $table->bigInteger('account_dep_spent_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign("company_id")->references("id")->on("companies");
        });

        Schema::create('journals', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("company_id");
            $table->bigInteger("user_id");
            $table->bigInteger('cost_center_id')->nullable();
            $table->date('date');
            $table->string('reference')->nullable(); // Numero de asiento contable de cada compania
            $table->string('description')->nullable();
            $table->boolean('is_deductible')->default(false);
            $table->string('prefix')->nullable();
            $table->string('document_id')->nullable(); // Ej. ID compra
            $table->string('table')->nullable(); // shops

            $table->timestamps();
            $table->softDeletes();

            $table->foreign("company_id")->references("id")->on("companies");
            $table->foreign('cost_center_id')->references('id')->on('cost_centers');
        });

        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('journal_id');
            $table->bigInteger('account_id');
            $table->decimal('debit', 14, 2)->default(0);
            $table->decimal('have', 14, 2)->default(0);
            $table->json('data_additional')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('journal_id')->references('id')->on('journals');
            $table->foreign('account_id')->references('id')->on('accounts');
        });

        Schema::create('fixed_assets', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id');
            $table->bigInteger('pay_method_id');
            $table->bigInteger('active_type_id');
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
            $table->foreign('active_type_id')->references('id')->on('active_types');
            $table->unique(['company_id', 'code']);
        });

        Schema::create('depreciations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('fixed_asset_id');
            $table->date('date');
            $table->decimal('percentage', 5, 2)->default(0);
            $table->decimal('amount', 14, 2)->default(0);
            $table->decimal('value', 14, 2)->default(0);
            $table->decimal('accumulated', 14, 2)->default(0);
            $table->json('data_additional')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('fixed_asset_id')->references('id')->on('fixed_assets');
        });

        Schema::create('intangible_assets', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id');
            $table->bigInteger('pay_method_id');
            $table->String('type');
            $table->boolean('is_legal')->default(false);
            $table->string('vaucher')->nullable();
            $table->date('date_acquisition');
            $table->string('detail');
            $table->string('code');
            $table->integer('period');
            $table->decimal('value', 14, 2)->default(0);
            $table->date('date_end');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('pay_method_id')->references('id')->on('pay_methods');
            $table->foreign('company_id')->references('id')->on('companies');
            $table->unique(['company_id', 'code']);
        });

        Schema::create('amortizations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('intangible_asset_id');
            $table->date('date');
            $table->decimal('percentage', 5, 2)->default(0);
            $table->decimal('amount', 14, 2)->default(0);
            $table->decimal('value', 14, 2)->default(0);
            $table->decimal('accumulated', 14, 2)->default(0);
            $table->json('data_additional')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('intangible_asset_id')->references('id')->on('intangible_assets');
        });

        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id');
            $table->String('cuit');
            $table->string('name');
            $table->string('sector_code')->nullable();
            $table->string('position');
            $table->integer('days');
            $table->decimal('salary', 14, 2)->default(0);
            $table->decimal('porcent_aportation', 14, 2)->default(0);
            $table->boolean('is_a_parnert')->default(false);
            $table->boolean('is_a_qualified_craftsman')->default(false);
            $table->boolean('affiliated_with_spouse')->default(false);
            $table->date('date_start');
            $table->boolean('xiii')->default(false);
            $table->boolean('xiv')->default(false);
            $table->boolean('reserve_funds')->default(false);
            $table->string('email')->nullable();
            $table->boolean('state')->default(true); // Active/inactive state

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies');
            $table->unique(['company_id', 'cuit']);
        });

        Schema::create('advances', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id');
            $table->bigInteger('employee_id');
            $table->string('detail')->nullable();
            $table->decimal('amount', 14, 2)->default(0);
            $table->date('date');
            $table->string('type');
            $table->string('payment_type')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('employee_id')->references('id')->on('employees');
            $table->foreign('company_id')->references('id')->on('companies');
        });

        Schema::create('role_ingresses', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id');
            $table->string('code');
            $table->string('name');
            $table->string('type');
            $table->bigInteger('account_departure_id')->nullable();
            $table->bigInteger('account_counterpart_id')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies');
            $table->unique(['company_id', 'code']);
        });

        Schema::create('role_egresses', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id');
            $table->string('code');
            $table->string('name');
            $table->string('type');
            $table->bigInteger('account_departure_id')->nullable();
            $table->bigInteger('account_counterpart_id')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies');
            $table->unique(['company_id', 'code']);
        });

        Schema::create('payment_roles', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id');
            $table->bigInteger('employee_id');
            $table->string('position');
            $table->string('sector_code')->nullable();
            $table->integer('days');
            $table->decimal('salary', 14, 2)->default(0);
            $table->decimal('salary_receive', 14, 2)->default(0);
            $table->date('date');
            $table->string('state')->default("CREADO");
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('employee_id')->references('id')->on('employees');
            $table->foreign('company_id')->references('id')->on('companies');
        });

        Schema::create('payment_role_ingresses', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('payment_role_id');
            $table->bigInteger('role_ingress_id');
            $table->decimal('value', 14, 2)->default(0);
            $table->json('data_additional')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('payment_role_id')->references('id')->on('payment_roles');
            $table->foreign('role_ingress_id')->references('id')->on('role_ingresses');
        });

        Schema::create('payment_role_egresses', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('payment_role_id');
            $table->bigInteger('role_egress_id');
            $table->decimal('value', 14, 2)->default(0);
            $table->json('data_additional')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('payment_role_id')->references('id')->on('payment_roles');
            $table->foreign('role_egress_id')->references('id')->on('role_egresses');
        });

        Schema::create('hours', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id');
            $table->bigInteger('employee_id');
            $table->string('detail')->nullable();
            $table->decimal('amount', 14, 2)->default(0);
            $table->date('date');
            $table->string('type');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('employee_id')->references('id')->on('employees');
            $table->foreign('company_id')->references('id')->on('companies');
        });

        Schema::create('movement_types', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id');
            $table->string('code')->unique();
            $table->string('name'); // Movement type name 
            $table->string('type'); // tipo de movimiento(ingreso o egreso)
            $table->string('venue');//caja,banco,ambos
            $table->bigInteger('account_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies');
        });

        Schema::create('banks', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id');
            $table->string('name'); // Bank name
            $table->text('description')->nullable(); // Additional information
            $table->boolean('state')->default(true); // Active/inactive state
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies');
        });

        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('bank_id');
            $table->string('account_number'); // Unique account number
            $table->string('account_type'); // Account type
            $table->decimal('current_balance', 15, 2)->default(0); // Current balance
            $table->boolean('state')->default(true); // Active/inactive state
            $table->bigInteger('account_id')->nullable(); // cuenta de vinculacion
            $table->json('data_additional')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('bank_id')->references('id')->on('banks');

            $table->unique(['bank_id', 'account_number']);
        });

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('movement_type_id');
            $table->bigInteger('bank_account_id');
            $table->date('transaction_date'); // Transaction date and time
            $table->decimal('amount', 15, 2); // Transaction amount
            $table->string('description')->nullable(); // Transaction description
            $table->string('payment_method'); // Tipo de pago (por ejemplo, cheque, efectivo, trasnferencia bancaria)
            $table->bigInteger('beneficiary_id'); // Persona beneficiaria
            $table->date('cheque_date')->nullable(); // Fecha del cheque (solo cuando el método de pago sea cheque)
            $table->string('transfer_account')->nullable(); // numero de cuenta (solo cuando el método de pago sea diferente de cheque)
            $table->string('voucher_number')->nullable(); // Número de comprobante
            $table->string('state_transaction')->default('vigente'); //vigente o finalizado
            $table->json('data_additional')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('bank_account_id')->references('id')->on('bank_accounts');
            $table->foreign('movement_type_id')->references('id')->on('movement_types');
        });

        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id');
            $table->string('identification'); // cedula
            $table->string('name'); // Primer nombre
            $table->string('email'); // Correo electrónico
            $table->string('phone')->nullable(); // Número de teléfono
            $table->string('address')->nullable(); // direccion
            $table->json('data_additional')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies');
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

        //retenciones tenant
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
            $table->string('code')->unique();
            $table->string('type');
            $table->string('name');
            $table->decimal('percentage');
            $table->timestamps();
        });

        Schema::create('payment_regims', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("company_id");
            $table->string('region'); // Costa o Sierra 
            $table->string('months_xiii'); // Meses para el décimo tercero
            $table->string('months_xiv'); // Meses para el décimo cuarto
            $table->string('months_reserve_funds')->nullable(); // Meses para fondos de reserva
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies');

        });

        Schema::create('benefits_accumulation', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('employee_id'); // Número de identificación del empleado
            $table->foreignId('regimen_id');//sierra o a costa
            $table->string('month'); // Mes de pago o acumulacion
            $table->decimal('value', 10, 2)->default(0.00);
            $table->string('type');
            $table->string('pay_status')->default("pendiente");//pendiente/pagado

            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees');
            $table->foreign('regimen_id')->references('id')->on('payment_regims');
            $table->unique(['employee_id', 'month', 'type']);
        });

        Schema::create('boxes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id');//puede estar relacionada con establecimiento o centro de costo
            $table->bigInteger('owner_id');
            $table->string('name'); // Boxe name
            $table->string('type'); // boxe type general crearce por defecto 
            $table->bigInteger('account_id')->nullable(); // cuenta de vinculacion
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('owner_id')->references('id')->on('employees');
        });

        Schema::create('cash_sessions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('box_id');
            $table->bigInteger('open_employee_id');
            $table->bigInteger('close_employee_id')->nullable();
            $table->decimal('initial_value', 10, 2);
            $table->decimal('ingress', 10, 2)->default(0);
            $table->decimal('egress', 10, 2)->default(0);
            $table->decimal('balance', 10, 2)->default(0);
            $table->string('state_box');//open,close

            $table->timestamps();
            $table->softDeletes();//

            $table->foreign('box_id')->references('id')->on('boxes');
            $table->foreign('open_employee_id')->references('id')->on('employees');
        });

        Schema::create('transaction_boxes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('cash_session_id');
            $table->bigInteger('movement_type_id');
            $table->decimal('amount', 10, 2);
            $table->string('description')->nullable();
            $table->bigInteger('beneficiary_id')->nullable(); // Persona beneficiaria
            $table->string('state_transaction')->nullable(); //vigente o finalizado
            $table->json('data_additional')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('cash_session_id')->references('id')->on('cash_sessions');
            $table->foreign('movement_type_id')->references('id')->on('movement_types');
        });

        //configuracion de cajas para poder continuar con el monto quedado en la caja cerrada anteriormente y un monto maximo que tenga cada caja para alertas
        // Schema::create('setting_boxes', function (Blueprint $table) {
        //     $table->id();
        //     $table->bigInteger('company_id');
        //     $table->boolean('continue')->default(false);//ingresos y egresos
        //     $table->decimal('max', 10, 2)->nullable();

        //     $table->timestamps();
        //     $table->softDeletes();

        //     $table->foreign('company_id')->references('id')->on('companies');
        // });

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
        Schema::dropIfExists('active_types');
        Schema::dropIfExists('intangible_assets');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('advances');
        Schema::dropIfExists('payment_roles');
        Schema::dropIfExists('hours');
        Schema::dropIfExists('movement_types');
        Schema::dropIfExists('bank_accounts');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('people');
        Schema::dropIfExists('ivas');
        Schema::dropIfExists('ices');
        Schema::dropIfExists('withholdings');
        Schema::dropIfExists('iesses');
        Schema::dropIfExists('payment_regimes');
        Schema::dropIfExists('employee_payments');
        Schema::dropIfExists('boxes');
        Schema::dropIfExists('boxes');
    }
};