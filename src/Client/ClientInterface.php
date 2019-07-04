<?php

namespace ReMailBundle\Client;

interface ClientInterface {

    /**
     * Get host
     *
     * @return string
     */
    public function getHost();

    /**
     * Get port
     *
     * @return int
     */
    public function getPort();

    /**
     * Get service
     *
     * @return string
     */
    public function getService();

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername();

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword();

    /**
     * Get folder
     *
     * @return string
     */
    public function getFolder();

    /**
     * Set host
     *
     * @param  string $host
     * @return self
     */
    public function setHost($host);

    /**
     * Set port
     *
     * @param  int $port
     * @return self
     */
    public function setPort($port);

    /**
     * Set service
     *
     * @param  string $service
     * @return self
     */
    public function setService($service);

    /**
     * Set username
     *
     * @param  string $username
     * @return self
     */
    public function setUsername($username);

    /**
     * Set password
     *
     * @param  string $password
     * @return self
     */
    public function setPassword($password);

    /**
     * Set folder
     *
     * @param  string $folder
     * @return self
     */
    public function setFolder($folder);

}
