<?php

namespace AppMail;

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

class NotifService implements NotifInterface {
    private Mailer $mailer;
    private string $from;

    public function __construct() {
        $dsn = getenv('MAILER_DSN') ?: 'smtp://mailcatcher:1025';
        $this->from = getenv('MAILER_FROM') ?: 'no-reply@photopro.local';

        $transport = Transport::fromDsn($dsn);
        $this->mailer = new Mailer($transport);
    }

    public function send(string $to, string $subject, string $text): void {
        $email = (new Email())
            ->from($this->from)
            ->to($to)
            ->subject($subject)
            ->text($text);

        $this->mailer->send($email);
    }
}
