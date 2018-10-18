<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Install\Task\GenerateDatabaseConfig;

use PeeHaa\AwesomeFeed\Install\Output;

class Phinx
{
    public function run(Output $output, array $configuration, $phinxConfigFile)
    {
        $output->info('Writing phinx configuration file');

        if (file_exists($phinxConfigFile)) {
            $output->success('Phinx configuration already exists at: ' . realpath($phinxConfigFile));
        }

        $searchAndReplace = [
            '{host}'     => $configuration['database']['host'],
            '{name}'     => $configuration['database']['name'],
            '{username}' => $configuration['database']['username'],
            '{password}' => $configuration['database']['password'],
        ];

        $config = str_replace(array_keys($searchAndReplace), $searchAndReplace, $this->getConfigTemplate());

        file_put_contents($phinxConfigFile, $config);

        $output->success('Phinx configuration written to: ' . realpath($phinxConfigFile));
    }

    public function getConfigTemplate(): string
    {
        return <<<'EOD'
paths:
    migrations: '%%PHINX_CONFIG_DIR%%/db/migrations'
    seeds: '%%PHINX_CONFIG_DIR%%/db/seeds'

environments:
    default_migration_table: phinxlog
    default_database: development
    production:
        adapter: postgres
        host: {host}
        name: {name}
        user: {username}
        pass: '{password}'
        port: 3306
        charset: utf8

    development:
        adapter: mysql
        host: localhost
        name: development_db
        user: root
        pass: ''
        port: 3306
        charset: utf8

    testing:
        adapter: mysql
        host: localhost
        name: testing_db
        user: root
        pass: ''
        port: 3306
        charset: utf8

version_order: creation

EOD;
    }
}
