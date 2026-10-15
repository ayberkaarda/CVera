<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('kind', 20);
            $t->string('title', 150);
            $t->json('content');
            $t->string('share_slug', 64)->nullable()->unique();
            $t->timestamps();
        });
        Schema::create('document_versions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('document_id')->constrained()->cascadeOnDelete();
            $t->json('content');
            $t->string('title', 150);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_versions');
        Schema::dropIfExists('documents');
    }
};
