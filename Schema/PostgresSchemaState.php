<?php

namespace QuantaQuirk\Database\Schema;

use QuantaQuirk\Database\Connection;

class PostgresSchemaState extends SchemaState
{
    /**
     * Dump the database's schema into a file.
     *
     * @param  \QuantaQuirk\Database\Connection  $connection
     * @param  string  $path
     * @return void
     */
    public function dump(Connection $connection, $path)
    {
        $commands = collect([
            $this->baseDumpCommand().' --schema-only > '.$path,
            $this->baseDumpCommand().' -t '.$this->migrationTable.' --data-only >> '.$path,
        ]);

        $commands->map(function ($command, $path) {
            $this->makeProcess($command)->mustRun($this->output, array_merge($this->baseVariables($this->connection->getConfig()), [
                'QUANTAQUIRK_LOAD_PATH' => $path,
            ]));
        });
    }

    /**
     * Load the given schema file into the database.
     *
     * @param  string  $path
     * @return void
     */
    public function load($path)
    {
        $command = 'pg_restore --no-owner --no-acl --clean --if-exists --host="${:QUANTAQUIRK_LOAD_HOST}" --port="${:QUANTAQUIRK_LOAD_PORT}" --username="${:QUANTAQUIRK_LOAD_USER}" --dbname="${:QUANTAQUIRK_LOAD_DATABASE}" "${:QUANTAQUIRK_LOAD_PATH}"';

        if (str_ends_with($path, '.sql')) {
            $command = 'psql --file="${:QUANTAQUIRK_LOAD_PATH}" --host="${:QUANTAQUIRK_LOAD_HOST}" --port="${:QUANTAQUIRK_LOAD_PORT}" --username="${:QUANTAQUIRK_LOAD_USER}" --dbname="${:QUANTAQUIRK_LOAD_DATABASE}"';
        }

        $process = $this->makeProcess($command);

        $process->mustRun(null, array_merge($this->baseVariables($this->connection->getConfig()), [
            'QUANTAQUIRK_LOAD_PATH' => $path,
        ]));
    }

    /**
     * Get the base dump command arguments for PostgreSQL as a string.
     *
     * @return string
     */
    protected function baseDumpCommand()
    {
        return 'pg_dump --no-owner --no-acl --host="${:QUANTAQUIRK_LOAD_HOST}" --port="${:QUANTAQUIRK_LOAD_PORT}" --username="${:QUANTAQUIRK_LOAD_USER}" --dbname="${:QUANTAQUIRK_LOAD_DATABASE}"';
    }

    /**
     * Get the base variables for a dump / load command.
     *
     * @param  array  $config
     * @return array
     */
    protected function baseVariables(array $config)
    {
        $config['host'] ??= '';

        return [
            'QUANTAQUIRK_LOAD_HOST' => is_array($config['host']) ? $config['host'][0] : $config['host'],
            'QUANTAQUIRK_LOAD_PORT' => $config['port'] ?? '',
            'QUANTAQUIRK_LOAD_USER' => $config['username'],
            'PGPASSWORD' => $config['password'],
            'QUANTAQUIRK_LOAD_DATABASE' => $config['database'],
        ];
    }
}
