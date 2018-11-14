<?php 
namespace Common\Service;
class MailService {
    protected  $config = null;
    protected  $error = '';
	public function __construct() {
		$this->MailService();
	}
	public function getMailer() {
	    static $mailer = null;
	    if ( $mailer == null) {
            import('Common.Lib.PHPMailer.PHPMailerAutoload','','.php');
            $mailer = new \PHPMailer;
            $config = &$this->config;
            //$mail->SMTPDebug = 3;                               // Enable verbose debug output

            $mailer->isSMTP();                                      // Set mailer to use SMTP
            $mailer->CharSet = "utf-8";
            $mailer->Host = $config['server'];  // Specify main and backup SMTP servers
            $mailer->SMTPAuth = true;                               // Enable SMTP authentication
            $mailer->Username = $config['user'];                 // SMTP username
            $mailer->Password = $config['pwd'];                           // SMTP password
            $mailer->SMTPSecure = $config['secure'];                            // Enable TLS encryption, `ssl` also accepted
            $mailer->Port = $config['port'];                                    // TCP port to connect to
            $mailer->setFrom($config['from'], $config['name']);
            return $mailer;
        }
    }
	public function MailService() {
        $config = C('MAIL_CONFIG');
        if (empty($config)) {
            E('config_first');
        }
        $this->config = $config;
	}
	
	public function send($email, $subject, $body) {
	    $mailer = $this->getMailer();

        //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
        //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
        $mailer->isHTML(true);                                  // Set email format to HTML

        $mailer->Subject = $subject;
        $mailer->Body    = $body;
        //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
        $mailer->clearAddresses();
        $mailer->addAddress($email);               // Name is optional
        if(!$mailer->send()) {
            E($mailer->ErrorInfo);
        }
	}
	//发送带二维码图片的邮件
	public function codesend($email, $subject, $body, $img) {
	    $mailer = $this->getMailer();

        //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
        //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
        $mailer->isHTML(true);                                  // Set email format to HTML

        $mailer->Subject = $subject;
        $mailer->AddEmbeddedImage($img, 'logoimg', $img);
        $mailer->Body    = $body;
        //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
        $mailer->clearAddresses();
        $mailer->addAddress($email);               // Name is optional
        if(!$mailer->send()) {
            E($mailer->ErrorInfo);
        }
	}

	public function sendBulk($emails, $subject, $body) {
        $mailer = $this->getMailer();
        $bulk = $this->config['bulk'];
        if (empty($bulk))
            $bulk = 40;
        //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
        //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
        $mailer->isHTML(true);                                  // Set email format to HTML

        $mailer->Subject = $subject;
        $mailer->Body    = $body;
        //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
        $max = count($emails);
        $cnt = 0;
        $success = 0;
        foreach($emails as $email) {
            if ($cnt == 0)
                $mailer->clearAddresses();
            $mailer->addAddress($email);
            $cnt ++;
            if ($cnt == $bulk || $cnt == $max) {
                if($mailer->send()) {
                    $success ++;
                } else {
                    $this->error = $mailer->ErrorInfo;
                }
                $cnt = 0;

            }

        }
    }

}