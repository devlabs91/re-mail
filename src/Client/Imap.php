<?php
namespace ReMailBundle\Client;

use ReMailBundle\Message;

class Imap extends AbstractClient {

    /**
     * Mailbox connection string
     * @var string
     */
    protected $connectionString = null;

    /**
     * Mailbox connection resource
     * @var resource
     */
    protected $connection = null;

    /**
     * 
     * @param string $host
     * @param int $port
     * @param string $service
     */
    public function __construct($host, $port, $service = 'imap') {
        parent::__construct($host, $port, $service);
    }

    /**
     * 
     * @param string $flags
     * @param int $options
     * @param int $retries
     * @param array $params
     * @return \ReMailBundle\Client\Imap
     */
    public function open($flags = null, $options = null, $retries = 1, array $params = [ 'DISABLE_AUTHENTICATOR' => 'GSSAPI' ]) {
        $this->connectionString = '{'.$this->host.':'.$this->port.'/'.$this->service;
        if(null !== $flags){ $this->connectionString .= $flags; }
        $this->connectionString .= '}';
        $this->connection = @imap_open($this->connectionString . $this->folder, $this->username, $this->password, $options, $retries, $params);
        return $this;
    }

    /**
     * Determine if the mailbox connection has been opened
     *
     * @return boolean
     */
    public function isOpen() {
        return is_resource($this->connection);
    }

    /**
     * Get mailbox connection
     *
     * @return resource
     */
    public function connection() {
        return $this->connection;
    }

    /**
     * Get mailbox connection string
     *
     * @return string
     */
    public function getConnectionString()
    {
        return $this->connectionString;
    }

    /**
     * List mailboxes
     *
     * @param string $pattern
     * @return array
     */
    public function listMailboxes($pattern = '*')
    {
        return imap_list($this->connection, $this->connectionString . $this->folder, $pattern);
    }

    /**
     * Get mailbox status
     *
     * @return \stdClass
     */
    public function getStatus()
    {
        return imap_status($this->connection, $this->connectionString . $this->folder, SA_ALL);

    }

    /**
     * Get mailbox info
     *
     * @return \stdClass
     */
    public function getInfo()
    {
        return imap_mailboxmsginfo($this->connection);

    }
    /**
     * Get total number of messages
     *
     * @return int
     */
    public function getNumberOfMessages()
    {
        return imap_num_msg($this->connection);
    }

    /**
     * Get total number of read messages
     *
     * @return int
     */
    public function getNumberOfReadMessages()
    {
        return abs($this->getNumberOfMessages() - $this->getStatus()->unseen);
    }

    /**
     * Get total number of unread messages
     *
     * @return int
     */
    public function getNumberOfUnreadMessages()
    {
        return $this->getStatus()->unseen;
    }

    /**
     * Get message overviews
     *
     * @param  mixed  $ids
     * @param  int    $options
     * @return array
     */
    public function getOverview($ids, $options = FT_UID)
    {
        if (is_array($ids)) {
            $ids = implode(',', $ids);
        }
        return imap_fetch_overview($this->connection, $ids, $options);
    }

    /**
     * Get message IDs from a mailbox
     *
     * @param string $criteria
     * @param int    $options
     * @param string $charset
     * @return array
     */
    public function getMessageIds($criteria = 'ALL', $options = SE_UID, $charset = null) {
        return (null !== $charset) ?
            imap_search($this->connection, $criteria, $options, $charset) :
            imap_search($this->connection, $criteria, $options);
    }

    /**
     * Get message IDs from a mailbox by a sort criteria
     *
     * @param  int     $criteria
     * @param  boolean $reverse
     * @param  int     $options
     * @param  string  $search
     * @param  string  $charset
     * @return array
     */
    public function getMessageIdsBy($criteria = SORTDATE, $reverse = true, $options = SE_UID, $search = 'ALL', $charset = null) {
        return (null !== $charset) ?
            imap_sort($this->connection, $criteria, (int)$reverse, $options, $search, $charset) :
            imap_sort($this->connection, $criteria, (int)$reverse, $options, $search);
    }

    /**
     * Get message headers from a mailbox
     *
     * @param  string $criteria
     * @param  int    $options
     * @param  string $charset
     * @return array
     */
    public function getMessageHeaders($criteria = 'ALL', $options = SE_UID, $charset = null)
    {
        $headers = [];
        $ids     = $this->getMessageIds($criteria, $options, $charset);

        foreach ($ids as $id) {
            $headers[$id] =  $this->getMessageHeadersById($id);
        }

        return $headers;
    }

    /**
     * Get message headers from a mailbox
     *
     * @param  int     $criteria
     * @param  boolean $reverse
     * @param  int     $options
     * @param  string  $search
     * @param  string  $charset
     * @return array
     */
    public function getMessageHeadersBy($criteria = SORTDATE, $reverse = true, $options = SE_UID, $search = 'ALL', $charset = null)
    {
        $headers = [];
        $ids     = $this->getMessageIdsBy($criteria, $reverse, $options, $search, $charset);

        foreach ($ids as $id) {
            $headers[$id] =  $this->getMessageHeadersById($id);
        }

        return $headers;
    }

    /**
     * Get message headers by message ID
     *
     * @param  int $id
     * @return \stdClass
     */
    public function getMessageHeadersById($id) {
        $headers        = imap_fetchheader($this->connection, $id, FT_UID);
        $parsedHeaders  = imap_rfc822_parse_headers($headers);
        $contentHeaders = [];

        preg_match_all('/^Content\-(.*)$/m', $headers, $contentHeaders);

        if (isset($contentHeaders[0]) && isset($contentHeaders[0][0])) {
            foreach ($contentHeaders[0] as $contentHeaderString) {
                $contentHeader      = trim(substr($contentHeaderString, 0, strpos($contentHeaderString, ':')));
                $contentHeaderValue = trim(substr($contentHeaderString, (strpos($contentHeaderString, ':') + 1)));

                $parsedHeaders->{$contentHeader} = $contentHeaderValue;
            }
        }

        return $parsedHeaders;
    }

    /**
     * Get message structure by message ID
     *
     * @param  int $id
     * @return \stdClass
     */
    public function getMessageStructure($id)
    {
        return imap_fetchstructure($this->connection, $id, FT_UID);
    }

    /**
     * Get message boundary by message ID
     *
     * @param  int $id
     * @return string
     */
    public function getMessageBoundary($id)
    {
        $boundary  = null;
        $structure = $this->getMessageStructure($id);

        if (isset($structure->parameters) && (count($structure->parameters) > 0)) {
            foreach ($structure->parameters as $parameter) {
                if (strtolower($parameter->attribute) == 'boundary') {
                    $boundary = $parameter->value;
                    break;
                }
            }
        }

        return $boundary;
    }

    /**
     * Get message body by message ID
     *
     * @param int $id
     * @return string
     */
    public function getMessageBody($id) {
        return imap_body($this->connection, $id, FT_UID);
    }

    /**
     * Get message parts by message ID
     *
     * @param  int $id
     * @param  string $encoding
     * @return array
     */
    public function getMessageParts($id, $encoding = null) {
        $boundary = $this->getMessageBoundary($id);
        $body     = $this->getMessageBody($id);
        $parts    = (strpos($body, $boundary) !== false) ?
            explode($boundary, $body) : [$body];

        return Message::parseMessageParts($parts, $encoding);
    }

    /**
     * Copy messages to another mailbox
     *
     * @param  mixed        $ids
     * @param  string|array $to
     * @param  int          $options
     * @return Imap
     */
    public function copyMessage($ids, $to, $options = CP_UID)
    {
        if (is_array($ids)) {
            $ids = implode(',', $ids);
        }

        imap_mail_copy($this->connection, $ids, $to, $options);
        return $this;
    }

    /**
     * Move messages to another mailbox
     *
     * @param  mixed        $ids
     * @param  string|array $to
     * @param  int          $options
     * @return Imap
     */
    public function moveMessage($ids, $to, $options = CP_UID)
    {
        if (is_array($ids)) {
            $ids = implode(',', $ids);
        }

        imap_mail_move($this->connection, $ids, $to, $options);
        return $this;
    }

    /**
     * Mark a message or messages as read
     *
     * @param  mixed $ids
     * @param  int   $options
     * @return Imap
     */
    public function markAsRead($ids, $options = ST_UID)
    {
        return $this->setMessageFlags($ids, "\\Seen", $options);
    }

    /**
     * Mark a message or messages as unread
     *
     * @param  mixed $ids
     * @param  int   $options
     * @return Imap
     */
    public function markAsUnread($ids, $options = ST_UID)
    {
        return $this->clearMessageFlags($ids, "\\Seen", $options);
    }

    /**
     * Mark a message or messages as read
     *
     * @param  mixed  $ids
     * @param  string $flags
     * @param  int    $options
     * @return Imap
     */
    public function setMessageFlags($ids, $flags, $options = ST_UID)
    {
        if (is_array($ids)) {
            $ids = implode(',', $ids);
        }
        imap_setflag_full($this->connection, $ids, $flags, $options);

        return $this;
    }

    /**
     * Mark a message or messages as unread
     *
     * @param  mixed  $ids
     * @param  string $flags
     * @param  int    $options
     * @return Imap
     */
    public function clearMessageFlags($ids, $flags, $options = ST_UID)
    {
        if (is_array($ids)) {
            $ids = implode(',', $ids);
        }
        imap_clearflag_full($this->connection, $ids, $flags, $options);

        return $this;
    }

    /**
     * Delete message
     *
     * @param  int $id
     * @param  int $options
     * @return Imap
     */
    public function deleteMessage($id, $options = FT_UID)
    {
        imap_delete($this->connection, $id, $options);
        imap_expunge($this->connection);
        return $this;
    }

    /**
     * Create mailbox
     *
     * @param  string $new
     * @return Imap
     */
    public function createMailbox($new)
    {
        if (strpos($new, $this->connectionString) === false) {
            $new = $this->connectionString . $new;
        }
        imap_createmailbox($this->connection, $new);
        return $this;
    }

    /**
     * Rename mailbox
     *
     * @param  string $new
     * @param  string $old
     * @return Imap
     */
    public function renameMailbox($new, $old = null)
    {
        if (null === $old) {
            $old = $this->connectionString . $this->folder;
        } else if (strpos($old, $this->connectionString) === false) {
            $old = $this->connectionString . $old;
        }

        if (strpos($new, $this->connectionString) === false) {
            $new = $this->connectionString . $new;
        }

        imap_renamemailbox($this->connection, $old, $new);
        return $this;
    }

    /**
     * Delete mailbox
     *
     * @param  string $mailbox
     * @throws Exception
     * @return Imap
     */
    public function deleteMailbox($mailbox = null) {
        if (null === $mailbox) {
            $mailbox = $this->folder;
        }

        if (empty($mailbox)) {
            throw new Exception('Error: The mailbox is not set.');
        }

        imap_deletemailbox($this->connection, $this->connectionString . $mailbox);
        return $this;
    }

    /**
     * Decode text
     *
     * @param  string $text
     * @return string
     */
    public  function decodeText($text) {
        return Message::decodeText($text);
    }

    /**
     * Close the mailbox connection resource
     *
     * @return void
     */
    public function close() {
        if (is_resource($this->connection)) {
            imap_close($this->connection, CL_EXPUNGE);
        }
    }

}
