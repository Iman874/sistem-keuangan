<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice->load(['incomes.category','expends','cashier']);
    }

    public function build()
    {
        return $this->subject('Invoice '.$this->invoice->number)
            ->view('kasir.invoice.print', ['invoice' => $this->invoice]);
    }
}
