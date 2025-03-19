<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderReady extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = route('orders.show', $this->order);
        
        // Générer la facture PDF
        $pdf = PDF::loadView('emails.invoice', ['order' => $this->order]);

        return (new MailMessage)
            ->subject('Votre commande est prête - ISI BURGER')
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Votre commande n°' . $this->order->id . ' est prête!')
            ->line('Vous pouvez venir la récupérer à notre comptoir.')
            ->line('Montant à payer: ' . number_format($this->order->total_amount, 0, ',', ' ') . ' FCFA')
            ->line('Méthode de paiement: ' . ($this->order->payment_method === 'especes' ? 'Espèces' : 'Carte bancaire'))
            ->action('Voir ma commande', $url)
            ->line('Vous trouverez ci-joint votre facture.')
            ->line('Merci d\'avoir choisi ISI BURGER!')
            ->attachData($pdf->output(), 'facture_' . $this->order->id . '.pdf', [
                'mime' => 'application/pdf',
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'amount' => $this->order->total_amount,
            'status' => $this->order->status,
        ];
    }
} 