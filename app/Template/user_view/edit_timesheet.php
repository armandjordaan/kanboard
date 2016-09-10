sub<div class="page-header">
    <h2><?= t('Edit a timesheet entry') ?></h2>
</div>


<form class="popover-form" method="post" action="<?= $this->url->href('UserViewController', 'update', array('subtask_time_tracking_id' => $record['id'])) ?>" autocomplete="off">
    
    <?= $this->form->csrf() ?>
    <?= $this->form->label('Task: '.$record['task_title'],'task_title_label') ?>
    <?= $this->form->label('Subtask: '.$record['subtask_title'],'subtask_title_label') ?>
    <hr>
    <?= $this->subtaskTimeTrackingHelper->selectStartDate(array('start' => $record['start'])) ?>
    <?= $this->subtaskTimeTrackingHelper->selectEndDate(array('end' => $record['end'])) ?>

    <div class="form-actions">
        <button type="submit" class="btn btn-blue"><?= t('Save') ?></button>
        <?= t('or') ?>
        <?= $this->url->link(t('cancel'), 'UserViewController', 'timesheet', array(), false, 'close-popover') ?>
    </div>
</form>
