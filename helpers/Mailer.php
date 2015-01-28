<?php
/**
 * Created by PhpStorm.
 * Author: dawid
 * Date: 28.01.15
 * Time: 10:29
 */

namespace Helpers;

require APP_PATH.'/libs/MailGun/autoload.php';
use Mailgun\Mailgun;


class Mailer {
    private $mgClient;
    private $domain;
    private $defaultFrom;

    public function __construct(){
        $this->defaultFrom = \Helpers\Consts::defaultEmailFromAddress;
        $this->mgClient = new Mailgun('key-f88f80e3837adc90257107fd0d1f824b');
        $this->domain = "sandboxa713f41ee0804cdda4dea26cf358f4cc.mailgun.org";

    }

    public function SendEmail($recipientName , $recipientEmail, $subject , $content){

# Make the call to the client.
        $result = $this->mgClient->sendMessage("$this->domain",
            array('from'    => $this->defaultFrom,
                'to'      => $recipientName . '<'.$recipientEmail.'>',
                'subject' => $subject,
                'text'    => $content));

        return $result;
    }
} 