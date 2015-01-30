<?php
/**
 * Created by PhpStorm.
 * Author: dawid
 * Date: 28.01.15
 * Time: 10:29
 */

namespace Helpers;

require APP_PATH.'/Libs/MailGun/autoload.php';
use Mailgun\Mailgun;

/**
 * Class Mailer
 * Klasa służąca do wysyłania maili z aplikacji
 * Na tą chwilę opera się na zewnętrznej systemu - mailgun.com
 * @package Helpers
 */
class Mailer {
    private $mgClient;
    private $domain;
    private $defaultFrom;

    /**
     * Tablica powiązań szablon -> potrzebne dla niego obrazki z katalogu $this->filesRoot
     * @var array
     */
    private $imageDependiences = array(
        "registered" => array("iceo_agency_mini_logo.png")
    );

    /**
     * Ustawienia potrzebne do konfiguracji połączenia z MailGun
     */
    public function __construct(){
        $this->defaultFrom = \Helpers\Consts::defaultEmailFromAddress;
        $this->mgClient = new Mailgun('key-f88f80e3837adc90257107fd0d1f824b');
        $this->domain = "sandboxa713f41ee0804cdda4dea26cf358f4cc.mailgun.org";
        $this->filesRoot = APP_PATH."/Helpers/MailTemplates";
    }

    /**
     * Wysyłanie emaila w klasyczny sposób
     * @param $recipientName
     * @param $recipientEmail
     * @param $subject
     * @param $content
     * @param array $inline
     * @return \stdClass
     */
    public function SendEmail($recipientName , $recipientEmail, $subject , $content  ,$inline = array()){

        $result = $this->mgClient->sendMessage(
            $this->domain,
            array(
                'from'    => $this->defaultFrom,
                'to'      => $recipientName . '<'.$recipientEmail.'>',
                'subject' => $subject,
                'html'    => $content
            ),
            array('inline'   =>  $inline)
        );

        return $result;
    }

    /**
     * Wysłanie emaila w oparciu o wcześniej utworzony szablon
     * Szablony są tworzone przez wbudowany w Phalcona system - Volt
     * Posiada automatyczne cachowanie
     * i możliwość dołączenia obrazków
     * @param $template
     * @param $variables
     * @param $recipientName
     * @param $recipientEmail
     * @param $subject
     * @return \stdClass
     */
    public function SendTemplateEmail($template,$variables,$recipientName , $recipientEmail, $subject){

        $view = new \Phalcon\Mvc\View();
        $di = new \Phalcon\DI\FactoryDefault();

        $view->setDi($di);
        $view->setViewsDir($this->filesRoot);

        $view->registerEngines(array(
            ".phtml" => function($view, $di) {
                    $volt = new \Phalcon\Mvc\View\Engine\Volt($view, $di);

                    $volt->setOptions(array(
                        "compiledPath" => APP_PATH."/Cache/Mailer/"
                    ));


                    return $volt;
                }
        ));

        $view->post = $variables;
        $view->test_the_if = true;

        $content = $view->getRender('templates', $template);

        $inline = array();
        if(array_key_exists($template,$this->imageDependiences)){
            foreach($this->imageDependiences[$template] as $fileName){
                array_push($inline,'@'.$this->filesRoot.'/images/'.$fileName);
            }

        }

        return $this->SendEmail($recipientName,$recipientEmail,$subject,$content,$inline);
    }
} 