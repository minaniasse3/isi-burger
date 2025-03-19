<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderCreated extends Notification implements ShouldQueue
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

        return (new MailMessage)
            ->subject('Confirmation de votre commande - ISI BURGER')
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Nous avons bien reçu votre commande n°' . $this->order->id . '.')
            ->line('Montant total: ' . number_format($this->order->total_amount, 0, ',', ' ') . ' FCFA')
            ->line('Méthode de paiement: ' . ($this->order->payment_method === 'especes' ? 'Espèces' : 'Carte bancaire'))
            ->line('Votre commande est en cours de préparation. Nous vous informerons dès qu\'elle sera prête.')
            ->action('Voir ma commande', $url)
            ->line('Merci d\'avoir choisi ISI BURGER!');
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