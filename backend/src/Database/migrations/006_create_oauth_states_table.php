<?php
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;


return [
    'up' => function (Capsule $capsule) {
        $capsule::schema()->create('oauth_states', function (Blueprint $table) {
            $table->string('state')->primary();
            $table->timestamp('expires_at');
            $table->timestamps();
        });
    },
    'down' => function (Capsule $capsule) {
        $capsule::schema()->dropIfExists('oauth_states');
    },
];
