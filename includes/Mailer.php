<?php

require_once 'MimeType.php';
require_once 'PHPMailer/PHPMailer.php';
require_once 'Message.php';

class SimpleMailer {

    private $stream = null;
    private $config = array();
    private $error = '';
    private $direction = null;
    private $configuration = array();

    const TEXT  = 0;
    const MULTIPART = 1;
    const MESSAGE = 2;
    const APPLICATION = 3;
    const AUDIO = 4;
    const IMAGE = 5;
    const VIDEO = 6;
    const OTHER = 7;

    const OUTGOING_SERVER = 0;
    const INCOMING_SERVER = 1;
    
    public function __construct($direction = self::INCOMING_SERVER, $autoConnect = true) {
        $this->direction = $direction;
        $this->configuration = array('email', array(
        'outgoing' => array(
                'transport' => 'sendmail', // smtp or sendmail or mail
                'parameters' => array(
                    'path' => '/usr/sbin/sendmail', // pour sendmail, pas nécessaire pour smtp
                ),
                'fromName' => 'skyspa.ca',
                'fromAddress' => 'info@skyspa.ca',
            ),
            'adminAddress' => 'info@skyspa.ca',
        ));
    
        if ($autoConnect) {
            $this->connect();
        }
    }

    public function  __destruct() {
        $this->disconnect();
    }

    public function connect() {
        if ($this->direction == self::INCOMING_SERVER) {
            return $this->connectToIncoming();
        } else {
            return $this->setOutgoing();
        }
    }

    public function disconnect() {
        if ($this->direction == self::INCOMING_SERVER) {
            if (!@imap_close($this->stream, CL_EXPUNGE)) {
                $this->error = '[ERR_DISCONNECT] - ' . imap_last_error();
                return false;
            }
        } else {
            if ($this->stream->Mailer == 'smtp') {
                $this->stream->SmtpClose();
            }
        }
        return true;
    }

    public function getError() {
        return $this->error;
    }

    public function getMessages($range = null)
    {
        if ($this->direction == self::INCOMING_SERVER) {
            $result = array();
            try {
                if ($range === null) {
                    $check = imap_check($this->stream);
                    $nbMessages = $check->Nmsgs;
                    if ($nbMessages > 0) {
                        $range = "1:" . $check->Nmsgs;
                    } else {
                        return array();
                    }
                }
                $messages = imap_fetch_overview($this->stream, $range);
                foreach ($messages as $message) {
                    $result[$message->msgno] = new SimpleMessage($this->stream, (array) $message);
                }
            } catch (Exception $e) {
                $this->error = $e->getMessage() . $e->getLine() . "\n" . '[ERR_GET_MESSAGES] - ' . imap_last_error();
                $result = false;
            }
            return $result;
        } else {
            $this->error = 'ERR_BAD_DIRECTION';
            return false;
        }
    }

    public function getStats() {
        if ($this->direction == self::INCOMING_SERVER) {
            $stats = imap_mailboxmsginfo($this->stream);
            return ((array)$stats);
        } else {
            $this->error = 'ERR_BAD_DIRECTION';
            return array();
        }
    }

    public function getStream() {
        return $this->stream;
    }

    public function isConnected() {
        if ($this->direction == self::INCOMING_SERVER) {
            return imap_ping($this->stream);
        } else { 
            return true;
        }
    }

    public function send(SimpleMessage $message)
    {   
        if ($this->direction == self::OUTGOING_SERVER) {
            $retVal = true;
            try {
                $this->stream->CharSet = $this->configuration['main']['charset'];
                $this->stream->From = $message->getFromAddress();
                $this->stream->FromName = $message->getFromName();
                $this->stream->Subject = $message->getSubject();


                if ($message->getHtmlBody() != '') {
                    $this->stream->Body = $message->getHtmlBody();
                    $this->stream->IsHTML(true);
                }

                if ($message->getPlainTextBody() != '') {
                    if ($message->getHtmlBody() != '') {
                        $this->stream->AltBody = $message->getPlainTextBody();
                    } else {
                        $this->stream->Body = $message->getPlainTextBody();
                    }
                }

                $this->stream->ClearAttachments();
                if ($message->hasAttachments()) {
                    foreach($message->getAttachments() as $attachment) {
                        if (file_exists($attachment)) {
                            $filename = @pathinfo($attachment, PATHINFO_BASENAME);
                            $this->stream->AddAttachment($attachment, $filename, 'base64', MimeType::getType($filename));
                        }
                    }
                }

                // To
                $this->stream->ClearAddresses();
                if (is_array($message->getTo())) {
                    foreach($message->getTo() as $to) {
                        $this->stream->AddAddress($to);
                    }
                } else {
                    if ($message->getTo() != '') {
                        $this->stream->AddAddress($message->getTo());
                    }
                }

                // Cc
                $this->stream->ClearCCs();
                if (is_array($message->getCc())) {
                    foreach($message->getCc() as $cc) {
                        $this->stream->AddCC($cc);
                    }
                } else {
                    if ($message->getCc() != '') {
                        $this->stream->AddCC($message->getCc());
                    }
                }

                // Bcc
                $this->stream->ClearBCCs();
                if (is_array($message->getBcc())) {
                    foreach($message->getBcc() as $bcc) {
                        $this->stream->AddBCC($bcc);
                    }
                } else {
                    if ($message->getBcc() != '') {
                        $this->stream->AddBCC($message->getBcc());
                    }
                }

                $this->stream->ClearReplyTos();
                if ($message->getReplyTo() != '') {
                    $this->stream->AddReplyTo($message->getReplyTo());
                }

                if (!$this->stream->Send())  {
                    $retVal = false;
                    $this->error = $this->stream->ErrorInfo;
                }
            }
            //catch exception
            catch(Exception $e) {
               $this->error = 'SimpleMailer::send() - ' . $e->getMessage() . ' on line ' . $e->getLine();
               $retVal = false;
            }

            return $retVal;

        } else {
            $this->error = 'ERR_BAD_DIRECTION';
            return false;
        }
    }

    /**
     * Connexion au serveur entrant
     *
     * @return boolean TRUE si succès , FALSE si failure.
     */
    private function connectToIncoming() {
       try {

            $this->config = $this->configuration['email']['incoming'];

            // Set APOP
            $secure = isset($this->config['parameters']['secure_password']) ? ($this->config['parameters']['secure_password'] ? '/secure' : '') : '';

            // Set encryption
            $encryption = '';
            if ($this->config['parameters']['encryption'] !== false) {
                $encryption = '/' . $this->config['parameters']['encryption'];
            }
            $encryption .= $this->config['parameters']['validate_certificate'] ? '/validate-cert' : '/novalidate-cert';

            // Set mailbox
            $mailbox = isset($this->config['parameters']['mailbox']) ? $this->config['parameters']['mailbox'] : 'INBOX';

            // Set timeouts
            if (isset($this->config['parameters']['open_timeout'])) { imap_timeout(IMAP_OPENTIMEOUT, $this->config['parameters']['open_timeout']); }
            if (isset($this->config['parameters']['read_timeout'])) { imap_timeout(IMAP_READTIMEOUT, $this->config['parameters']['read_timeout']); }
            if (isset($this->config['parameters']['write_timeout'])) { imap_timeout(IMAP_WRITETIMEOUT, $this->config['parameters']['write_timeout']); }
            if (isset($this->config['parameters']['close_timeout'])) { imap_timeout(IMAP_CLOSETIMEOUT, $this->config['parameters']['close_timeout']); }

            $this->stream = imap_open('{' . $this->config['host'] . ':' . $this->config['port'] . '/service=' . $this->config['protocol'] . $encryption . $secure. '}' . $mailbox, $this->config['username'], $this->config['password'], OP_SILENT | OP_SHORTCACHE);
            imap_gc($this->stream, IMAP_GC_ELT | IMAP_GC_ENV | IMAP_GC_TEXTS);
        } catch (Exception $e) {
            $this->error = '[ERR_CONNECT_TO_INCOMING] - ' . imap_last_error();
            return false;
        }
        return true;
    }

    /**
     * Initialisation du serveur sortant
     *
     * @return boolean TRUE si succès , FALSE si failure.
     */
    private function setOutgoing() {
       try {
           $this->stream = new PHPMailer();
           $outgoing = $this->configuration['email']['outgoing'];
           if (empty($outgoing)) {
               $this->error = 'ERR_NO_CONFIG';
               return false;
           } else {
                $parameters = $outgoing['parameters'];
                $transport = $outgoing['transport'];
                switch ($transport) {
                    case 'mail' :
                        $this->stream->IsMail();
                        break;
                    case 'smtp' :
                        $this->stream->IsSMTP();
                        if (isset($parameters['host'])) { $this->stream->Host = $parameters['host']; }
                        if (isset($parameters['port'])) { $this->stream->Port = $parameters['port']; }
                        if (isset($parameters['connection_prefix'])) {  $this->stream->SMTPSecure = $parameters['connection_prefix']; }
                        if (isset($parameters['smtp_auth'])) { $this->stream->SMTPAuth = $parameters['smtp_auth']; }
                        if (isset($parameters['username'])) { $this->stream->Username = $parameters['username']; }
                        if (isset($parameters['password'])) { $this->stream->Password = $parameters['password']; }
                        if (isset($parameters['timeout'])) { $this->stream->Timeout = $parameters['timeout']; }
                        break;
                    default :
                        $this->stream->IsSendMail();
                        if (isset($parameters['path'])) { $this->stream->Sendmail = $parameters['path']; }
                        break;
                }
            }
        } catch (Exception $e) {
            $this->error = 'ERR_SET_OUTGOING - ' . $e->getMessage();
            return false;
        }
        return true;
    }

}


