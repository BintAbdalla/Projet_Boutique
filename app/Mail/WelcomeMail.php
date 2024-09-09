<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $pdfPath;

    public function __construct($user,$pdfPath)
    {
        $this->user = $user;
        $this->pdfPath = $pdfPath;
    }

    // public function build()
    // {
    //     return $this->view('emails.texte_mail')
    //                 ->with(['user' => $this->user])
    //                 ->subject('Carte_Fidelité');
    // }


    public function build()
    {
        return $this->view('emails.texte_mail') // Vue de l'email
                    ->with([
                        'user' => $this->user,
                    ])
                    ->subject('Carte de Fidélité') // Sujet de l'email
                    ->attach($this->pdfPath); // Pièce jointe
    }
}
