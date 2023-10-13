<?php

namespace QuantaQuirk\Database\Events;

abstract class ConnectionEvent
{
    /**
     * The name of the connection.
     *
     * @var string
     */
    public $connectionName;

    /**
     * The database connection instance.
     *
     * @var \QuantaQuirk\Database\Connection
     */
    public $connection;

    /**
     * Create a new event instance.
     *
     * @param  \QuantaQuirk\Database\Connection  $connection
     * @return void
     */
    public function __construct($connection)
    {
        $this->connection = $connection;
        $this->connectionName = $connection->getName();
    }
}
