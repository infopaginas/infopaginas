<?php

namespace Domain\ReportBundle\Model;

class UserActionModel implements ReportInterface
{
    const TYPE_ACTION_VIEW_SHOW_PAGE    = 'view_show_page';
    const TYPE_ACTION_VIEW_LIST_PAGE    = 'view_list_page';
    const TYPE_ACTION_VIEW_CREATE_PAGE  = 'view_create_page';
    const TYPE_ACTION_VIEW_UPDATE_PAGE  = 'view_update_page';
    const TYPE_ACTION_VIEW_DELETE_PAGE  = 'view_delete_page';

    const TYPE_ACTION_CREATE  = 'create';
    const TYPE_ACTION_UPDATE  = 'update';
    const TYPE_ACTION_PHYSICAL_DELETE  = 'physical_delete';
    const TYPE_ACTION_POSTPONE_DELETE  = 'postpone_delete';
    const TYPE_ACTION_RESTORE = 'restore';
    const TYPE_ACTION_EXPORT  = 'export';

    const TYPE_ACTION_TASK_APPROVE  = 'task_approve';
    const TYPE_ACTION_TASK_REJECT   = 'task_reject';

    const TYPE_ACTION_DRAFT_APPROVE  = 'draft_approve';
    const TYPE_ACTION_DRAFT_REJECT   = 'draft_reject';

    const TYPE_ACTION_LOGIN  = 'login';
    const TYPE_ACTION_LOGOUT = 'logout';

    const ENTITY_TYPE_AUTH = 'Authentication';

    const EVENT_TYPES = [
        self::TYPE_ACTION_VIEW_SHOW_PAGE    => 'user_action_report.action.view_show_page',
        self::TYPE_ACTION_VIEW_LIST_PAGE    => 'user_action_report.action.view_list_page',
        self::TYPE_ACTION_VIEW_CREATE_PAGE  => 'user_action_report.action.view_create_page',
        self::TYPE_ACTION_VIEW_UPDATE_PAGE  => 'user_action_report.action.view_update_page',

        self::TYPE_ACTION_CREATE    => 'user_action_report.action.create',
        self::TYPE_ACTION_UPDATE    => 'user_action_report.action.update',
        self::TYPE_ACTION_PHYSICAL_DELETE => 'user_action_report.action.physical_delete',
        self::TYPE_ACTION_POSTPONE_DELETE => 'user_action_report.action.postpone_delete',
        self::TYPE_ACTION_RESTORE   => 'user_action_report.action.restore',

        self::TYPE_ACTION_EXPORT    => 'user_action_report.action.export',

        self::TYPE_ACTION_TASK_APPROVE => 'user_action_report.action.task_approve',
        self::TYPE_ACTION_TASK_REJECT  => 'user_action_report.action.task_reject',

        self::TYPE_ACTION_DRAFT_APPROVE => 'user_action_report.action.draft_approve',
        self::TYPE_ACTION_DRAFT_REJECT  => 'user_action_report.action.draft_reject',

        self::TYPE_ACTION_LOGIN  => 'user_action_report.action.login',
        self::TYPE_ACTION_LOGOUT => 'user_action_report.action.logout',
    ];

    /**
     * @return array
     */
    public static function getExportFormats()
    {
        return [
            self::CODE_PDF_BUSINESS_OVERVIEW_REPORT   => self::FORMAT_PDF,
            self::CODE_EXCEL_BUSINESS_OVERVIEW_REPORT => self::FORMAT_EXCEL,
        ];
    }

    /**
     * @return array
     */
    public static function getTypes()
    {
        return [
            self::TYPE_ACTION_VIEW_SHOW_PAGE,
            self::TYPE_ACTION_VIEW_LIST_PAGE,
            self::TYPE_ACTION_VIEW_CREATE_PAGE,
            self::TYPE_ACTION_VIEW_UPDATE_PAGE,

            self::TYPE_ACTION_CREATE,
            self::TYPE_ACTION_UPDATE,
            self::TYPE_ACTION_PHYSICAL_DELETE,
            self::TYPE_ACTION_POSTPONE_DELETE,
            self::TYPE_ACTION_RESTORE,
            self::TYPE_ACTION_EXPORT,

            self::TYPE_ACTION_TASK_APPROVE,
            self::TYPE_ACTION_TASK_REJECT,

            self::TYPE_ACTION_DRAFT_APPROVE,
            self::TYPE_ACTION_DRAFT_REJECT,

            self::TYPE_ACTION_LOGIN,
            self::TYPE_ACTION_LOGOUT
        ];
    }
}
