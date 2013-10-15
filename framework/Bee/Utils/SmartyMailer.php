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
use Bee\Utils\PhpMailerWrapper;

/**
 * @throws Exception
 *
 */
class Bee_Utils_SmartyMailer extends PhpMailerWrapper {

    private $pluginDir;

	/**
	 * 
	 * @var Smarty
	 */
	private $smarty;

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
	 * @throws Exception
	 * @return \PHPMailer
	 */
    public function instantiatePhpMailer($subjectTemplate, $bodyTemplate, array $model, $recipient=null, $sender=null) {
		$phpMailer = $this->createMailer($sender, $recipient);

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
}