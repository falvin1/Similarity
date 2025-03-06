<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('reference_documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content'); // Teks asli dari referensi
            $table->text('preprocessed_content')->nullable(); // Hasil preprocessing
            $table->string('file_path')->nullable(); // Path ke file PDF
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('reference_documents');
    }
};
