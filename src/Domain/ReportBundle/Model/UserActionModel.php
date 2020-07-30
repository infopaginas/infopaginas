<?php

namespace Domain\ReportBundle\Model;

class UserActionModel implements ReportInterface
{
    public const TYPE_ACTION_VIEW_SHOW_PAGE   = 'view_show_page';
    public const TYPE_ACTION_VIEW_LIST_PAGE   = 'view_list_page';
    public const TYPE_ACTION_VIEW_CREATE_PAGE = 'view_create_page';
    public const TYPE_ACTION_VIEW_UPDATE_PAGE = 'view_update_page';
    public const TYPE_ACTION_VIEW_DELETE_PAGE = 'view_delete_page';

    public const TYPE_ACTION_CREATE          = 'create';
    public const TYPE_ACTION_UPDATE          = 'update';
    public const TYPE_ACTION_PHYSICAL_DELETE = 'physical_delete';
    public const TYPE_ACTION_POSTPONE_DELETE = 'postpone_delete';
    public const TYPE_ACTION_RESTORE         = 'restore';
    public const TYPE_ACTION_EXPORT          = 'export';

    public const TYPE_ACTION_TASK_APPROVE = 'task_approve';
    public const TYPE_ACTION_TASK_REJECT  = 'task_reject';

    public const TYPE_ACTION_DRAFT_APPROVE = 'draft_approve';
    public const TYPE_ACTION_DRAFT_REJECT  = 'draft_reject';

    public const TYPE_ACTION_LOGIN  = 'login';
    public const TYPE_ACTION_LOGOUT = 'logout';

    public const ENTITY_TYPE_AUTH = 'Authentication';

    public const EVENT_TYPES = [
        self::TYPE_ACTION_VIEW_SHOW_PAGE   => 'user_action_report.action.view_show_page',
        self::TYPE_ACTION_VIEW_LIST_PAGE   => 'user_action_report.action.view_list_page',
        self::TYPE_ACTION_VIEW_CREATE_PAGE => 'user_action_report.action.view_create_page',
        self::TYPE_ACTION_VIEW_UPDATE_PAGE => 'user_action_report.action.view_update_page',

        self::TYPE_ACTION_CREATE          => 'user_action_report.action.create',
        self::TYPE_ACTION_UPDATE          => 'user_action_report.action.update',
        self::TYPE_ACTION_PHYSICAL_DELETE => 'user_action_report.action.physical_delete',
        self::TYPE_ACTION_POSTPONE_DELETE => 'user_action_report.action.postpone_delete',
        self::TYPE_ACTION_RESTORE         => 'user_action_report.action.restore',

        self::TYPE_ACTION_EXPORT => 'user_action_report.action.export',

        self::TYPE_ACTION_TASK_APPROVE => 'user_action_report.action.task_approve',
        self::TYPE_ACTION_TASK_REJECT  => 'user_action_report.action.task_reject',

        self::TYPE_ACTION_DRAFT_APPROVE => 'user_action_report.action.draft_approve',
        self::TYPE_ACTION_DRAFT_REJECT  => 'user_action_report.action.draft_reject',

        self::TYPE_ACTION_LOGIN  => 'user_action_report.action.login',
        self::TYPE_ACTION_LOGOUT => 'user_action_report.action.logout',
    ];

    public static function getExportFormats(): array
    {
        return [
            self::CODE_PDF_BUSINESS_OVERVIEW_REPORT   => self::FORMAT_PDF,
            self::CODE_EXCEL_BUSINESS_OVERVIEW_REPORT => self::FORMAT_EXCEL,
        ];
    }

    public static function getTypes(): array
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
