<?php
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;


return [
    'up' => function (Capsule $capsule) {
        $capsule::schema()->table('articles', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->after('id');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        $capsule::schema()->table('article_status', function (Blueprint $table) {
            $table->boolean('is_synced')->default(false)->after('is_published');
        });
    },
    'down' => function (Capsule $capsule) {
        $capsule::schema()->table('articles', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'created_at', 'updated_at']);
        });

        $capsule::schema()->table('article_status', function (Blueprint $table) {
            $table->dropColumn('is_synced');
        });
    },
];
