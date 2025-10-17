<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('user_oauth_tokens', function (Blueprint $table) {
			$table->id();
			$table->foreignId('user_id')->constrained()->onDelete('cascade');
			$table->string('provider'); // 'google', etc
			$table->text('access_token');
			$table->text('refresh_token')->nullable();
			$table->timestamp('expires_at')->nullable();
			$table->json('metadata')->nullable(); // additional provider-specific data
			$table->timestamps();

			$table->unique(['user_id', 'provider']);
			$table->index(['user_id', 'provider']);
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('user_oauth_tokens');
	}
};
