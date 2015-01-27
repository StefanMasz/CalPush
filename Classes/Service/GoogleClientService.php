<?php

/**
 * Class GoogleClientService
 * @author Stefan Masztalerz <stefanmasz@hotmail.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class GoogleClientService
{

    /**
     * @var Google_Client
     */
    private $googleClient;

    /**
     * Initialize Google Client from constants
     * @return Google_Client
     */
    public function getGoogleClient()
    {
        if (null === $this->googleClient) {
            $config = $this->getGoogleConfig();

            $this->googleClient = new Google_Client($config);
            $this->googleClient->setDeveloperKey(DEVELOPERKEY);
            $this->googleClient->setApplicationName(APPLICATIONNAME);
            $this->googleClient->setClientId(CLIENTID);
            $this->googleClient->setClientSecret(CLIENTSECRET);
            $this->googleClient->addScope("https://www.googleapis.com/auth/calendar");

            // This file location should point to the private key file.
            $key = file_get_contents(__DIR__ . PATH_TO_CREDANTIALFILE);
            $cred = new Google_Auth_AssertionCredentials(
            // Replace this with the email address from the client.
                CLIENTMAIL,
                // Replace this with the scopes you are requesting.
                array('https://www.googleapis.com/auth/calendar'),
                $key
            );
            $this->googleClient->setAssertionCredentials($cred);
        }
        return $this->googleClient;
    }

    /**
     * @return Google_Config
     */
    private function getGoogleConfig()
    {
        $config = new Google_Config();
        $config->setClassConfig('Google_Cache_File', array('directory' => __DIR__ . '/../../tmp/'));
        return $config;
    }
}