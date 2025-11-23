<?php

namespace App\Notifications;

use App\Models\IncomeSessionReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SessionReportDecided extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public IncomeSessionReport $report)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Keputusan Laporan Sesi Pemasukkan')
            ->line('Laporan sesi telah diputuskan.')
            ->line('Tanggal: '.$this->report->date->format('Y-m-d'))
            ->line('Sesi: '.$this->report->session)
            ->line('Status: '.strtoupper($this->report->status));
        if ($this->report->status==='approved' && $this->report->approval_note) {
            $mail->line('Catatan Approval: '.$this->report->approval_note);
        }
        if ($this->report->status==='rejected' && $this->report->note) {
            $mail->line('Alasan Penolakan: '.$this->report->note);
        }
        return $mail;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'session_report_decided',
            'report_id' => $this->report->id,
            'date' => $this->report->date->format('Y-m-d'),
            'session' => $this->report->session,
            'status' => $this->report->status,
            'approval_note' => $this->report->approval_note,
            'rejection_note' => $this->report->note,
            'message' => $this->report->status==='approved' ? 'Laporan disetujui' : 'Laporan ditolak'
        ];
    }
}
