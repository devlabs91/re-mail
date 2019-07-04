<?php

namespace ReMailBundle\Client;

class Pop extends Imap {

    public function __construct($host, $port, $service = 'pop3') {
        parent::__construct($host, $port, $service);
    }

}
