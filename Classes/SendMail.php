<?php

class SendMail
{
    private $fields;
    private $notificationEmail;

    public function __construct($fields, $notificationEmail = null)
    {
        $this->fields = $fields;
        $this->notificationEmail = $notificationEmail || !empty($notificationEmail) ? $notificationEmail : $this->notificationEmail();
    }

    public function serveRequest()
    {
        $success = mail($this->notificationEmail(), $this->mailSubject(), $this->mailText(), $this->mailHeaders());
		if(isset($_POST['ajax'])){
            self::answerJs($success ? 'success' : 'error');
        }else{
            self::redirect();
        }
    }

    public function mailHeaders()
    {
        return "MIME-Version: 1.0\r\nContent-type: text/html; charset=utf-8\r\nFrom: {$this->notificationFromEmail()}\r\n";
    }

    private function mailSubject()
    {
        return isset($_POST['subject']) ? Utils::clearValue($_POST['subject']) : 'Запрос с сайта '.$_SERVER['SERVER_NAME'];
    }


    private function notificationFromEmail()
    {
        $notificationFromEmail = get_option('common_site_settings')['notification_from_email'];
        if(empty($notificationFromEmail)){
            $notificationFromEmail = 'no-reply@'.$_SERVER['SERVER_NAME'];
        }
        return $notificationFromEmail;
    }

    public function notificationEmail()
    {
        return get_option('common_site_settings')['notification_email'];
    }

    private function mailText()
    {
		return array_reduce($this->fields, function ($mailText, $input){
			if(isset($_POST[$input[0]]) && !empty($_POST[$input[0]])){
				return $mailText . $input[1].': '.Utils::clearValue($_POST[$input[0]]).'<br>';
			}else{
				return $mailText;
			}
		}, '');
    }

    public static function redirect()
    {
        if(isset($_POST['redirect'])){
            $redirectUrl = $_POST['redirect'];
        }elseif (isset($_SERVER['HTTP_REFERER'])){
            $redirectUrl = $_SERVER['HTTP_REFERER'];
        }else{
            $redirectUrl = Utils::getRequestScheme().'://'.$_SERVER['SERVER_NAME'];
        }
        header('Location: '.$redirectUrl);
        exit(0);
    }

    public static function answerJs($status)
    {
        header($_SERVER['SERVER_PROTOCOL'].' 200 OK');
        echo json_encode(["status" => $status]);
        exit(0);
    }

    public static function badRequest()
    {
        if(isset($_POST['ajax'])){
            self::answerJs('error');
        }else{
            self::redirect();
        }
    }

    public static function goodRequest()
    {
        if(isset($_POST['ajax'])){
            self::answerJs('success');
        }else{
            self::redirect();
        }
    }

}
