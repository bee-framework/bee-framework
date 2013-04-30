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

    private $pluginDir;

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
	private $smtpPort = 25;

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
     * @var string
     */
    private $smtpSecurity;

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

    /**
     * @var string
     */
    private $defaultRecipientAddress;

    /**
     * @var string
     */
    private $defaultRecipientName;


    private $mailType = 'mail';


    /**
     * @param $subjectTemplate
     * @param $bodyTemplate
     * @param array $model
     * @param mixed $recipient Either string example@mail.com or array with keys "address" and optional "name"
     * @param mixed $sender Either string example@mail.com or array with keys "address" and optional "name"
     */
    public function sendMail($subjectTemplate, $bodyTemplate, array $model, $recipient=null, $sender=null) {
        $phpMailer = $this->instantiatePhpMailer($subjectTemplate, $bodyTemplate, $model, $recipient, $sender);

        if (!$phpMailer->Send()) {
            throw new Exception($phpMailer->ErrorInfo);
        }
        return true;
	}

    /**
     * @param $subjectTemplate
     * @param $bodyTemplate
     * @param array $model
     * @param mixed $recipient Either string example@mail.com or array with keys "address" and optional "name"
     * @param mixed $sender Either string example@mail.com or array with keys "address" and optional "name"
     */
    public function dumpMail($subjectTemplate, $bodyTemplate, array $model, $recipient=null, $sender=null, $exit=true) {
        $phpMailer = $this->instantiatePhpMailer($subjectTemplate, $bodyTemplate, $model, $recipient, $sender);
        // TODO: inlude bcc and cc in wrapper!
        $phpMailer->AddBCC('b.hartmann@arend-hartmann.com', 'Benjamin Hartmann');

        echo '<div style="border: solid 1px #f00; padding: 5px; margin: 10px;">';
        echo 'To:<br/>';
        echo '=> Address: '.$recipient['address'].'<br/>';
        echo '=> Name: '.$recipient['name'].'<br/>';
        echo '<br/>';
        echo 'Subject: "'.$phpMailer->Subject.'"<br/>';
        echo '</div>';

        echo '<div style="border: solid 1px #f00; padding: 5px; margin: 10px;">';
        echo $phpMailer->Body;
        echo '</div>';

        if ($exit) {
            exit();
        }
	}


    /**
     * @param $subjectTemplate
     * @param $bodyTemplate
     * @param array $model
     * @param mixed $recipient Either string example@mail.com or array with keys "address" and optional "name"
     * @param mixed $sender Either string example@mail.com or array with keys "address" and optional "name"
     *
     * @return PHPMailer
     */
    public function instantiatePhpMailer($subjectTemplate, $bodyTemplate, array $model, $recipient=null, $sender=null) {
        $phpMailer = new PHPMailer(true);
        $phpMailer->PluginDir = $this->getPluginDir();
        $phpMailer->CharSet = 'UTF-8';
        $phpMailer->IsHTML(true);


        // SET CONNECTION
        switch ($this->getMailType()) {
            case 'smtp' :
                $phpMailer->IsSMTP();
                $phpMailer->Host = $this->getSmtpHost();
                $phpMailer->Port = intval($this->getSmtpPort());

                if (Bee_Utils_Strings::hasText($this->getSmtpUsername())) {
                    $phpMailer->SMTPAuth = true;
                    $phpMailer->Username = $this->getSmtpUsername();
                    $phpMailer->Password = $this->getSmtpPassword();

                    if (Bee_Utils_Strings::hasText($this->getSmtpSecurity())) {
                        $phpMailer->SMTPSecure = $this->getSmtpSecurity();
                    }
                } else {
                }
                break;

            case 'mail' :
                $phpMailer->IsMail();
                break;
        }


        // SET RECIPIENT
        if (is_null($recipient)) {
            $recipient = array();
            $recipient['address'] = $this->getDefaultRecipientAddress();
            $recipient['name'] = $this->getDefaultRecipientName();
        }

        if (is_string($recipient)) {
            $phpMailer->AddAddress($recipient);

        } else if (is_array($recipient)) {
            if (!array_key_exists('address', $recipient)) {
                throw new Exception('SmartyMailer failed: mailformed recipient. Field not found: "address"');
            }

            if (array_key_exists('name', $recipient)) {
                $phpMailer->AddAddress($recipient['address'], $recipient['name']);
            } else {
                $phpMailer->AddAddress($recipient['address'], $recipient['name']);
            }

        } else {
            throw new Exception('SmartyMailer failed: mailformed recipient. Type-mismatch. Recipient must be either string or array, but is: "'.gettype($recipient).'" instead.');
        }


        // SET SENDER
        if (is_null($sender)) {
            $sender = array();
            $sender['address'] = $this->getDefaultSenderAddress();
            $sender['name'] = $this->getDefaultSenderName();
        }

        if (is_string($sender)) {
            $phpMailer->SetFrom($sender);

        } else if (is_array($sender)) {
            if (!array_key_exists('address', $sender)) {
                throw new Exception('SmartyMailer failed: mailformed sender. Field not found: "address"');
            }

            if (array_key_exists('name', $sender)) {
                $phpMailer->SetFrom($sender['address'], $sender['name']);
            } else {
                $phpMailer->SetFrom($sender['address'], $sender['name']);
            }

        } else {
            throw new Exception('SmartyMailer failed: mailformed sender. Type-mismatch. Sender must be either string or array, but is: "'.gettype($sender).'" instead.');
        }


        // SET CONTENT
        $this->smarty->clearAllAssign();
        foreach($model as $key => $value) {
            $this->smarty->assign($key, $value);
        }
        $phpMailer->Subject = $this->smarty->fetch($subjectTemplate);
        $phpMailer->Body = $this->smarty->fetch($bodyTemplate);

        return $phpMailer;
    }



    //=== GETTERS & SETTERS ============================================================================================
    /**
     * Gets the PluginDir
     *
     * @return  $pluginDir
     */
    public function getPluginDir() {
        return $this->pluginDir;
    }

    /**
     * Sets the PluginDir
     *
     * @param $pluginDir
     * @return void
     */
    public function setPluginDir($pluginDir) {
        $this->pluginDir = $pluginDir;
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
     * Gets the SmtpPort
     *
     * @return  $smtpPort
     */
    public function getSmtpPort() {
        return $this->smtpPort;
    }

    /**
     * Sets the SmtpPort
     *
     * @param $smtpPort
     * @return void
     */
    public function setSmtpPort($smtpPort) {
        $this->smtpPort = $smtpPort;
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
     * Gets the SmtpPassword
     *
     * @return  $smtpPassword
     */
    public function getSmtpPassword() {
        return $this->smtpPassword;
    }

    /**
     * Sets the SmtpPassword
     *
     * @param $smtpPassword
     * @return void
     */
    public function setSmtpPassword($smtpPassword) {
        $this->smtpPassword = $smtpPassword;
    }

    /**
     * Gets the SmtpSecurity
     *
     * @return  $smtpSecurity
     */
    public function getSmtpSecurity() {
        return $this->smtpSecurity;
    }

    /**
     * Sets the SmtpSecurity
     *
     * @param $smtpSecurity
     * @return void
     */
    public function setSmtpSecurity($smtpSecurity) {
        $this->smtpSecurity = $smtpSecurity;
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

    /**
     * Gets the DefaultRecipientAddress
     *
     * @return  $defaultRecipientAddress
     */
    public function getDefaultRecipientAddress() {
        return $this->defaultRecipientAddress;
    }

    /**
     * Sets the DefaultRecipientAddress
     *
     * @param $defaultRecipientAddress
     * @return void
     */
    public function setDefaultRecipientAddress($defaultRecipientAddress) {
        $this->defaultRecipientAddress = $defaultRecipientAddress;
    }

    /**
     * Gets the DefaultRecipientName
     *
     * @return  $defaultRecipientName
     */
    public function getDefaultRecipientName() {
        return $this->defaultRecipientName;
    }

    /**
     * Sets the DefaultRecipientName
     *
     * @param $defaultRecipientName
     * @return void
     */
    public function setDefaultRecipientName($defaultRecipientName) {
        $this->defaultRecipientName = $defaultRecipientName;
    }

}