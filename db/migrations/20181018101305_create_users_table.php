<?php declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class CreateUsersTable extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('users', ['id' => false, 'primary_key' => 'id']);

        $table
            ->addColumn('id', 'biginteger')
            ->addColumn('username', 'string', ['limit' => 255])
            ->addColumn('avatar', 'string', ['limit' => 255])
            ->addIndex(['username'], [
                'unique' => true,
                'name'   => 'idx_users_username',
            ])
            ->create()
        ;
    }
}
