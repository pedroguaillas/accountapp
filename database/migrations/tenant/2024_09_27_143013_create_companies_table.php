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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->char('ruc', 13)->unique(); //Constraint
            $table->string('company', 300);
            $table->string('economic_activity', 300);
            $table->string('type'); //sociedad--persona natural
            $table->date('date')->nullable();   //Date init activity economic
            $table->integer('special')->nullable(); //Value integer only seller
            $table->boolean('accounting')->default(false);
            $table->string('phone', 15)->nullable();
            $table->string('cert_dir', 30)->nullable();
            $table->string('pass_cert')->nullable();
            $table->date('sign_valid_from')->nullable();
            $table->date('sign_valid_to')->nullable();
            $table->integer('enviroment_type')->default(1);

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("company_id");
            $table->string('name');
            $table->smallInteger('number');
            $table->string('ciudad');
            $table->string('canton');
            $table->string('parroquia');
            $table->string('address');
            $table->string('phone', 15)->nullable();
            $table->string('logo_dir', 30)->nullable();
            $table->boolean('is_matriz')->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign("company_id")->references("id")->on("companies");
            $table->unique(['company_id', 'number']);
        });

        Schema::create('emision_points', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("branch_id");
            $table->string('name')->nullable();
            $table->smallInteger('number');
            $table->integer('invoice')->default(1);
            $table->integer('credit_note')->default(1);
            $table->integer('debit_note')->default(1);
            $table->integer('retention')->default(1);
            $table->integer('referral_guide')->default(1);
            $table->integer('purchase_settlement')->default(1);
            $table->integer('lot')->default(1);
            $table->string('recognition')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign("branch_id")->references("id")->on("branches");
            $table->unique(['branch_id', 'number']);
        });

        Schema::create('cost_centers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("company_id");
            $table->string('name');
            $table->string('code');
            $table->string('type');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string("state");

            $table->timestamps();
            $table->softDeletes();

            $table->foreign("company_id")->references("id")->on("companies");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
        Schema::dropIfExists('cost_centers');
        Schema::dropIfExists('branches');
        Schema::dropIfExists('emision_points');
        Schema::dropIfExists('cost_centers');
    }
};
