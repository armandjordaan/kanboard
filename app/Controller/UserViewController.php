<?php

namespace Kanboard\Controller;

use Kanboard\Core\Controller\PageNotFoundException;
use Kanboard\Model\ProjectModel;

/**
 * Class UserViewController
 *
 * @package Kanboard\Controller
 * @author  Frederic Guillot
 */
class UserViewController extends BaseController
{
    /**
     * Public user profile
     *
     * @access public
     * @throws PageNotFoundException
     */
    public function profile()
    {
        $user = $this->userModel->getById($this->request->getIntegerParam('user_id'));

        if (empty($user)) {
            throw new PageNotFoundException();
        }

        $this->response->html($this->helper->layout->app('user_view/profile', array(
            'title' => $user['name'] ?: $user['username'],
            'user'  => $user,
        )));
    }

    /**
     * Display user information
     *
     * @access public
     */
    public function show()
    {
        $user = $this->getUser();
        $this->response->html($this->helper->layout->user('user_view/show', array(
            'user'      => $user,
            'timezones' => $this->timezoneModel->getTimezones(true),
            'languages' => $this->languageModel->getLanguages(true),
        )));
    }

    /**
     * Display timesheet
     *
     * @access public
     */
    public function timesheet()
    {
        $user = $this->getUser();

        $subtask_paginator = $this->paginator
            ->setUrl('UserViewController', 'timesheet', array('user_id' => $user['id'], 'pagination' => 'subtasks'))
            ->setMax(20)
            ->setOrder('start')
            ->setDirection('DESC')
            ->setQuery($this->subtaskTimeTrackingModel->getUserQuery($user['id']))
            ->calculateOnlyIf($this->request->getStringParam('pagination') === 'subtasks');

        $this->response->html($this->helper->layout->user('user_view/timesheet', array(
            'subtask_paginator' => $subtask_paginator,
            'user'              => $user,
        )));
    }

    /**
     * Display last password reset
     *
     * @access public
     */
    public function passwordReset()
    {
        $user = $this->getUser();
        $this->response->html($this->helper->layout->user('user_view/password_reset', array(
            'tokens' => $this->passwordResetModel->getAll($user['id']),
            'user'   => $user,
        )));
    }

    /**
     * Edit timesheet entry
     *
     * @access public
     */
    public function edit_Timesheet()
    {
        $record = $this->subtaskTimeTrackingModel->getTimesheetEntry($this->request->getIntegerParam('subtask_time_tracking_id'));

        if (empty($record)) {
            throw new PageNotFoundException();
        }
        
        $record = $this->dateParser->format($record, array('start'), $this->dateParser->getUserDateTimeFormat());
        $record = $this->dateParser->format($record, array('end'),   $this->dateParser->getUserDateTimeFormat());
        
        $this->response->html($this->helper->layout->user('user_view/edit_timesheet', array(
            'record' => $record,
        )));
    }
    
    /**
     * Update timesheet entry
     *
     * @access public
     */    
    public function update()
    {
        $user = $this->getUser();
        
        // update the timesheet entry        
        $id = $this->request->getIntegerParam('subtask_time_tracking_id');
        $values = $this->request->getValues();
        
        $start_dt = $values['start'];
        $end_dt   = $values['end'];
        
        //$start_dt = $this->request->getStringParam('start');
        //$end_dt = $this->request->getStringParam('end');
        
        $this->logger->info('Start: '.$start_dt);
        $this->logger->info('end: '.$end_dt);
        
        if ($this->subtaskTimeTrackingModel->updateSubTask($id, $user['id'], $start_dt, $end_dt))
        {
            $this->flash->success(t('Time entry updated successfully.'));            
        }
        else 
        {
            $this->flash->failure(t('Unable to update time entry.'));
        }
        
        return $this->response->redirect($this->helper->url->to('UserViewController', 'timesheet', 
                array('user_id' => $user['id'])));        
    }
    
    /**
     * Display last connections
     *
     * @access public
     */
    public function lastLogin()
    {
        $user = $this->getUser();
        $this->response->html($this->helper->layout->user('user_view/last', array(
            'last_logins' => $this->lastLoginModel->getAll($user['id']),
            'user'        => $user,
        )));
    }

    /**
     * Display user sessions
     *
     * @access public
     */
    public function sessions()
    {
        $user = $this->getUser();
        $this->response->html($this->helper->layout->user('user_view/sessions', array(
            'sessions' => $this->rememberMeSessionModel->getAll($user['id']),
            'user'     => $user,
        )));
    }

    /**
     * Remove a "RememberMe" token
     *
     * @access public
     */
    public function removeSession()
    {
        $this->checkCSRFParam();
        $user = $this->getUser();
        $this->rememberMeSessionModel->remove($this->request->getIntegerParam('id'));
        $this->response->redirect($this->helper->url->to('UserViewController', 'sessions', array('user_id' => $user['id'])));
    }

    /**
     * Display user notifications
     *
     * @access public
     */
    public function notifications()
    {
        $user = $this->getUser();

        if ($this->request->isPost()) {
            $values = $this->request->getValues();
            $this->userNotificationModel->saveSettings($user['id'], $values);
            $this->flash->success(t('User updated successfully.'));
            return $this->response->redirect($this->helper->url->to('UserViewController', 'notifications', array('user_id' => $user['id'])));
        }

        return $this->response->html($this->helper->layout->user('user_view/notifications', array(
            'projects'      => $this->projectUserRoleModel->getProjectsByUser($user['id'], array(ProjectModel::ACTIVE)),
            'notifications' => $this->userNotificationModel->readSettings($user['id']),
            'types'         => $this->userNotificationTypeModel->getTypes(),
            'filters'       => $this->userNotificationFilterModel->getFilters(),
            'user'          => $user,
        )));
    }

    /**
     * Display user integrations
     *
     * @access public
     */
    public function integrations()
    {
        $user = $this->getUser();

        if ($this->request->isPost()) {
            $values = $this->request->getValues();
            $this->userMetadataModel->save($user['id'], $values);
            $this->flash->success(t('User updated successfully.'));
            $this->response->redirect($this->helper->url->to('UserViewController', 'integrations', array('user_id' => $user['id'])));
        }

        $this->response->html($this->helper->layout->user('user_view/integrations', array(
            'user'   => $user,
            'values' => $this->userMetadataModel->getAll($user['id']),
        )));
    }

    /**
     * Display external accounts
     *
     * @access public
     */
    public function external()
    {
        $user = $this->getUser();
        $this->response->html($this->helper->layout->user('user_view/external', array(
            'last_logins' => $this->lastLoginModel->getAll($user['id']),
            'user'        => $user,
        )));
    }

    /**
     * Public access management
     *
     * @access public
     */
    public function share()
    {
        $user = $this->getUser();
        $switch = $this->request->getStringParam('switch');

        if ($switch === 'enable' || $switch === 'disable') {
            $this->checkCSRFParam();

            if ($this->userModel->{$switch . 'PublicAccess'}($user['id'])) {
                $this->flash->success(t('User updated successfully.'));
            } else {
                $this->flash->failure(t('Unable to update this user.'));
            }

            return $this->response->redirect($this->helper->url->to('UserViewController', 'share', array('user_id' => $user['id'])));
        }

        return $this->response->html($this->helper->layout->user('user_view/share', array(
            'user'  => $user,
            'title' => t('Public access'),
        )));
    }
}
