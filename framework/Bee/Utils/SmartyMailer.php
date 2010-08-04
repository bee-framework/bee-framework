<?php
/*
 * Copyright 2008-2010 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * @throws Exception
 *
 */
class Bee_Utils_SmartyMailer {

	/**
	 * 
	 * @var Smarty
	 */
	private $smarty;

	/**
	 * Enter description here...
	 *
	 * @var String
	 */
	private $smtpHost;
	
	/**
	 * Enter description here...
	 *
	 * @var String
	 */
	private $smtpUsername;
	
	/**
	 * Enter description here...
	 *
	 * @var String
	 */
	private $smtpPassword;
	
	/**
	 * Enter description here...
	 *
	 * @var String
	 */
	private $defaultSenderAddress;
	
	/**
	 * Enter description here...
	 *
	 * @var String
	 */
	private $defaultSenderName;

    private $mailType = 'smtp';
	
	public function sendMail($recipient, $subjectTemplate, $messageTemplate, array $model, $senderAddress = false, $senderName = false) {

		$mail = new PHPMailer();
        $mail->CharSet = 'UTF-8';

        switch ($this->mailType) {
            case 'smtp' :
                $mail->IsSMTP();
                $mail->Host = $this->smtpHost;
                if(Bee_Utils_Strings::hasText($this->smtpUsername)) {
                    $mail->SMTPAuth = true;
                    $mail->Username = $this->smtpUsername;
                    $mail->Password = $this->smtpPassword;
                }
                break;
            
            case 'mail' :
                $mail->IsMail();
                break;
        }


		$mail->From = $senderAddress ? $senderAddress : $this->defaultSenderAddress;
		$mail->FromName = $senderName ? $senderName : $this->defaultSenderName;
		
		$mail->AddAddress($recipient);
		
		$this->smarty->clear_all_assign();
		foreach($model as $key => $value) {
			$this->smarty->assign($key, $value);
		}

		$mail->Subject = $this->smarty->fetch($subjectTemplate);
		$mail->Body = $this->smarty->fetch($messageTemplate);

		$mail->IsHTML(true);

		if(!$mail->Send()) {
			throw new Exception($mail->ErrorInfo);
		}
	}

	public function dumpMail($recipient, $subjectTemplate, $messageTemplate, array $model, $senderAddress = false, $senderName = false) {

		$mail = new PHPMailer();
		$mail->IsSMTP();
		$mail->CharSet = 'UTF-8';

		$mail->Host = $this->smtpHost;
		if(Bee_Utils_Strings::hasText($this->smtpUsername)) {
			$mail->SMTPAuth = true;
			$mail->Username = $this->smtpUsername;
			$mail->Password = $this->smtpPassword;
		}

		$mail->From = $senderAddress ? $senderAddress : $this->defaultSenderAddress;
		$mail->FromName = $senderName ? $senderName : $this->defaultSenderName;

		$mail->AddAddress($recipient);

		$this->smarty->clear_all_assign();
		foreach($model as $key => $value) {
			$this->smarty->assign($key, $value);
		}

		$mail->Subject = $this->smarty->fetch($subjectTemplate);
		$mail->Body = $this->smarty->fetch($messageTemplate);

		$mail->IsHTML(true);

        echo '<div style="border: solid 1px #f00; padding: 5px;">';
        echo $mail->Body;
        echo '</div>';
        exit();
	}

	/**
	 * 
	 * @return Smarty
	 */
	public final function getSmarty() {
		return $this->smarty;
	}

	/**
	 * 
	 * @param Smarty $smarty
	 * @return void
	 */
	public final function setSmarty(Smarty $smarty) {
		$this->smarty = $smarty;
	}

	/**
	 * Enter description here...
	 *
	 * @return String
	 */
	public final function getSmtpHost() {
		return $this->smtpHost;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param String $smtpHost
	 * @return void
	 */
	public final function setSmtpHost($smtpHost) {
		$this->smtpHost = $smtpHost;
	}

	/**
	 * Enter description here...
	 *
	 * @return String
	 */
	public final function getDefaultSenderAddress() {
		return $this->defaultSenderAddress;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param String $defaultSenderAddress
	 */
	public final function setDefaultSenderAddress($defaultSenderAddress) {
		$this->defaultSenderAddress = $defaultSenderAddress; 
	}
	
	/**
	 * Enter description here...
	 *
	 * @return String
	 */
	public final function getDefaultSenderName() {
		return $this->defaultSenderName;  
	}
	
	/**
	 * Enter description here...
	 *
	 * @param String $defaultSenderName
	 * @return void
	 */
	public final function setDefaultSenderName($defaultSenderName) {
		$this->defaultSenderName = $defaultSenderName;
	}
	
	/**
	 * Enter description here...
	 *
	 * @return String
	 */
	public final function getSmtpUsername() {
		return $this->smtpUsername;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param String $smtpUsername
	 * @return void
	 */
	public final function setSmtpUsername($smtpUsername) {
		$this->smtpUsername = $smtpUsername;
	}

	/**
	 * Enter description here...
	 *
	 * @param String $smtpPassword
	 */
	public final function setSmtpPassword($smtpPassword) {
		$this->smtpPassword = $smtpPassword;
	}

    /**
     * Gets the MailType
     *
     * @return String $mailType
     */
    public function getMailType() {
        return $this->mailType;
    }

    /**
     * Sets the MailType
     *
     * @param $mailType
     * @return void
     */
    public function setMailType($mailType) {
        $this->mailType = $mailType;
    }

}
?>