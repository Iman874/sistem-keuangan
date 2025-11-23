<?php

namespace App\Notifications;

use App\Models\IncomeSessionReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SessionReportSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public IncomeSessionReport $report)
    {
    }

    public function via(object $notifiable): array
    {
        // Use database notifications; email optional if configured
        return ['database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Laporan Sesi Pemasukkan Dikirim')
            ->line('Kasir mengirim laporan sesi pemasukkan.')
            ->line('Tanggal: '.$this->report->date->format('Y-m-d'))
            ->line('Sesi: '.$this->report->session)
            ->line('Total Cash: Rp '.number_format($this->report->total_cash,0,',','.'))
            ->line('Total QRIS: Rp '.number_format($this->report->total_qris,0,',','.'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'session_report_submitted',
            'report_id' => $this->report->id,
            'cashier_id' => $this->report->cashier_id,
            'date' => $this->report->date->format('Y-m-d'),
            'session' => $this->report->session,
            'total_cash' => $this->report->total_cash,
            'total_qris' => $this->report->total_qris,
            'message' => 'Laporan sesi pemasukkan dikirim',
        ];
    }
}
