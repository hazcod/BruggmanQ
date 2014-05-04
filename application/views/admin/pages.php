<br><br><br>
<div class="bs-docs-section">
        <div class="row">
	<?php $this->renderPartial('flashmessage'); ?>
        </div>
        <div class="row-fluid">
          <div class="span4">
             <h2><?= $this->lang['pages']; ?></h2>
             <a href="<?= URL::base_uri(); ?>admin/lists/<?= $this->list; ?>"><?= $this->lang['gobacktolist']; ?>&nbsp;<?= $this->list; ?>&nbsp;.</a><br>
             <div class="pull-right">
                <button type="button" class="btn btn-default">
                    <a href="<?= URL::base_uri(); ?>admin/pages/add"><span class="glyphicon glyphicon-plus"></span> <?= $this->lang['addpage']; ?></a>
                </button>
             </div>
             <table class="table table-hover">
              <?php if ($this->pages == false): ?>
              <p><?= $this->lang['nopagesyet']; ?></p>
              <?php else: ?>
              <thead>
                <tr>
                  <?php foreach ($this->pages[0] as $property => $data): ?>
                  <th><?= ucfirst($property); ?></th>
                  <?php endforeach; ?>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($this->pages as $key => $list): ?>
                <tr>
                  <?php foreach ($list as $property => $data): ?>
                  <td><?= $data ?></td>
                  <?php endforeach; ?>
                  <td>
                      <a href="<?= URL::base_uri(); ?>admin/pages/<?= $this->list; ?>/delete/<?= $list->id; ?>"><i class="glyphicon glyphicon-remove"></i><?= $this->lang['delete']; ?></a>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
              <?php endif; ?>
            </table>
          </div>
        </div>
      </div>