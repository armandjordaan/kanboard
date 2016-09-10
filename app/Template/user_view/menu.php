<div class="dropdown">
    <a href="#" class="dropdown-menu dropdown-menu-link-icon"><i class="fa fa-cog fa-fw"></i><i class="fa fa-caret-down"></i></a>
    <ul>
        <li>
            <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
            <?= $this->url->link(t('Edit'), 'UserViewController', 'edit_timesheet', array('subtask_time_tracking_id' => $record['id']), false, 'popover') ?>
        </li>
    </ul>
</div>