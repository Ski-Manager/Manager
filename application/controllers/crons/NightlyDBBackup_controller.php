<?php
class NightlyDBBackup_controller extends CI_Controller {

    function __construct(){
        // Authentication for scripts
        parent::__construct();
        if (isset($_SERVER['HTTP_AUTHORIZATION']))
            list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':' , base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));

        if (!isset($_SERVER['PHP_AUTH_USER'])) {
            header('WWW-Authenticate: Basic realm="Cron Area"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'You need to login to access this area';
            exit;
        } else if ($_SERVER['PHP_AUTH_USER'] == CRON_USERNAME && $_SERVER['PHP_AUTH_PW'] == CRON_PASSWORD){
            echo "<p>Authentication OK.<br></p>";
        }
        else {
            header('WWW-Authenticate: Basic realm="Cron Area"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'You need to login to access this area';
            exit;
        }
        $this->Log_filename = gmdate('Y-m-d H-i-s', time())."";
    }
    
    public function index($mode = 'Nightly'){
        
        if (isset($mode) && $mode != 'Nightly') {
            $mode_text = ' MANUAL MODE';
        }
        else {
            $mode_text = ' NIGHTLY MODE';
        }
        $this->logToFile($this->Log_filename, "INFO", "[START]", "index", $mode, "NightlyDBBackup_controller ".$mode_text."\n");
        
        
        // Load the DB utility class
        $this->load->dbutil();
        ini_set('memory_limit', '-1');

        $prefs = array(
            'tables'        => array(),   // Array of tables to backup.
            'ignore'        => array('audit', 'index_5b8fdf95f08be', 'index_pref_en', 'messu_archive', 'messu_messages', 'messu_sent', 'sessions', 'ci_sessions', 'tiki_acct_account', 'tiki_acct_bankaccount', 'tiki_acct_book', 'tiki_acct_item', 'tiki_acct_journal', 'tiki_acct_stack', 'tiki_acct_stackitem', 'tiki_acct_statement', 'tiki_acct_tax', 'tiki_actionlog', 'tiki_actionlog_conf', 'tiki_actionlog_params', 'tiki_activity_stream', 'tiki_activity_stream_mapping', 'tiki_activity_stream_rules', 'tiki_addon_profiles', 'tiki_areas', 'tiki_article_types', 'tiki_articles', 'tiki_auth_tokens', 'tiki_banners', 'tiki_banning', 'tiki_banning_sections', 'tiki_blog_activity', 'tiki_blog_posts', 'tiki_blog_posts_images', 'tiki_blogs', 'tiki_calendar_categories', 'tiki_calendar_items', 'tiki_calendar_locations', 'tiki_calendar_options', 'tiki_calendar_recurrence', 'tiki_calendar_roles', 'tiki_calendars', 'tiki_cart_inventory_hold', 'tiki_categories', 'tiki_categorized_objects', 'tiki_category_objects', 'tiki_category_sites', 'tiki_chat_channels', 'tiki_chat_messages', 'tiki_chat_users', 'tiki_comments', 'tiki_connect', 'tiki_content', 'tiki_content_templates', 'tiki_content_templates_sections', 'tiki_contributions', 'tiki_contributions_assigned', 'tiki_cookies', 'tiki_copyrights', 'tiki_credits', 'tiki_credits_types', 'tiki_credits_usage', 'tiki_custom_route', 'tiki_db_status', 'tiki_directory_categories', 'tiki_directory_search', 'tiki_directory_sites', 'tiki_discount', 'tiki_download', 'tiki_dsn', 'tiki_dynamic_variables', 'tiki_extwiki', 'tiki_faq_questions', 'tiki_faqs', 'tiki_feature', 'tiki_featured_links', 'tiki_file_backlinks', 'tiki_file_drafts', 'tiki_file_galleries', 'tiki_file_handlers', 'tiki_files', 'tiki_forum_attachments', 'tiki_forum_reads', 'tiki_forums', 'tiki_forums_queue', 'tiki_forums_reported', 'tiki_freetagged_objects', 'tiki_freetags', 'tiki_galleries', 'tiki_galleries_scales', 'tiki_goal_events', 'tiki_goals', 'tiki_group_inclusion', 'tiki_group_watches', 'tiki_groupalert', 'tiki_h5p_contents', 'tiki_h5p_contents_libraries', 'tiki_h5p_libraries', 'tiki_h5p_libraries_cachedassets', 'tiki_h5p_libraries_hub_cache', 'tiki_h5p_libraries_languages', 'tiki_h5p_libraries_libraries', 'tiki_h5p_results', 'tiki_h5p_tmpfiles', 'tiki_history', 'tiki_hotwords', 'tiki_html_pages', 'tiki_html_pages_dynamic_zones', 'Tables_in_u853012228_skiman (%tiki%)', 'tiki_images', 'tiki_images_data', 'tiki_integrator_reps', 'tiki_integrator_rules', 'tiki_invite', 'tiki_invited', 'tiki_language', 'tiki_link_cache', 'tiki_links', 'tiki_live_support_events', 'tiki_live_support_message_comments', 'tiki_live_support_messages', 'tiki_live_support_modules', 'tiki_live_support_operators', 'tiki_live_support_requests', 'tiki_logs', 'tiki_mail_events', 'tiki_mail_queue', 'tiki_mailin_accounts', 'tiki_menu_languages', 'tiki_menu_options', 'tiki_menus', 'tiki_minical_events', 'tiki_minical_topics', 'tiki_minichat', 'tiki_modules', 'tiki_newsletter_groups', 'tiki_newsletter_included', 'tiki_newsletter_pages', 'tiki_newsletter_subscriptions', 'tiki_newsletters', 'tiki_object_attributes', 'tiki_object_ratings', 'tiki_object_relations', 'tiki_object_scores', 'tiki_objects', 'tiki_output', 'tiki_page_footnotes', 'tiki_page_references', 'tiki_pages', 'tiki_pages_changes', 'tiki_pages_translation_bits', 'tiki_pageviews', 'tiki_payment_received', 'tiki_payment_requests', 'tiki_perspective_preferences', 'tiki_perspectives', 'tiki_plugin_security', 'tiki_poll_objects', 'tiki_poll_options', 'tiki_polls', 'tiki_preferences', 'tiki_private_messages', 'tiki_profile_symbols', 'tiki_programmed_content', 'tiki_queue', 'tiki_quiz_question_options', 'tiki_quiz_questions', 'tiki_quiz_results', 'tiki_quiz_stats', 'tiki_quiz_stats_sum', 'tiki_quizzes', 'tiki_rating_configs', 'tiki_rating_obtained', 'tiki_received_articles', 'tiki_received_pages', 'tiki_referer_stats', 'tiki_registration_fields', 'tiki_related_categories', 'tiki_rss_feeds', 'tiki_rss_items', 'tiki_rss_modules', 'tiki_scheduler', 'tiki_scheduler_run', 'tiki_schema', 'tiki_score', 'tiki_search_queries', 'tiki_search_stats', 'tiki_secdb', 'tiki_sefurl_regex_out', 'tiki_semantic_tokens', 'tiki_semaphores', 'tiki_sent_newsletters', 'tiki_sent_newsletters_errors', 'tiki_sent_newsletters_files', 'tiki_sessions', 'tiki_sheet_layout', 'tiki_sheet_values', 'tiki_sheets', 'tiki_shoutbox', 'tiki_shoutbox_words', 'tiki_source_auth', 'tiki_stats', 'tiki_structure_versions', 'tiki_structures', 'tiki_submissions', 'tiki_suggested_faq_questions', 'tiki_survey_question_options', 'tiki_survey_questions', 'tiki_surveys', 'Tables_in_u853012228_skiman (%tiki%)', 'tiki_tabular_formats', 'tiki_tags', 'tiki_theme_control_categs', 'tiki_theme_control_objects', 'tiki_theme_control_sections', 'tiki_todo', 'tiki_todo_notif', 'tiki_topics', 'tiki_tracker_fields', 'tiki_tracker_item_attachments', 'tiki_tracker_item_field_logs', 'tiki_tracker_item_fields', 'tiki_tracker_items', 'tiki_tracker_options', 'tiki_trackers', 'tiki_transitions', 'tiki_translated_objects', 'tiki_translations_in_progress', 'tiki_untranslated', 'tiki_url_shortener', 'tiki_user_answers', 'tiki_user_answers_uploads', 'tiki_user_assigned_modules', 'tiki_user_bookmarks_folders', 'tiki_user_bookmarks_urls', 'tiki_user_login_cookies', 'tiki_user_mail_accounts', 'tiki_user_mailin_struct', 'tiki_user_menus', 'tiki_user_modules', 'tiki_user_monitors', 'tiki_user_notes', 'tiki_user_postings', 'tiki_user_preferences', 'tiki_user_quizzes', 'tiki_user_reports', 'tiki_user_reports_cache', 'tiki_user_taken_quizzes', 'tiki_user_tasks', 'tiki_user_tasks_history', 'tiki_user_votings', 'tiki_user_watches', 'tiki_userfiles', 'tiki_userpoints', 'tiki_webmail_contacts', 'tiki_webmail_contacts_ext', 'tiki_webmail_contacts_fields', 'tiki_webmail_contacts_groups', 'tiki_webmail_messages', 'tiki_webservice', 'tiki_webservice_template', 'tiki_wiki_attachments', 'tiki_workspace_templates', 'tiki_zones' ),                     // List of tables to omit from the backup
            'format'        => 'zip',                       // gzip, zip, txt
            'filename'      => $mode.'_DB_Backup_'.$this->Log_filename.'.gz',              // File name - NEEDED ONLY WITH ZIP FILES
            'add_drop'      => TRUE,                        // Whether to add DROP TABLE statements to backup file
            'add_insert'    => TRUE,                        // Whether to add INSERT data to backup file
            'newline'       => "\n"                         // Newline character used in backup file
         );
        
        // Backup your entire database and assign it to a variable
        $backup = $this->dbutil->backup($prefs);

        // Load the file helper and write the file to your server
        //$this->load->helper('file');
        write_file(FCPATH . '/application/controllers/backup/'.$mode.'_DB_Backup_'.$this->Log_filename.'.gz', $backup);

        // Load the download helper and send the file to your desktop
       // $this->load->helper('download');
       // force_download('mybackup.gz', $backup);
             
        
        $this->logToFile($this->Log_filename, "INFO", "[END]", "index", $mode, "NightlyDBBackup_controller ".$mode_text."\n");
        
    } // End of Index function
        
    function logToFile($log_filename, $level, $thread, $function, $mode, $data){ 
        
        $timestamp = gmdate('Y-m-d H:i:s,000', time())." ";
        $data_formatted = $timestamp." ".$level." ".$thread." ".$function." - ".$data;
        if ( ! write_file(FCPATH . '/application/controllers/logs/'.$mode.'DBBackup_'.$log_filename.'.log', $data_formatted, "a+")){
            echo "Unable to log ".$function." to file Cron2min_".$log_filename."'.log :<br>".$data_formatted;
        }
        else{
            echo "Logged ".$function."<br>";
        }
    }
    
}
?>