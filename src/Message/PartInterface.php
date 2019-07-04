<?php
namespace ReMailBundle\Message;

interface PartInterface
{

    /**
     * Get message part content
     *
     * @return string
     */
    public function getContent();

    /**
     * Set message part content
     *
     * @param  string $content
     * @return PartInterface
     */
    public function setContent($content);

}
