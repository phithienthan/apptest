<?= $this->HTML->css('/Manage/css/common/datatable_all_page', ['block' => 'css_header']) ?>
<?= $this->HTML->css('/Manage/css/transaction/index', ['block' => 'css_header']) ?>
<!-- Data table CSS -->
<?= $this->element('Manage/data_table_css') ?>
<div class="row wrapper border-bottom white-bg page-heading" id="head-title">
    <div class="col-lg-10">
        <h2>Manage  Transaction</h2>
        <ol class="breadcrumb">
            <li>
                <a href="#">Manage</a>
            </li>
            <li>
                <a>Wallet</a>
            </li>
            <li>
            <a>Transaction</a>
            </li>
            <li class="active">
                <strong>Index</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2">
    </div>
</div>
<!-- message success -->
<?= $this->Flash->render(); ?>
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5 id="title-balance"><b>Balance: <?= $this->Number->format($dataWallet->amount) ?></b>
         <?= $this->Form->create(null)?> 
                <select id="change-date">
                    <option value="<?= $this->Url->build(['_name'=>'transaction_query','wallet_id'=>$walletId,'query_date'=>'today'])?>">To day</option>
                    <option value="<?= $this->Url->build(['_name'=>'transaction_query','wallet_id'=>$walletId,'query_date'=>'this-week']) ?>">This Week</option>
                    <option value="<?= $this->Url->build(['name'=>'transaction_query','wallet_id'=>$walletId,'query_date'=>'this-month']) ?>">This Month</option>
                </select>
         <?= $this->Form->end() ?>
                 </h5>
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="fa fa-chevron-up"></i>
                    </a>
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-wrench"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li><a href="#">Config option 1</a>
                        </li>
                        <li><a href="#">Config option 2</a>
                        </li>
                    </ul>
                    <a class="close-link">
                        <i class="fa fa-times"></i>
                    </a>
                </div>
            </div> <!-- end ibox-title -->
            <div id="ibox-content" class="ibox-content">
                <?= $this->HTML->link('Add', ['_name' => 'transaction_add', 'wallet_id' => $walletId], ['id' => 'add-new-record', 'class' => 'btn btn-primary col-sm-2 col-md-2 col-lg-1 col-xs-2']) ?>                
                <table id="main" class="table table-striped table-bordered table-hover dataTables-content" cellpadding="0" cellspacing="0">
                    <thead>
                        <tr>
                            <th><?= $this->Paginator->sort('id') ?></th>
                            <th><?= $this->Paginator->sort('wallet') ?></th>
                            <th><?= $this->Paginator->sort('category_id') ?></th>
                            <th><?= $this->Paginator->sort('type') ?></th>
                            <th><?= $this->Paginator->sort('amount') ?></th>
                            <th><?= $this->Paginator->sort('date') ?></th>
                            <th><?= $this->Paginator->sort('note') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i=1; foreach ($transaction as $transaction): ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= $transaction->wallet->name ?></td>
                                <td>
                                    <?= $this->HTML->image($transaction->category->avatar,array('class'=>'circle icon-category')); ?> &nbsp; <?= h($transaction->category->name); ?>
                                </td>
                                <td>
                                    <?= $transaction->category->mst_catalog->name ?>
                                </td>
                                <td><?= $this->Number->format($transaction->amount) ?></td>
                                  <td><?= date_format($transaction->created_at,'Y-m-d') ?></td>
                                <td><?= $transaction->note ?></td>
                                <td class="actions">
                                    <?= $this->Html->link(__('Edit'), ['action' => 'edit',$transaction->id],array('class'=>'btn btn-warning')) ?>
                                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $transaction->id], ['class'=>'btn btn-danger','confirm' => __('Are you sure you want to delete # {0}?', $transaction->id)]) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <!-- PAGINATION-->
                <div class="row">
                    <nav class="pull-right" id="nav-pagination">
                        <ul class="pagination">
                            
                            <?= $this->Paginator->options(array(
                            'url' => array(
                              '_name' => 'transaction_query',
                                'wallet_id'=>$walletId,
                                'query_date'=>$queryDate
                            )
                          )); ?>
                            
                            <?= $this->Paginator->prev('« Previous') ?>
                            <?= $this->Paginator->numbers() ?>
                            <?= $this->Paginator->next('Next »') ?>
                        </ul>
                        <p><?=
                            $this->Paginator->counter([
                                'format' => 'Page {{page}} of {{pages}}, showing {{current}} records out of {{count}} total'
                            ])
                            ?></p>
                    </nav>
                </div>
            </div> <!-- end ibox content -->
        </div>
    </div>
</div>
<div  id="loading">
</div>
<!-- Configuration --->
<?= $this->element('Manage/configuration') ?>
<!-- Data table JS -->
<?= $this->element('Manage/data_table_js') ?>
<?= $this->HTML->script('../Manage/js/common/datatable_all_page', array('block' => 'scriptBottom')) ?> 
<?= $this->HTML->script('../Manage/js/transaction/transaction_index', array('block' => 'scriptBottom')) ?>
<?= $this->HTML->script('../Manage/js/transaction/spin.min', array('block' => 'scriptBottom')) ?> 