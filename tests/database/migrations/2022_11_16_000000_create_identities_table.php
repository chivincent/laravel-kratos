<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('identities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('schema_id', 2048);
            $table->jsonb('traits');
            $table->timestamps();
            $table->uuid('nid')->nullable();
            $table->string('state')->default('active');
            $table->timestamp('state_changed_at')->nullable();
            $table->jsonb('metadata_public')->nullable();
            $table->jsonb('metadata_admin')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('identities');
    }
};