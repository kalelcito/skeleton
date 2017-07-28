<?php

namespace CoreBundle\Services;
use Swift_Attachment;

class Mailer
{
    private $mailer;
    private $dev_mail;
    public function __construct($mailer,$devmail){
        $this->mailer=$mailer;
        $this->dev_mail=$devmail;
    }
    public function send($from,$to,$subject,$body,$attachment=null){

        $to=$this->to($to);
        if($attachment!=null){
            $message = \Swift_Message::newInstance()
                ->setSubject($subject)
                ->setFrom($from)
                ->setTo($to)
                ->attach(Swift_Attachment::fromPath($attachment))
                ->setBody($body, 'text/html');

        }else{
            $message = \Swift_Message::newInstance()
                ->setSubject($subject)
                ->setFrom($from)
                ->setTo($to)
                ->setBody($body, 'text/html');
        }
        $this->mailer->send($message);
    }
    private function to($to){
        $url=$_SERVER['HTTP_HOST'];
        $local=array("192.168.0.200","localhost:8888","localhost","127.0.0.1");
        if (in_array($url,$local)) {
            $to = $this->dev_mail;
        }
        return $to;
    }
}