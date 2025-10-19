<?php

namespace Modules\MigraineDiary\App\Models;

use App\Models\User;
use Exception;
use Google\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserOauthToken extends Model
{

	protected $table = 'user_oauth_tokens';

	protected $fillable = [
		'user_id',
		'provider',
		'access_token',
		'refresh_token',
		'expires_at',
		'metadata'
	];

	protected $casts = [
		'expires_at' => 'datetime',
		'metadata' => 'array'
	];

	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}

	public function isExpired(): bool
	{
		return $this->expires_at && $this->expires_at->isPast();
	}

	public function needsRefresh(): bool
	{
		return $this->isExpired() && $this->refresh_token;
	}

	/**
	 * Refresh the access token using the refresh token
	 * @return bool
	 * @throws Exception
	 */
	public function refreshGoogleToken(): bool
	{
		if (!$this->needsRefresh()) {
			return false;
		}

		try {
			$client = new Client();
			$client->setClientId($this->metadata['client_id'] ?? null);
			$client->setClientSecret($this->metadata['client_secret'] ?? null);
			$client->setAccessType('offline');

			$newToken = $client->fetchAccessTokenWithRefreshToken($this->refresh_token);

			if (isset($newToken['access_token'])) {
				$this->update([
					'access_token' => $newToken['access_token'],
					'expires_at' => now()->addSeconds($newToken['expires_in'] ?? 3600),
				]);
				return true;
			}
		} catch (Exception $e) {
			\Log::error('Google token refresh failed', [
				'user_id' => $this->user_id,
				'provider' => $this->provider,
				'error' => $e->getMessage(),
			]);
		}

		return false;
	}
}
