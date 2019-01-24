<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class email_f_ven extends Mailable
{
    use Queueable, SerializesModels;
     
    /**
     * The obj_mail object instance.
     *
     * @var obj_mail
     *
     */
    public $obj_mail;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($obj_mail)
    {
        $this->obj_mail = $obj_mail;
    }
 
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->withSwiftMessage(function ($message)
        {
            $message->getHeaders()
                    ->addTextHeader('Custom-Header', 'Notificación de periodo de alerta de vencimiento');
        });
        return $this->from('sysadmin@televisa.com.mx')
                    ->view('email.alerta_vencimiento')
                    -> subject ('Notificación de periodo de alerta de vencimiento');
    }
}
