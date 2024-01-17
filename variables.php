<?php
declare(strict_types=1);

$root    = '/sites/FF3/release/firefly-iii';
$allRoot = '/sites/FF3';

return [
    'paths'                   => [
        'firefly_iii' => $root,
        'data'        => sprintf('%1$s/data-importer', $allRoot),
        'help'        => sprintf('%s/documentation/help', $allRoot),
    ],
    'cleanup'                 => [
        'extensions' => ['php', 'less', 'twig', 'gitkeep', 'gitignore', 'yml', 'xml', 'js'],
        'paths'      => ['.deploy', '.github', 'app', 'bootstrap', 'config', 'database', 'resources', 'routes', 'tests',],
    ],
    'languages'               => [
        'bg_BG',
        'ca_ES',
        'cs_CZ',
        'da_DK',
        'de_DE',
        'el_GR',
        'en_GB',
        'en_US',
        'es_ES',
        'fi_FI',
        'fr_FR',
        'hu_HU',
        'id_ID',
        'it_IT',
        'ja_JP',
        'ko_KR',
        'nb_NO',
        'nl_NL',
        'nn_NO',
        'pl_PL',
        'pt_BR',
        'pt_PT',
        'ro_RO',
        'ru_RU',
        'sk_SK',
        'sl_SI',
        'sv_SE',
        'tr_TR',
        'uk_UA',
        'vi_VN',
        'zh_TW',
        'zh_CN',
    ],
    'ignore_translation_keys' => [
        // only used dynamically
        'config.dow_',
        'firefly.telemetry_type_',
        'firefly.rule_trigger_',
        'firefly.rule_action_',
        'form.convert_',
        'firefly.no_',
        'firefly.opt_group_',
        'firefly.search_modifier_',
        'firefly.repeat_freq_',
        'firefly.convert_is_already_type_',
        'firefly.cannot_disable_',

        // old import
        'import.spectre_extra_',

        // configs
        'config.month_js',
        'config.date_time_js',
        'config.specific_day_js',
        'config.week_in_year_js',
        'config.year_js',
        'config.half_year_js',

        // impossible to verify
        'validation.',

        // used in JS only
        'firefly.errors_submission',
        'firefly.add_another_split',
        'firefly.you_create_transfer',
        'firefly.you_create_withdrawal',
        'firefly.you_create_deposit',
    ],
    'json'                    => [
        'v2' => [
            'config'  => [
                'html_language',
                'date_time_fns'
            ],
            'firefly' => [
                'spent',
                'left',
                'paid',
                'errors_submission',
                'unpaid',
                'default_group_title_name_plain',
                'subscriptions_in_group',
                'subscr_expected_x_times',
                'overspent',
                'money_flowing_in',
                'money_flowing_out',
                'category',
                'unknown_category_plain',
                'all_money',
                'unknown_source_plain',
                'unknown_dest_plain',
                'unknown_any_plain',
                'unknown_budget_plain',
                'stored_journal_js',
                'wait_loading_transaction',
                'nothing_found',
                'wait_loading_data',
                'Transfer',
                'Withdrawal',
                'Deposit',
                'expense_account',
                'revenue_account',
                'budget',
                'account_type_Asset account',
                'account_type_Expense account',
                'account_type_Revenue account',
                'account_type_Debt',
                'account_type_Loan',
                'account_type_Mortgage',
                'errors_submission'
            ],
        ],
        'v1' => [
            'firefly' => [
                'welcome_back',
                'flash_error',
                'flash_warning',
                'flash_success',
                'close',
                'split_transaction_title',
                'errors_submission',
                'split',
                'single_split',
                'transaction_stored_link',
                'webhook_stored_link',
                'webhook_updated_link',
                'transaction_updated_link',
                'transaction_new_stored_link',
                'transaction_journal_information',
                'submission_options',
                'apply_rules_checkbox',
                'fire_webhooks_checkbox',
                'no_budget_pointer',
                'no_bill_pointer',
                'source_account',
                'hidden_fields_preferences',
                'destination_account',
                'add_another_split',
                'submission',
                'stored_journal',
                'create_another',
                'reset_after',
                'submit',
                'errors_submission',
                'amount',
                'date',
                'is_reconciled_fields_dropped',
                'tags',
                'no_budget',
                'no_bill',
                'category',
                'attachments',
                'notes',
                'external_url',
                'update_transaction',
                'after_update_create_another',
                'store_as_new',
                'reset_after',
                'errors_submission',
                'split_title_help',
                'none_in_select_list',
                'no_piggy_bank',
                'description',
                'split_transaction_title_help',
                'destination_account_reconciliation',
                'source_account_reconciliation',
                'budget',
                'bill',
                'you_create_withdrawal',
                'you_create_transfer',
                'you_create_deposit',
                'edit',
                'delete',
                'name',
                'profile_whoops',
                'profile_something_wrong',
                'profile_try_again',
                'profile_oauth_clients',
                'profile_oauth_no_clients',
                'profile_oauth_clients_header',
                'profile_oauth_client_id',
                'profile_oauth_client_name',
                'profile_oauth_client_secret',
                'profile_oauth_create_new_client',
                'profile_oauth_create_client',
                'profile_oauth_edit_client',
                'profile_oauth_name_help',
                'profile_oauth_redirect_url',
                'profile_oauth_clients_external_auth',
                'profile_oauth_redirect_url_help',
                'profile_authorized_apps',
                'profile_authorized_clients',
                'profile_scopes',
                'profile_revoke',
                'profile_personal_access_tokens',
                'profile_personal_access_token',
                'profile_personal_access_token_explanation',
                'profile_no_personal_access_token',
                'profile_create_new_token',
                'profile_create_token',
                'profile_create',
                'profile_save_changes',
                'default_group_title_name',
                'piggy_bank',
                'profile_oauth_client_secret_title',
                'profile_oauth_client_secret_expl',
                'profile_oauth_confidential',
                'profile_oauth_confidential_help',
                'multi_account_warning_unknown',
                'multi_account_warning_withdrawal',
                'multi_account_warning_deposit',
                'multi_account_warning_transfer',
                'webhook_trigger_STORE_TRANSACTION',
                'webhook_trigger_UPDATE_TRANSACTION',
                'webhook_trigger_DESTROY_TRANSACTION',
                'webhook_response_TRANSACTIONS',
                'webhook_response_ACCOUNTS',
                'webhook_response_none_NONE',
                'webhook_delivery_JSON',
                'actions',
                'meta_data',
                'webhook_messages',
                'inactive',
                'no_webhook_messages',
                'inspect',
                'edit',
                'delete',
                'create_new_webhook',
                'webhooks',
                'webhook_trigger_form_help',
                'webhook_response_form_help',
                'webhook_delivery_form_help',
                'webhook_active_form_help',
                'edit_webhook_js',
                'webhook_was_triggered',
                'view_message',
                'view_attempts',
                'message_content_title',
                'message_content_help',
                'attempt_content_title',
                'attempt_content_help',
                'no_attempts',
                'webhook_attempt_at',
                'logs',
                'response',
                'visit_webhook_url',
                'reset_webhook_secret',
            ],
            'form'    => [
                'url',
                'active',
                'interest_date',
                'title',
                'book_date',
                'process_date',
                'due_date',
                'foreign_amount',
                'payment_date',
                'invoice_date',
                'internal_reference',
                'webhook_response',
                'webhook_trigger',
                'webhook_delivery',
            ],
            'list'    => [
                'active',
                'trigger',
                'response',
                'delivery',
                'url',
                'secret',
            ],
            'config'  => [
                'html_language',
                'date_time_fns',
            ],
        ],
    ],
];
