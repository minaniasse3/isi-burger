<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderForManager extends Notification implements ShouldQueue
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
            ->subject('Nouvelle commande - ISI BURGER')
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Une nouvelle commande a été passée.')
            ->line('Commande n°: ' . $this->order->id)
            ->line('Client: ' . $this->order->user->name)
            ->line('Montant total: ' . number_format($this->order->total_amount, 0, ',', ' ') . ' FCFA')
            ->line('Méthode de paiement: ' . ($this->order->payment_method === 'especes' ? 'Espèces' : 'Carte bancaire'))
            ->action('Voir la commande', $url)
            ->line('Merci de traiter cette commande rapidement.');
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
            'user_id' => $this->order->user_id,
            'amount' => $this->order->total_amount,
        ];
    }
} 