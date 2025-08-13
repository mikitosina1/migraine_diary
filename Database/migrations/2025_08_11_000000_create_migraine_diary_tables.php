<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migraine Diary Tables
 *
 * Basic data in MigraineDiary/Database/Seeders
 */
return new class extends Migration {
	public function up(): void
	{
		Schema::create('migraine_attacks', function (Blueprint $table) {
			$table->id();
			$table->foreignId('user_id')->constrained()->onDelete('cascade');
			$table->dateTime('start_time')->default(now());
			$table->dateTime('end_time')->nullable();
			$table->integer('pain_level')->nullable(); // basic as 1-10
			$table->text('notes')->nullable();
			$table->timestamps();
		});

		/**
		 * dictionary and translations
		 */

		Schema::create('migraine_symptoms', function (Blueprint $table) {
			$table->id();
			$table->string('code')->unique();
			$table->timestamps();
		});

		Schema::create('migraine_symptom_translations', function (Blueprint $table) {
			$table->id();
			$table->foreignId('symptom_id')->constrained('migraine_symptoms')->onDelete('cascade');
			$table->string('locale', 5); // ru, en, de
			$table->string('name');
			$table->text('description')->nullable();
			$table->timestamps();

			$table->unique(['symptom_id', 'locale']);
		});

		Schema::create('migraine_triggers', function (Blueprint $table) {
			$table->id();
			$table->string('code')->unique();
			$table->timestamps();
		});

		Schema::create('migraine_trigger_translations', function (Blueprint $table) {
			$table->id();
			$table->foreignId('trigger_id')->constrained('migraine_triggers')->onDelete('cascade');
			$table->string('locale', 5);
			$table->string('name');
			$table->text('description')->nullable();
			$table->timestamps();

			$table->unique(['trigger_id', 'locale']);
		});

		Schema::create('migraine_meds', function (Blueprint $table) {
			$table->id();
			$table->string('code')->unique(); // example: ibuprofen_400
			$table->timestamps();
		});

		Schema::create('migraine_med_translations', function (Blueprint $table) {
			$table->id();
			$table->foreignId('med_id')->constrained('migraine_meds')->onDelete('cascade');
			$table->string('locale', 5);
			$table->string('name');
			$table->text('description')->nullable();
			$table->timestamps();

			$table->unique(['med_id', 'locale']);
		});

		/**
		 * PIVOT-Tables (many-to-many)
		 */

		Schema::create('migraine_attack_symptom', function (Blueprint $table) {
			$table->foreignId('attack_id')->constrained('migraine_attacks')->onDelete('cascade');
			$table->foreignId('symptom_id')->constrained('migraine_symptoms')->onDelete('cascade');
			$table->primary(['attack_id', 'symptom_id']);
		});

		Schema::create('migraine_attack_trigger', function (Blueprint $table) {
			$table->foreignId('attack_id')->constrained('migraine_attacks')->onDelete('cascade');
			$table->foreignId('trigger_id')->constrained('migraine_triggers')->onDelete('cascade');
			$table->primary(['attack_id', 'trigger_id']);
		});

		Schema::create('migraine_attack_med', function (Blueprint $table) {
			$table->foreignId('attack_id')->constrained('migraine_attacks')->onDelete('cascade');
			$table->foreignId('med_id')->constrained('migraine_meds')->onDelete('cascade');
			$table->string('dosage')->nullable();
			$table->primary(['attack_id', 'med_id']);
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('migraine_attack_med');
		Schema::dropIfExists('migraine_attack_trigger');
		Schema::dropIfExists('migraine_attack_symptom');

		Schema::dropIfExists('migraine_med_translations');
		Schema::dropIfExists('migraine_meds');

		Schema::dropIfExists('migraine_trigger_translations');
		Schema::dropIfExists('migraine_triggers');

		Schema::dropIfExists('migraine_symptom_translations');
		Schema::dropIfExists('migraine_symptoms');

		Schema::dropIfExists('migraine_attacks');
	}
};
