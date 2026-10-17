<?php
namespace App\Models;

use App\Models\Challan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChallanItem extends Model
{
    protected $fillable = [
        'challan_id', 'item_description', 'style_name',
        'qty', 'unit', 'rate', 'amount',
    ];

    public function challan(): BelongsTo
    {
        return $this->belongsTo(Challan::class);
    }
}
