<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('document_histories', function (Blueprint $table) {
            $table->foreignId('reference_document_id')->nullable()->constrained('reference_documents')->onDelete('cascade');
        });
    }
    
    public function down()
    {
        Schema::table('document_histories', function (Blueprint $table) {
            $table->dropForeign(['reference_document_id']);
            $table->dropColumn('reference_document_id');
        });
    }
};
