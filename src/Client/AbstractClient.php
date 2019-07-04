<?php
namespace ReMailBundle\Client;

abstract class AbstractClient implements ClientInterface {

    /** @var string */
    protected $host = null;

    /** @var int */
    protected $port = null;

    /** @var string */
    protected $service = null;

    /** @var string */
    protected $username = '';

    /** @var string */
    protected $password = '';

    /** @var string */
    protected $folder = '';

    /**
     * Constructor
     *
     * Instantiate the mail client object
     *
     * @param string $host
     * @param int $port
     * @param string $service
     */
    public function __construct($host, $port, $service = null) {
        $this->setHost($host);
        $this->setPort($port);
        if (null !== $service) {
            $this->setService($service);
        }
    }

    /**
     * 
     * {@inheritDoc}
     * @see \ReMailBundle\Client\ClientInterface::getHost()
     */
    public function getHost() {
        return $this->host;
    }

    /**
     * 
     * {@inheritDoc}
     * @see \ReMailBundle\Client\ClientInterface::getPort()
     */
    public function getPort() {
        return $this->port;
    }

    /**
     * 
     * {@inheritDoc}
     * @see \ReMailBundle\Client\ClientInterface::getService()
     */
    public function getService() {
        return $this->service;
    }

    /**
     * 
     * {@inheritDoc}
     * @see \ReMailBundle\Client\ClientInterface::getUsername()
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     * 
     * {@inheritDoc}
     * @see \ReMailBundle\Client\ClientInterface::getPassword()
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * 
     * {@inheritDoc}
     * @see \ReMailBundle\Client\ClientInterface::getFolder()
     */
    public function getFolder() {
        return $this->folder;
    }

    /**
     * 
     * {@inheritDoc}
     * @see \ReMailBundle\Client\ClientInterface::setHost()
     */
    public function setHost($host) {
        $this->host = $host;
        return $this;
    }

    /**
     * 
     * {@inheritDoc}
     * @see \ReMailBundle\Client\ClientInterface::setPort()
     */
    public function setPort($port) {
        $this->port = $port;
        return $this;
    }

    /**
     * 
     * {@inheritDoc}
     * @see \ReMailBundle\Client\ClientInterface::setService()
     */
    public function setService($service) {
        $this->service = $service;
        return $this;
    }

    /**
     * 
     * {@inheritDoc}
     * @see \ReMailBundle\Client\ClientInterface::setUsername()
     */
    public function setUsername($username) {
        $this->username = $username;
        return $this;
    }

    /**
     * 
     * {@inheritDoc}
     * @see \ReMailBundle\Client\ClientInterface::setPassword()
     */
    public function setPassword($password) {
        $this->password = $password;
        return $this;
    }

    /**
     * 
     * {@inheritDoc}
     * @see \ReMailBundle\Client\ClientInterface::setFolder()
     */
    public function setFolder($folder) {
        $this->folder = $folder;
        return $this;
    }

}
