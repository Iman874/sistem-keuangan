<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaldoTransfer extends Model
{
    use HasFactory;
    protected $fillable = [
        'source_account','destination_account','amount','user_id','date','time','note',
        'income_id','expend_id','invoice_income_id','invoice_expend_id'
    ];

    public function user(){ return $this->belongsTo(User::class,'user_id'); }
    public function income(){ return $this->belongsTo(Income::class,'income_id'); }
    public function expend(){ return $this->belongsTo(Expend::class,'expend_id'); }
    public function invoiceIncome(){ return $this->belongsTo(Invoice::class,'invoice_income_id'); }
    public function invoiceExpend(){ return $this->belongsTo(Invoice::class,'invoice_expend_id'); }
}
