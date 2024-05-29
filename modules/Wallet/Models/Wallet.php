<?php

namespace Modules\Wallet\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\User\Models\User;

class Wallet extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'balance',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'balance' => 'integer',
        ];
    }

    /**
     * Get the user that owns the wallet.
     *
     * @return BelongsTo<User, Wallet>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function credit(int $value): bool
    {
        return $this->update(['balance' => $this->balance + $value]);
    }

    public function debit(int $value): bool
    {
        return $this->update(['balance' => $this->balance - $value]);
    }

    public function hasBalance(int $amount): bool
    {
        return $this->balance >= $amount;
    }
}
