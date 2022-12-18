<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
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

        Schema::create('identity_verifiable_addresses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('status', 16);
            $table->string('via', 16);
            $table->boolean('verified');
            $table->string('value', 400);
            $table->timestamp('verified_at')->nullable();
            $table->foreignUuid('identity_id')->references('id')->on('identities')->onDelete('cascade');
            $table->timestamps();
            $table->uuid('nid')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('identity_verifiable_addresses');
        Schema::dropIfExists('identities');
    }
};
