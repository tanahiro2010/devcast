<?php

namespace App\Models\DB;

use App\Models\DB\BaseModel;
use App\Models\DB\User;

class ProviderToken extends BaseModel {
  protected $table = 'tokens';
  protected $primaryKey = 'id';
  protected $fillable = [
    'id',
    'user_id',
    'provider',
    'token',
    'expires_at',
    'created_at',
    'updated_at'
  ];

  public function __construct() {
    parent::__construct();
  }

  public static function findByUserId(int $userId) {
    return self::firstWhere(['user_id' => $userId]);
  }

  public function user(): ?User {
    return $this->belongsTo(User::class, 'user_id');
  }

  public function isTokenExpired() {
    if ($this->expires_at === null) {
      return true; // If there's no expiration time, consider it expired
    }
    $currentTime = new \DateTime();
    $expirationTime = new \DateTime($this->expires_at);
    return $currentTime >= $expirationTime;
  }
}