<?php
class email{
	
	private $to;
	private $subject;
	private $message;
	private $header;
	
	function __construct(){
		$this->header  = 'MIME-Version: 1.0' . "\r\n";
		$this->header .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	}
	
	function sent_mail(){
		return mail($this->to, $this->subject, $this->message, $this->header);
	}
	
	function set_subject($subject){
		$this->subject = $subject;
	}
	
	function set_to($recipient){
		if(is_array($recipient)){
			$recipient = implode(",", $recipient);
		}
		$this->to = $recipient;
	}
	
	function set_from($sender){
		$this->header .= 'From: '. $sender . "\r\n";
	}
	
	function set_cc($cc){
		if(is_array($cc)){
			$cc = implode(",", $cc);
		}
		$this->header .= 'Cc: '. $cc . "\r\n";
	}
	
	function set_bcc($bcc){
		if(is_array($bcc)){
			$bcc = implode(",", $bcc);
		}
		$this->header .= 'Bcc: ' . $bcc . "\r\n";
	}
	
	function set_reply_to($reply){
		$this->header .= 'Reply-To: ' . $reply . "\r\n";
	}
	
	function set_message($title, $body){
		$this->message = <<<EOF
<html>
<head>
  <title>$title</title>
</head>
<body>
  $body
</body>
</html>
EOF;
	}
}