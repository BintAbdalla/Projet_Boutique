<?php
// app/Jobs/EmailJob.php

// app/Jobs/EmailJob.php

namespace App\Jobs;


use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Log;
use App\Mail\ExampleMail;


class EmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $pdfpath;

    /**
     * Crée une nouvelle instance du job.
     *
     * @param User $user
     */
    public function __construct(User $user,$pdfpath)
    {
        $this->user = $user;
        $this->pdfpath = $pdfpath;
    }

    /**
     * Exécute le job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            // Envoyer l'e-mail
            Mail::to($this->user->login)->send(new WelcomeMail($this->user, $this->pdfpath));
            // Mail::to($this->user->login)->send(new ExampleMail(storage_path('emails.CarteFidelite')));

        } catch (\Exception $e) {
            // Enregistrez l'erreur pour le débogage
            Log::error('Erreur lors de l\'envoi de l\'email : ' . $e->getMessage());
        }
    }
}

