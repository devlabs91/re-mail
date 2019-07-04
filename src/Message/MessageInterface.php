<?php

namespace ReMailBundle\Message;

interface MessageInterface {

    /**
     * Add header
     *
     * @param  string $header
     * @param  string $value
     * @return AbstractPart
     */
    public function addHeader($header, $value);

    /**
     * Add headers
     *
     * @param  array $headers
     * @return AbstractPart
     */
    public function addHeaders(array $headers);

    /**
     * Determine if message has header
     *
     * @param  string $header
     * @return boolean
     */
    public function hasHeader($header);

    /**
     * Get header
     *
     * @param  string $header
     * @return string
     */
    public function getHeader($header);

    /**
     * Get all headers
     *
     * @return array
     */
    public function getHeaders();

    /**
     * Get header as string
     *
     * @param  string $header
     * @return string
     */
    public function getHeaderAsString($header);

    /**
     * Get all headers as string
     *
     * @param  array $omit
     * @return string
     */
    public function getHeadersAsString(array $omit = []);

    /**
     * Get content type
     *
     * @return string
     */
    public function getContentType();

    /**
     * Get character set
     *
     * @return string
     */
    public function getCharSet();

    /**
     * Get body
     *
     * @return string
     */
    public function getBody();

    /**
     * Set content type
     *
     * @param  string $contentType
     * @return MessageInterface
     */
    public function setContentType($contentType);

    /**
     * Set character set
     *
     * @param  string $charSet
     * @return MessageInterface
     */
    public function setCharSet($charSet);

    /**
     * Render
     *
     * @return string
     */
    public function render();

    /**
     * Render as an array of lines
     *
     * @return array
     */
    public function renderAsLines();

    /**
     * Render to string
     *
     * @return string
     */
    public function __toString();

}