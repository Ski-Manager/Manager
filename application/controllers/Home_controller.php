<?php
class Home_controller extends CI_Controller{
    
    public function __construct() {
        parent::__construct(); 
        $ci =& get_instance();
        if ($ci->session->userdata('site_lang')) {
            $siteLang = $ci->session->userdata('site_lang');
        } else {
            $siteLang = 'english';
            $this->session->set_userdata('site_lang', $siteLang);
        }
        $ci->lang->load('home',$siteLang);
        //$ci->lang->load('staff',$siteLang);
        $ci->lang->load('login_form',$siteLang);
        $ci->lang->load('navbar',$siteLang);
        $this->load->model('news_model');
        $this->load->model('leaderboard_model');
    }
    
    public function index(){
        $language = $this->session->userdata('site_lang');
        $nb_slopes = count_nb_slopes();
        $nb_lifts = count_nb_lifts();
        $nb_visitors = count_total_accumulated_visitors();
        $nb_tournaments = count_nb_tournaments();
        $data['news_block'] = $this->retrieve_news($language);
        $data['home_stats'] = [
            ['icon' => 'fa-users',           'value' => number_format($this->session->userdata('registered_players'), 0, ',', ' '), 'label' => $this->lang->line('home')['stat_label_members']      ?? 'registered members'],
            ['icon' => 'fa-snowflake',       'value' => number_format($nb_slopes, 0, ',', ' '),      'label' => $this->lang->line('home')['small_slopes']],
            ['icon' => 'fa-cable-car',       'value' => number_format($nb_lifts, 0, ',', ' '),       'label' => $this->lang->line('home')['small_lifts']],
            ['icon' => 'fa-trophy',          'value' => number_format($nb_tournaments, 0, ',', ' '), 'label' => $this->lang->line('home')['small_tournaments']],
            ['icon' => 'fa-person-skiing',   'value' => number_format($nb_visitors, 0, ',', ' '),    'label' => $this->lang->line('home')['tourists_since_opening']],
            ['icon' => 'fa-language',        'value' => '2',                                          'label' => $this->lang->line('home')['languages']],
        ];
        $data['main_content'] = 'home';
        $data['top_resorts'] = $this->leaderboard_model->get_top_resorts(3);
        $this->load->view('templates/default',$data); 
    }
    
    protected function retrieve_news($language){
        $active_news = $this->news_model->get_active_news_language($language); // retrieve the news in the current player language
        $title_column_name = 'title_'.$language;
        $content_column_name = 'content_'.$language;
        $body_news = '';
        if ($active_news->num_rows() > 0) {
            foreach ($active_news->result() as $active_news_data) {
                $formatted_date = date("F j, Y", strtotime($active_news_data->created_date));
                $body_news .= '<article class="news_block">';
                $body_news .= '<div class="news_header">';
                $body_news .= '<span class="news_title"><i class="fa-solid fa-bullhorn news_title_icon"></i>' . htmlspecialchars($active_news_data->$title_column_name, ENT_QUOTES, 'UTF-8') . '</span>';
                $body_news .= '<span class="news_date"><i class="fa-regular fa-calendar"></i>' . htmlspecialchars($formatted_date, ENT_QUOTES, 'UTF-8') . '</span>';
                $body_news .= '</div>';
                $body_news .= '<div class="news_content">' . $active_news_data->$content_column_name . '</div>';
                $body_news .= '</article>';
            }
        }
        else {
            $body_news = '<p class="news_empty"><i class="fa-solid fa-newspaper"></i> ' . htmlspecialchars($this->lang->line('home')['no_news'], ENT_QUOTES, 'UTF-8') . '</p>';
        }
        return $body_news;
    }
    
}