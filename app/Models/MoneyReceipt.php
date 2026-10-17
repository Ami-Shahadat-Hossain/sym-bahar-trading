<?php
namespace App\Models;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MoneyReceipt extends Model
{
    protected $fillable = [
        'receipt_no', 'receipt_date', 'invoice_id', 'received_from',
        'amount', 'amount_in_words', 'against_bill_no', 'mode',
    ];

    protected $casts = ['receipt_date' => 'date'];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Auto-generate receipt number: MMMYY + 0001
     * e.g. 09260001
     */
    public static function generateReceiptNo(): string
    {
        $month  = now()->format('m');
        $year   = now()->format('y');
        $prefix = $month . $year;

        $last = static::where('receipt_no', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->first();

        $next = $last ? ((int) substr($last->receipt_no, -4)) + 1 : 1;

        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
