<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Install;

use Composer\Script\Event;
use PeeHaa\AwesomeFeed\Install\Output\StdOut;
use PeeHaa\AwesomeFeed\Install\Task\CompileAssets;
use PeeHaa\AwesomeFeed\Install\Task\GenerateConfig;
use PeeHaa\AwesomeFeed\Install\Task\GenerateDatabaseConfig;
use PeeHaa\AwesomeFeed\Install\Task\GenerateEncryptionKey;
use PeeHaa\AwesomeFeed\Install\Task\MigrateDatabase;

class PostInstall
{
    public static function run(Event $event): void
    {
        $installer = new Installer(new StdOut());

        $installer->addTask(new GenerateEncryptionKey(__DIR__ . '/../../config/encryption.key'));
        $installer->addTask(new CompileAssets(__DIR__ . '/../../'));
        $installer->addTask(new GenerateConfig(__DIR__ . '/../../config/config.php', $event->getIO()));
        $installer->addTask(new GenerateDatabaseConfig(
            __DIR__ . '/../../config/config.php',
            __DIR__ . '/../../phinx.yml',
            $event->getIO()
        ));
        $installer->addTask(new MigrateDatabase(__DIR__ . '/../../'));

        $installer->run();
    }
}
