<?php
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;


return [
    'up' => function (Capsule $capsule) {
        $capsule::schema()->create("articles", function (Blueprint $table) {
            $table->increments("id");

            $table->string("title");
            $table->string("slug");
            $table->text("body");
            $table->jsonb("tags");

            $table->timestamp("last_updated_at");
        });

        $capsule::schema()->create("article_status", function (Blueprint $table) {
            $table->increments("id");

            $table->integer("article_id");
            $table->string("provider_id");
            $table->boolean("is_published");

            $table->timestamp("published_at");
            $table->timestamps();
        });
    },
    'down' => function (Capsule $capsule) {
        $capsule::schema()->drop("articles");
        $capsule::schema()->drop("article_status");
    },
];
