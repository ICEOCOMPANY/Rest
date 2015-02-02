<?php
/**
 * Created by PhpStorm.
 * Author: dawid
 * Date: 30.01.15
 * Time: 17:16
 */

namespace Configs\Helpers;


class Mailer extends \Base\Config{

    protected $defaultEmailFromAddress = 'ICEO CORE <no-reply@back.core.iceo.zone>';
    protected $mailGunApiKey = 'key-f88f80e3837adc90257107fd0d1f824b';
    protected $mailGunDomain = "sandboxa713f41ee0804cdda4dea26cf358f4cc.mailgun.org";
    protected $filesRoot;
    protected $cacheTemplatesPath;

    protected $varsAvailableInTemplate;

    /**
     * Tablica powiązań szablon -> potrzebne dla niego obrazki z katalogu $this->filesRoot
     * @var array
     */
    protected $imageDependencies = array(
        "registered" => array("iceo_agency_mini_logo.png"),
        "resetpassword" => array("iceo_agency_mini_logo.png")
    );

    /**
     * @return mixed
     */
    public function getDefaultEmailFromAddress()
    {
        return $this->defaultEmailFromAddress;
    }

    /**
     * @return mixed
     */
    public function getFilesRoot()
    {
        return $this->filesRoot;
    }

    /**
     * @return array
     */
    public function getImageDependencies()
    {
        return $this->imageDependencies;
    }

    /**
     * @return string
     */
    public function getMailGunApiKey()
    {
        return $this->mailGunApiKey;
    }

    /**
     * @return string
     */
    public function getMailGunDomain()
    {
        return $this->mailGunDomain;
    }

    /**
     * @return string
     */
    public function getCacheTemplatesPath()
    {
        return $this->cacheTemplatesPath;
    }

    /**
     * @return array
     */
    public function getVarsAvailableInTemplate()
    {
        return $this->varsAvailableInTemplate;
    }


    public function __construct(){
        $this->filesRoot =  APP_PATH."/Helpers/MailTemplates";
        $this->cacheTemplatesPath = APP_PATH."/Cache/Mailer/";

        $this->varsAvailableInTemplate = array(
            "front_app_url" => "http://front.core.iceo.zone/app/#/"
        );
    }
} 