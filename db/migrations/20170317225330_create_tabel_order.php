<?php

use Phinx\Migration\AbstractMigration;

class CreateTabelOrder extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $order = $this->table('orders');
        $order->addColumn('user_id', 'integer')
              ->addColumn('no_invoice', 'integer')
              ->addColumn('total_paid', 'integer')
              ->addColumn('be_paid', 'integer')
              ->addColumn('update_at', 'timestamp')
              ->addColumn('create_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
              ->addColumn('deleted', 'integer', ['default' => 0])
              ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
              ->create();
    }
}
