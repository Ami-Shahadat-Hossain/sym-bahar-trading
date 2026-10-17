<?php
namespace App\Models;

use App\Models\Challan;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'customer_id', 'company_name', 'address', 'customer_name', 'attention',
    ];

    public function challans(): HasMany
    {
        return $this->hasMany(Challan::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Auto-generate next customer ID (CUST-0001, CUST-0002 …)
     */
    public static function generateCustomerId(): string
    {
        $last = static::orderByDesc('id')->first();
        $next = $last ? ((int) substr($last->customer_id, -4)) + 1 : 1;
        return 'CUST-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
