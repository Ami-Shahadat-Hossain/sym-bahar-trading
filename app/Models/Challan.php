<?php
namespace App\Models;

use App\Helpers\NumberToWords;
use App\Models\ChallanItem;
use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Challan extends Model
{
    protected $fillable = [
        'challan_no', 'challan_date', 'customer_id', 'buyer_name',
        'currency', 'total_amount', 'amount_in_words',
    ];

    protected $casts = ['challan_date' => 'date'];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ChallanItem::class);
    }

    public function invoices()
    {
        return $this->belongsToMany(Invoice::class, 'invoice_challan');
    }

    /**
     * Auto-generate challan number: CH + MMM + YYYY + 0001
     * e.g. CH09260001
     */
    public static function generateChallanNo(): string
    {
        $month  = now()->format('m');
        $year   = now()->format('y');
        $prefix = 'CH' . $month . $year;

        $last = static::where('challan_no', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->first();

        $next = $last ? ((int) substr($last->challan_no, -4)) + 1 : 1;

        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    public function recalculateTotal(): void
    {
        $this->total_amount = $this->items()->sum('amount');

        // Respect the challan's own currency (USD default for legacy rows)
        $this->amount_in_words = NumberToWords::convert(
            (float) $this->total_amount,
            $this->currency ?? 'USD'
        );

        $this->save();
    }

    /**
     * Convenience symbol for display: $ for USD, ৳ for BDT.
     */
    public function getCurrencySymbolAttribute(): string
    {
        return strtoupper($this->currency ?? 'USD') === 'BDT' ? '৳' : '$';
    }
}
