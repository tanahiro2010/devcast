<?php
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;


return [
    'up' => function (Capsule $capsule) {
        $capsule::schema()->create('article_revisions', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('article_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('version');

            $table->string('title');
            $table->text('body');
            $table->jsonb('tags');

            $table->timestamp('created_at')->useCurrent();

            $table->foreign('article_id')->references('id')->on('articles')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['article_id', 'version']);
        });
    },
    'down' => function (Capsule $capsule) {
        $capsule::schema()->dropIfExists('article_revisions');
    },
];
