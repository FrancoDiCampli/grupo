<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class Verificar extends Notification
{
    use Queueable;
    protected $user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $pass = str_shuffle(time());
        $this->user->code = Hash::make($pass);
        $this->user->save();

        return (new MailMessage)
            ->from('neaapc@grupoapc.com.ar', 'Grupo APC')
            ->subject('Registro en Grupo APC')
            ->greeting('Hola!')
            ->line('Su Código: ' . $pass)
            ->line('Click en el boton para continuar el registro.')
            ->action('Continuar', url('/'))
            ->line('Gracias!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
