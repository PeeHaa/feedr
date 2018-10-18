<?php declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class CreateFeedsTable extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('feeds');

        $table
            ->addColumn('name', 'string', ['limit' => 255])
            ->addColumn('created_by', 'biginteger')
            ->addForeignKeyWithName('fk_feeds_users', 'created_by', 'users', 'id', [
                'delete' => 'RESTRICT',
            ])
            ->create()
        ;

        $table = $this->table('feed_admins');

        $table
            ->addColumn('feed_id', 'integer')
            ->addColumn('user_id', 'biginteger')
            ->addForeignKeyWithName('fk_feeds_admins_feeds', 'feed_id', 'feeds', 'id', [
                'delete'=> 'CASCADE'
            ])
            ->addForeignKeyWithName('fk_feeds_admins_users', 'user_id', 'users', 'id', [
                'delete' => 'RESTRICT',
            ])
            ->create()
        ;
    }
}
