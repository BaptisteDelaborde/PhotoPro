<?php

namespace AppMail;

interface NotifInterface {
    public function send(string $to, string $subject, string $text): void;
}
