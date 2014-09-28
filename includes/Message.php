<?php

class Message {

	private $stream = null;
	private $error = '';
	private $messageNumber = -1;
	private $date = null;
	private $htmlBody = null;
	private $plainTextBody = null;
	private $subject = null;
	private $from = null;
	private $fromName = null;
	private $fromAddress = null;
	private $to = '';
	private $cc = '';
	private $bcc = '';
	private $replyTo = null;
	private $attachments = array();
	private $size = '';
	private $uid = '';
	private $messageId = '';
	private $structure = null;
	private $originalStructure = null;
	private $_hasAttachments = null;
	private $otherRecipients = null;

	const TEXT_TYPE  = 0;
	const MULTIPART_TYPE = 1;
	const MESSAGE_TYPE = 2;
	const APPLICATION_TYPE = 3;
	const AUDIO_TYPE = 4;
	const IMAGE_TYPE = 5;
	const VIDEO_TYPE = 6;
	const OTHER_TYPE = 7;

	const SEVEN_BIT_ENCODING = 0;
	const EIGHT_BIT_ENCODING = 1;
	const BINARY_ENCODING = 2;
	const BASE64_ENCODING = 3;
	const QUOTE_PRINTABLE_ENCODING = 4;
	const OTHER_ENCODING = 5;

	const TEXT_PLAIN = 0;
	const TEXT_HTML = 1;
	const MULTIPART_ALTERNATIVE = 2;
	const MESSAGE_RFC822 = 3;

	public function __construct($stream = null, $message = null) {
		if ($stream !== null && $message !== null) {
			$this->stream = $stream;
			$this->messageNumber = $message['msgno'];
			$this->subject = $this->makeItReadable($message['subject']);
			$this->from = $this->makeItReadable($message['from']);
			$this->date = $message['date'];
			$this->size = $message['size'];
			$this->uid = $message['uid'];
			$this->to = $message['to'];
			$emailParts = $this->getEmailPart($this->makeItReadable($message['from']));
			$this->fromAddress = $emailParts['address'];
			$this->fromName = $emailParts['name'];
			$this->from = trim($this->fromAddress . ' ' . $this->fromName);
			$this->replyTo = isset($message['in_reply_to']) ? $message['in_reply_to'] : $this->from;
			$this->originalStructure = imap_fetchstructure($this->stream, $this->messageNumber);
			$this->structure = $this->getStructure();
		}
	}

	public function delete() {
		$return = false;
		if (imap_delete($this->stream, $this->messageNumber)) {
			$return = true;
		} else {
			SimpleLogger::log('[ERR_MESSAGE_DELETE] - FROM: ' . $this->from . ' - MSGNO: ' . $this->messageNumber);
		}
		return $return;
	}

	public function getBody($withRaw = false) {
		$body = array(
			'plain_text' => $this->getPlainTextBody(),
			'html' => $this->getHtmlBody(),
		);
		if ($withRaw) { $body['raw'] = $this->getRawBody(); }
		return $body;
	}

	public function getAttachments() {
		return $this->attachments;
	}

	public function getBcc() {
		return $this->bcc;
	}

	public function getCc() {
		return $this->cc;
	}

	public function getError() {
		return $this->error;
	}

	public function getFrom() {
		return $this->from;
	}

	public function getFromAddress() {
		return $this->fromAddress;
	}

	public function getFromName() {
		return $this->fromName;
	}

	public function getDate() {
		return $this->date;
	}

	public function getHeader() {
		if ($this->structure !== null) {
			return $this->structure[0]['data'];
		}
		return '';
	}

	public function getHtmlBody() {
		if ($this->htmlBody === null) {
			$this->htmlBody = '';
			if ($this->structure !== null) {
				$htmlMimeType = $this->convertMimeType(self::TEXT_HTML);
				$indexes = array_keys($this->structure);

				foreach($indexes as $index) {
					$part = $this->structure[$index];
					if ($part['mime'] == $htmlMimeType) {
						if (strtoupper($part['charset']) != Configuration::getValue('main', 'charset')) {
							$this->htmlBody = $part['data'];
						} else {
							$this->htmlBody = mb_convert_encoding($part['data'], Configuration::getValue('main', 'charset'), $part['charset']);
						}
						break;
					}
				}
			}
		}
		return $this->htmlBody;
	}

	public function getMessageNumber() {
		return $this->messageNumber;
	}

	public function getOtherRecipients() {
		if ($this->otherRecipients === null) {
			$this->otherRecipients = array();
			$header = $this->parseHeaders($this->getHeader());
			if (isset($header['Cc'])) {
				$ccs = explode(',', $header['Cc']);
				foreach($ccs as $cc) {
					$emailParts = $this->getEmailPart($this->makeItReadable($cc));
					if ($emailParts['address'] != Configuration::getValue('ticket', 'defaultFromAddress')) {
						$this->otherRecipients[] = $emailParts['address'];
					}
				}
			}

			if (isset($header['To'])) {
				$tos = explode(',', $header['To']);
				foreach($tos as $to) {
					$emailParts = $this->getEmailPart($this->makeItReadable($to));
					if ($emailParts['address'] != Configuration::getValue('ticket', 'defaultFromAddress')) {
						$this->otherRecipients[] = $emailParts['address'];
					}
				}
			}
		}
		return $this->otherRecipients;
	}

	public function getPlainTextBody() {
		if ($this->plainTextBody === null) {
			$this->plainTextBody = '';
			if ($this->structure !== null) {
				$plainTextMimeType = $this->convertMimeType(self::TEXT_PLAIN);
				$indexes = array_keys($this->structure);

				array_splice($indexes, 0, 1);
				foreach($indexes as $index) {
					$part = $this->structure[$index];
					if ($part['mime'] == $plainTextMimeType) {
						if (strtoupper($part['charset']) == Configuration::getValue('main', 'charset')) {
							$this->plainTextBody = $part['data'];
						} else {
							$this->plainTextBody = mb_convert_encoding($part['data'], Configuration::getValue('main', 'charset'), $part['charset']);
						}
						break;
					}
				}
				$headers = SimpleMessage::parseHeaders($this->getHeader());
				if (isset($headers['X-Mailer'])) {
					if ($this->getHtmlBody() != '') {
						$mailSoftware = array('outlook');
						foreach($mailSoftware as $software) {
							if (strpos(strtolower($headers['X-Mailer']), $software) !== false) {
								$this->plainTextBody = preg_replace('/(\r\n){2}/', "\n", $this->plainTextBody);
								break;
							}
						}
					}
				}
			}
		}
		return $this->plainTextBody;
	}

	public function getRawBody() {
		if ($this->stream !== null) {
			return imap_body($this->stream, $this->messageNumber);
		} else {
			return '';
		}
	}

	public function getSize() {
		return $this->size;
	}

	public function getSubject() {
		if ($this->subject === null) {
			$this->subject = $this->makeItReadable($this->subject);
		}
		return $this->subject;
	}

	public function getUid() {
		return $this->Uid;
	}

	public function getMessageId() {
		return $this->messageId;
	}

	public function getReplyTo() {
		return $this->replyTo;
	}

	public function getTo() {
		return $this->to;
	}

	public function hasAttachments() {
		if ($this->_hasAttachments === null) {
			if ($this->stream !== null) {
				$this->_hasAttachments = false;
				foreach($this->structure as $index => $part) {
					if (isset($part['is_attachment'])) {
						$this->_hasAttachments = true;
						break;
					}
				}
			} else {
				$this->_hasAttachments = count($this->attachments) > 0;
			}
		}
		return $this->_hasAttachments;
	}

	public function saveAttachments($mySqlId) {
		$hasParentFolder = true;
		try {
			if ($this->structure !== null) {
				if ($this->hasAttachments()) {
					$path =  Configuration::getValue('path', 'attachments') .
							 DIRECTORY_SEPARATOR .
							 $mySqlId;
					if (!file_exists($path)) { $hasParentFolder = mkdir($path); chmod($path, 0777); }

					if ($hasParentFolder) {
						foreach($this->structure as $index => $part) {
							if (isset($part['is_attachment'])) {
								$filename = $this->makeItReadable(isset($part['filename']) ? $part['filename'] : $part['name']);
								$extension = @pathinfo($filename, PATHINFO_EXTENSION);
								if ($part['mime'] == $this->convertMimeType(self::MESSAGE_RFC822) && empty($extension)) { $filename .= '.eml'; }
								$filename = $this->getSafeName($filename);
								$this->attachments[] = "$path/$filename";
								file_put_contents("$path/$filename", $part['data']);
								chmod("$path/$filename", 0777);
							}
						}
					} else {
						return false;
					}
				}
				return true;
			}
		} catch (Exception $e) {
			$this->error = '[ERR_SAVE_ATTACHMENTS] - ' . $e->getMessage();
		}
		return false;
	}

	public function setFrom($address, $name = null) {
		$this->fromAddress = $address;
		$this->fromName = $name;
		$this->from = $name != '' ? trim($name) . ' ' . '<'. $address . '>' : $address;
	}

	public function setAttachments(array $value) {
		$this->attachments = $value;
	}

	public function setBcc($value) {
		$this->bcc = $value;
	}

	public function setCc($value) {
		$this->cc = $value;
	}

	public function setHtmlBody($value) {
		$this->htmlBody = $value;
	}

	public function setPlainTextBody($value) {
		$this->plainTextBody = $value;
	}

	public function setReplyTo($value) {
		$this->replyTo = $value;
	}

	public function setSubject($value) {
		$this->subject = $value;
	}

	public function setTo($value) {
		$this->to = $value;
	}

	public static function parseHeaders($headers)
	{
		$result = array();
		$headers = preg_replace('/\r\n\s+/m', '',$headers) . "\r\n";
		preg_match_all('/([^: ]+): (.+?(?:\r\n\s(?:.+?))*)?(\r\n)+/m', $headers, $matches);
		foreach ($matches[1] as $key =>$value) {
			$result[trim($value)] = trim($matches[2][$key]);
		}
		return $result;
	}

	private function convertMimeType($mimeType) {
		if ($mimeType == self::TEXT_PLAIN) {
			return "TEXT/PLAIN";
		} elseif ($mimeType == self::TEXT_HTML) {
			return "TEXT/HTML";
		} elseif ($mimeType == self::MULTIPART_ALTERNATIVE) {
			return "MULTIPART/ALTERNATIVE";
		} elseif ($mimeType == self::MESSAGE_RFC822) {
			return "MESSAGE/RFC822";
		} else {
			return false;
		}
	}

	private function decodePart($part, $prefix) {

		$decodedPart = array();
		$decodedPart['mime'] = $this->getMimeType($part);

		if ($part->ifdparameters) {
			foreach($part->dparameters as $object) {
				$decodedPart[strtolower($object->attribute)] = $object->value;
				if (strtolower($object->attribute) == 'filename') {
					$decodedPart['is_attachment'] = true;
					$decodedPart['filename'] = $object->value;
				}
			}
		}

		if ($part->ifparameters) {
			foreach($part->parameters as $object) {
				$decodedPart[strtolower($object->attribute)] = $object->value;
				if (strtolower($object->attribute) == 'name') {
					$decodedPart['is_attachment'] = true;
					$decodedPart['name'] = $object->value;
				}
			}
		}
		$decodedPart['data'] = imap_fetchbody($this->stream, $this->messageNumber, $prefix);
		if($part->encoding == self::BASE64_ENCODING) {
			$decodedStr = imap_base64($decodedPart['data']); // Décode en base64 seulement si c'est nécessaire.
			$decodedPart['data'] = $decodedStr === false ? $decodedPart['data'] : $decodedStr;
		} elseif ($part->encoding == self::QUOTE_PRINTABLE_ENCODING) {
			$decodedPart['data'] = quoted_printable_decode($decodedPart['data']);
		}
		return $decodedPart;
	}

	private function getMimeType($part) {
		$primaryMimeType = array("TEXT", "MULTIPART","MESSAGE", "APPLICATION", "AUDIO", "IMAGE", "VIDEO", "OTHER");
		if ($part->ifsubtype) {
			return $primaryMimeType[(int) $part->type] . '/' .$part->subtype;
		}
		return "TEXT/PLAIN";
	}

	private function getParts($part = null, $prefix = '0')
	{
		if ($part === null) { $part = $this->originalStructure; }
		$parts = array();
		$parts[$prefix] = $this->decodePart($part, $prefix);

		if (isset($part->parts)) // multipart
		{
			$prefix = ($prefix == "0") ? "" : "$prefix.";
			foreach ($part->parts as $number => $subpart) {
				$parts = array_merge($parts, $this->getParts($subpart, $prefix.($number+1)));
			}
		}
		return $parts;
	}

	private function getStructure($parseHeaders=false)
	{
		$structure = $this->getParts();
		$headers = self::parseHeaders($structure[0]["data"]);
		if ($parseHeaders) $structure[0]["parsed"] = $headers;
		if (count($structure) == 1) { // Html ou PlainText seulement.
			$rawBody = $this->getRawBody();
			$structure[1] = array(
				'mime' => $structure[0]['mime'],
				'charset' => $structure[0]['charset'],
				'data' => $this->originalStructure->encoding == self::BASE64_ENCODING ? base64_decode($rawBody) : quoted_printable_decode($rawBody),
			);
		}
		return $structure;
	}

	private function makeItReadable($str) {
		$returnStr = '';
		try {
			$elements = imap_mime_header_decode($str);
			for ($i=0; $i<count($elements); $i++) {
				if ($elements[$i]->charset != 'default') {
					$returnStr .=  mb_convert_encoding($elements[$i]->text, Configuration::getValue('main', 'charset'), $elements[$i]->charset);
				} else {
					$returnStr .= $elements[$i]->text;
				}
			}
		} catch (Exception $e) {
			return $str;
		}
		return trim($returnStr);
	}

	private function getEmailPart($str) {
		$parts = array('address' => trim($str), 'name' => '');
		if (preg_match('/([^\<]+)?\<([^\>]+)\>/', trim($str), $matches)) {
			$parts = array('address' => trim($matches[2]), 'name' => trim($matches[1]));
		}
		return $parts;
	}

	private function getSafeName($name)
	{
		$name = String::transformAccents($name);
		$name = strtolower($name);
		$name = preg_replace('/(_| )/', '-', trim($name));
		$name = preg_replace('/[^\w\-\.]/', '', $name);
		if (strlen($name) > 250) { // si le nom est trop long, on coupe
			$name = substr($name, 0, 249);
		}
		return $name;

	}
}
