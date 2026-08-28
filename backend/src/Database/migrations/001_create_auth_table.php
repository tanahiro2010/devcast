<?php
use App\Config\Config;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;


return [
    'up' => function (Capsule $capsule) {
        $capsule::schema()->create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('provider', Config::oauth()['providers'])->default('github');
            $table->string('provider_id')->unique();

            $table->string('username')->nullable();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('avatar_url')->nullable();

            $table->timestamps();
        });

        $capsule::schema()->create('credentials', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');

            $table->enum('provider', Config::oauth()['providers'])->default('github');
            $table->string('access_token')->nullable();
            $table->string('refresh_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->string('scope')->nullable();
            $table->string('token_type')->nullable()->default('Bearer');
            
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        $capsule::schema()->create('sessions', function (Blueprint $table) { // 基本的にはjwtで実装
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('session_id')->unique();
            
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();

            $table->boolean('is_logged_out')->default(false);

            $table->timestamps();
            $table->timestamp('expires_at');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    },
    'down' => function (Capsule $capsule) {
        $capsule::schema()->drop('users');
        $capsule::schema()->drop('credentials');
        $capsule::schema()->drop('sessions');
    },
];
