<?php

namespace QuantaQuirk\Database;

use Exception;
use QuantaQuirk\Database\PDO\PostgresDriver;
use QuantaQuirk\Database\Query\Grammars\PostgresGrammar as QueryGrammar;
use QuantaQuirk\Database\Query\Processors\PostgresProcessor;
use QuantaQuirk\Database\Schema\Grammars\PostgresGrammar as SchemaGrammar;
use QuantaQuirk\Database\Schema\PostgresBuilder;
use QuantaQuirk\Database\Schema\PostgresSchemaState;
use QuantaQuirk\Filesystem\Filesystem;

class PostgresConnection extends Connection
{
    /**
     * Escape a binary value for safe SQL embedding.
     *
     * @param  string  $value
     * @return string
     */
    protected function escapeBinary($value)
    {
        $hex = bin2hex($value);

        return "'\x{$hex}'::bytea";
    }

    /**
     * Escape a bool value for safe SQL embedding.
     *
     * @param  bool  $value
     * @return string
     */
    protected function escapeBool($value)
    {
        return $value ? 'true' : 'false';
    }

    /**
     * Determine if the given database exception was caused by a unique constraint violation.
     *
     * @param  \Exception  $exception
     * @return bool
     */
    protected function isUniqueConstraintError(Exception $exception)
    {
        return '23505' === $exception->getCode();
    }

    /**
     * Get the default query grammar instance.
     *
     * @return \QuantaQuirk\Database\Query\Grammars\PostgresGrammar
     */
    protected function getDefaultQueryGrammar()
    {
        ($grammar = new QueryGrammar)->setConnection($this);

        return $this->withTablePrefix($grammar);
    }

    /**
     * Get a schema builder instance for the connection.
     *
     * @return \QuantaQuirk\Database\Schema\PostgresBuilder
     */
    public function getSchemaBuilder()
    {
        if (is_null($this->schemaGrammar)) {
            $this->useDefaultSchemaGrammar();
        }

        return new PostgresBuilder($this);
    }

    /**
     * Get the default schema grammar instance.
     *
     * @return \QuantaQuirk\Database\Schema\Grammars\PostgresGrammar
     */
    protected function getDefaultSchemaGrammar()
    {
        ($grammar = new SchemaGrammar)->setConnection($this);

        return $this->withTablePrefix($grammar);
    }

    /**
     * Get the schema state for the connection.
     *
     * @param  \QuantaQuirk\Filesystem\Filesystem|null  $files
     * @param  callable|null  $processFactory
     * @return \QuantaQuirk\Database\Schema\PostgresSchemaState
     */
    public function getSchemaState(Filesystem $files = null, callable $processFactory = null)
    {
        return new PostgresSchemaState($this, $files, $processFactory);
    }

    /**
     * Get the default post processor instance.
     *
     * @return \QuantaQuirk\Database\Query\Processors\PostgresProcessor
     */
    protected function getDefaultPostProcessor()
    {
        return new PostgresProcessor;
    }

    /**
     * Get the Doctrine DBAL driver.
     *
     * @return \QuantaQuirk\Database\PDO\PostgresDriver
     */
    protected function getDoctrineDriver()
    {
        return new PostgresDriver;
    }
}
