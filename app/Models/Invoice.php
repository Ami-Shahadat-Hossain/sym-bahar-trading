<?php
namespace App\Models;

use App\Helpers\NumberToWords;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_no', 'invoice_date', 'customer_id', 'customer_name',
        'currency', 'total_amount', 'amount_in_words',
    ];

    protected $casts = ['invoice_date' => 'date'];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function challans(): BelongsToMany
    {
        return $this->belongsToMany(Challan::class, 'invoice_challan');
    }

    public function moneyReceipts(): HasMany
    {
        return $this->hasMany(MoneyReceipt::class);
    }

    /**
     * Auto-generate invoice number: INV + MM + YY + 0001
     */
    public static function generateInvoiceNo(): string
    {
        $prefix = 'INV' . now()->format('m') . now()->format('y');

        $last = static::where('invoice_no', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->first();

        $next = $last ? ((int) substr($last->invoice_no, -4)) + 1 : 1;

        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    public function recalculateTotal(): void
    {
        $this->total_amount = $this->items()->sum('amount');

        // Respect the invoice's own currency (USD default for legacy rows)
        $this->amount_in_words = NumberToWords::convert(
            (float) $this->total_amount,
            $this->currency ?? 'USD'
        );

        $this->save();
    }

    /**
     * Convenience symbol: $ for USD, ৳ for BDT.
     */
    public function getCurrencySymbolAttribute(): string
    {
        return strtoupper($this->currency ?? 'USD') === 'BDT' ? '৳' : '$';
    }
}
