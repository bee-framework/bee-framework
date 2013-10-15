<?php

namespace Bee\Utils;

use Bee_Utils_Strings;
use Exception;
use PHPMailer;


/**
 * Class PhpMailerWrapper
 * @package Bee\Utils
 */
class PhpMailerWrapper {

	/**
	 * Enter description here...
	 *
	 * @var string
	 */
	private $smtpHost;

	/**
	 * Enter description here...
	 *
	 * @var int
	 */
	private $smtpPort = 25;

	/**
	 * Enter description here...
	 *
	 * @var string
	 */
	private $smtpUsername;

	/**
	 * Enter description here...
	 *
	 * @var string
	 */
	private $smtpPassword;

	/**
	 * @var string
	 */
	private $smtpSecurity;

	/**
	 * Enter description here...
	 *
	 * @var string
	 */
	private $defaultSenderAddress;

	/**
	 * Enter description here...
	 *
	 * @var string
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

	/**
	 * @var string
	 */
	private $mailType = 'mail';

	/**
	 * Enter description here...
	 *
	 * @return string
	 */
	public final function getSmtpHost() {
		return $this->smtpHost;
	}

	/**
	 * Enter description here...
	 *
	 * @param string $smtpHost
	 * @return void
	 */
	public final function setSmtpHost($smtpHost) {
		$this->smtpHost = $smtpHost;
	}

	/**
	 * Gets the SmtpPort
	 *
	 * @return int $smtpPort
	 */
	public function getSmtpPort() {
		return $this->smtpPort;
	}

	/**
	 * Sets the SmtpPort
	 *
	 * @param int $smtpPort
	 * @return void
	 */
	public function setSmtpPort($smtpPort) {
		$this->smtpPort = $smtpPort;
	}

	/**
	 * Enter description here...
	 *
	 * @return string
	 */
	public final function getSmtpUsername() {
		return $this->smtpUsername;
	}

	/**
	 * Enter description here...
	 *
	 * @param string $smtpUsername
	 * @return void
	 */
	public final function setSmtpUsername($smtpUsername) {
		$this->smtpUsername = $smtpUsername;
	}

	/**
	 * Gets the SmtpPassword
	 *
	 * @return string $smtpPassword
	 */
	public function getSmtpPassword() {
		return $this->smtpPassword;
	}

	/**
	 * Sets the SmtpPassword
	 *
	 * @param string $smtpPassword
	 * @return void
	 */
	public function setSmtpPassword($smtpPassword) {
		$this->smtpPassword = $smtpPassword;
	}

	/**
	 * Gets the SmtpSecurity
	 *
	 * @return string $smtpSecurity
	 */
	public function getSmtpSecurity() {
		return $this->smtpSecurity;
	}

	/**
	 * Sets the SmtpSecurity
	 *
	 * @param string $smtpSecurity
	 * @return void
	 */
	public function setSmtpSecurity($smtpSecurity) {
		$this->smtpSecurity = $smtpSecurity;
	}

	/**
	 * Enter description here...
	 *
	 * @return string
	 */
	public final function getDefaultSenderAddress() {
		return $this->defaultSenderAddress;
	}

	/**
	 * Enter description here...
	 *
	 * @param string $defaultSenderAddress
	 */
	public final function setDefaultSenderAddress($defaultSenderAddress) {
		$this->defaultSenderAddress = $defaultSenderAddress;
	}

	/**
	 * Enter description here...
	 *
	 * @return string
	 */
	public final function getDefaultSenderName() {
		return $this->defaultSenderName;
	}

	/**
	 * Enter description here...
	 *
	 * @param string $defaultSenderName
	 * @return void
	 */
	public final function setDefaultSenderName($defaultSenderName) {
		$this->defaultSenderName = $defaultSenderName;
	}

	/**
	 * Gets the DefaultRecipientAddress
	 *
	 * @return string $defaultRecipientAddress
	 */
	public function getDefaultRecipientAddress() {
		return $this->defaultRecipientAddress;
	}

	/**
	 * Sets the DefaultRecipientAddress
	 *
	 * @param string $defaultRecipientAddress
	 * @return void
	 */
	public function setDefaultRecipientAddress($defaultRecipientAddress) {
		$this->defaultRecipientAddress = $defaultRecipientAddress;
	}

	/**
	 * Gets the DefaultRecipientName
	 *
	 * @return string $defaultRecipientName
	 */
	public function getDefaultRecipientName() {
		return $this->defaultRecipientName;
	}

	/**
	 * Sets the DefaultRecipientName
	 *
	 * @param string $defaultRecipientName
	 * @return void
	 */
	public function setDefaultRecipientName($defaultRecipientName) {
		$this->defaultRecipientName = $defaultRecipientName;
	}

	/**
	 * Gets the MailType
	 *
	 * @return string $mailType
	 */
	public function getMailType() {
		return $this->mailType;
	}

	/**
	 * Sets the MailType
	 *
	 * @param string $mailType
	 * @return void
	 */
	public function setMailType($mailType) {
		$this->mailType = $mailType;
	}

	protected function createMailer($sender, $recipient, $charSet = 'UTF-8', $html = true) {
		$phpMailer = new PHPMailer(true);
		$phpMailer->CharSet = $charSet;
		$phpMailer->IsHTML($html);

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
		if (!$recipient) {
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
			throw new Exception('SmartyMailer failed: mailformed recipient. Type-mismatch. Recipient must be either string or array, but is: "' . gettype($recipient) . '" instead.');
		}


		// SET SENDER
		if (!$sender) {
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
			throw new Exception('SmartyMailer failed: mailformed sender. Type-mismatch. Sender must be either string or array, but is: "' . gettype($sender) . '" instead.');
		}

		return $phpMailer;
	}
} 