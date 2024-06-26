<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\User\Enums\UserAccountTypeEnum;
use Modules\User\Enums\UserDocumentTypeEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique()->index();
            $table->enum('account_type', [
                UserAccountTypeEnum::Consumer->value,
                UserAccountTypeEnum::Seller->value,
            ]);
            $table->enum('document_type', [
                UserDocumentTypeEnum::CPF->value,
                UserDocumentTypeEnum::CNPJ->value,
            ]);
            $table->string('document_number')->unique()->index();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
