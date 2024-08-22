<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('model_infos', function (Blueprint $table) {
            $table->id();
            $table->text('embedding_model_name');
            $table->text('hugging_api_key');
            $table->text('weaviate_cluster_URL');
            $table->text('weaviate_api_key');
            $table->text('weaviate_collection_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('model_infos');
    }
};
