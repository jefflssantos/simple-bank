<?php

namespace Modules\User\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\User\Enums\UserAccountTypeEnum;
use Modules\User\Enums\UserDocumentTypeEnum;
use Modules\User\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\User\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'account_type' => fake()->randomElement([
                UserAccountTypeEnum::CUSTOMER->value,
                UserAccountTypeEnum::RETAILER->value,
            ]),
            'document_type' => $documentType = fake()->randomElement([
                UserDocumentTypeEnum::CPF->value,
                UserDocumentTypeEnum::CNPJ->value,
            ]),
            'document_number' => $documentType === UserDocumentTypeEnum::CPF->value
                ? fake()->unique()->cpf(false)
                : fake()->unique()->cnpj(false),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }
}
