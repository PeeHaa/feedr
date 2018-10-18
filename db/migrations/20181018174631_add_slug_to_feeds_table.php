<?php declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class AddSlugToFeedsTable extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('feeds');

        $table
            ->addColumn('slug', 'string', ['limit' => 250])
            ->addIndex('slug')
            ->update()
        ;
    }
}
