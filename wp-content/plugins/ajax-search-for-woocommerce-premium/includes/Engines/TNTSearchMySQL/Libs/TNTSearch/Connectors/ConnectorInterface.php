<?php

namespace DgoraWcas\Engines\TNTSearchMySQL\Libs\TNTSearch\Connectors;

interface ConnectorInterface
{
    /**
     * Establish a database connection.
     *
     * @param  array  $config
     * @return \PDO
     */
    public function connect(array $config);
}
