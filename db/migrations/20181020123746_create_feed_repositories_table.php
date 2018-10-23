<?php declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class CreateFeedRepositoriesTable extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('repositories', ['id' => false, 'primary_key' => 'id']);

        $table
            ->addColumn('id', 'biginteger')
            ->addColumn('owner', 'string', ['limit' => 255])
            ->addColumn('name', 'string', ['limit' => 255])
            ->addColumn('url', 'string', ['limit' => 255])
            ->addColumn('avatar_url', 'string', ['limit' => 255])
            ->addColumn('owner_url', 'string', ['limit' => 255])
            ->addIndex(['owner', 'name'], [
                'unique' => true,
                'name'   => 'idx_repositories_owner_name',
            ])
            ->addIndex('owner')
            ->addIndex('name')
            ->create()
        ;

        $table = $this->table('repository_releases', ['id' => false, 'primary_key' => 'id']);

        $table
            ->addColumn('id', 'biginteger')
            ->addColumn('repository_id', 'biginteger')
            ->addColumn('name', 'string', ['limit' => 255])
            ->addColumn('body', 'text')
            ->addColumn('url', 'string', ['limit' => 255])
            ->addColumn('published_at', 'datetime')
            ->addForeignKeyWithName('fk_repository_releases_repositories', 'repository_id', 'repositories', 'id', [
                'delete' => 'CASCADE',
            ])
            ->addIndex('published_at')
            ->create()
        ;

        $table = $this->table('feeds_repositories');

        $table
            ->addColumn('repository_id', 'biginteger')
            ->addColumn('feed_id', 'integer')
            ->addForeignKeyWithName('fk_feeds_repositories_repositories', 'repository_id', 'repositories', 'id', [
                'delete' => 'CASCADE',
            ])
            ->addForeignKeyWithName('fk_feeds_repositories_feeds', 'feed_id', 'feeds', 'id', [
                'delete' => 'CASCADE',
            ])
            ->addIndex(['repository_id', 'feed_id'], [
                'unique' => true,
                'name'   => 'idx_repository_id_feed_id',
            ])
            ->create()
        ;
    }
}
