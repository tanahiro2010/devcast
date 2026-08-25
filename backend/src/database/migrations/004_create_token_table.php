<?php
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;


return [
    'up' => function (Capsule $capsule) {
        //
        $capsule::schema()->create('tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');

            $table->string('provider')->default('qiita');
            $table->string('token', 255)->unique();
            $table->timestamp('expires_at');
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    },
    'down' => function (Capsule $capsule) {
        //
        $capsule::schema()->dropIfExists('tokens');
    },
];
