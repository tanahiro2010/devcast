<?php
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;


return [
    'up' => function (Capsule $capsule) {
        $capsule::schema()->table('article_status', function (Blueprint $table) {
            $table->unsignedInteger('article_id')->change();
            $table->foreign('article_id')->references('id')->on('articles')->onDelete('cascade');
        });
    },
    'down' => function (Capsule $capsule) {
        $capsule::schema()->table('article_status', function (Blueprint $table) {
            $table->dropForeign(['article_id']);
            $table->integer('article_id')->change();
        });
    },
];
