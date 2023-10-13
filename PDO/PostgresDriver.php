<?php

namespace QuantaQuirk\Database\PDO;

use Doctrine\DBAL\Driver\AbstractPostgreSQLDriver;
use QuantaQuirk\Database\PDO\Concerns\ConnectsToDatabase;

class PostgresDriver extends AbstractPostgreSQLDriver
{
    use ConnectsToDatabase;

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'pdo_pgsql';
    }
}
