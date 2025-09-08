<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		// User Symptoms
		Schema::create('migraine_user_symptoms', function (Blueprint $table) {
			$table->id();
			$table->foreignId('user_id')->constrained()->onDelete('cascade');
			$table->string('name');
			$table->text('description')->nullable();
			$table->timestamps();

			$table->index(['user_id', 'name']);
		});

		// User Triggers
		Schema::create('migraine_user_triggers', function (Blueprint $table) {
			$table->id();
			$table->foreignId('user_id')->constrained()->onDelete('cascade');
			$table->string('name');
			$table->text('description')->nullable();
			$table->timestamps();

			$table->index(['user_id', 'name']);
		});

		// User Medications
		Schema::create('migraine_user_meds', function (Blueprint $table) {
			$table->id();
			$table->foreignId('user_id')->constrained()->onDelete('cascade');
			$table->string('name');
			$table->string('dosage')->nullable();
			$table->text('description')->nullable();
			$table->timestamps();

			$table->index(['user_id', 'name']);
		});

		// Pivot table for user symptoms
		Schema::create('migraine_attack_user_symptom', function (Blueprint $table) {
			$table->foreignId('attack_id')->constrained('migraine_attacks')->onDelete('cascade');
			$table->foreignId('user_symptom_id')->constrained('migraine_user_symptoms')->onDelete('cascade');
			$table->primary(['attack_id', 'user_symptom_id']);
		});

		// Pivot table for user triggers
		Schema::create('migraine_attack_user_trigger', function (Blueprint $table) {
			$table->foreignId('attack_id')->constrained('migraine_attacks')->onDelete('cascade');
			$table->foreignId('user_trigger_id')->constrained('migraine_user_triggers')->onDelete('cascade');
			$table->primary(['attack_id', 'user_trigger_id']);
		});

		// Pivot table for user meds
		Schema::create('migraine_attack_user_med', function (Blueprint $table) {
			$table->foreignId('attack_id')->constrained('migraine_attacks')->onDelete('cascade');
			$table->foreignId('user_med_id')->constrained('migraine_user_meds')->onDelete('cascade');
			$table->string('dosage')->nullable();
			$table->primary(['attack_id', 'user_med_id']);
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('migraine_attack_user_med');
		Schema::dropIfExists('migraine_attack_user_trigger');
		Schema::dropIfExists('migraine_attack_user_symptom');

		Schema::dropIfExists('migraine_user_meds');
		Schema::dropIfExists('migraine_user_triggers');
		Schema::dropIfExists('migraine_user_symptoms');
	}
};
