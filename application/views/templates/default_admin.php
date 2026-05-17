<?php

$this->load->view('includes/header');

echo '<title>Admin | Ski-Manager</title>';
echo '<link rel="icon" href="'.base_url().'favicon.ico" type="image/x-icon">';
echo '</head>';
echo '<body>';
echo '<a href="#main-content" class="sr-only-focusable">' . ($this->lang->line('home')['skip_to_main'] ?? 'Skip to main content') . '</a>';

echo '<header class="bgimage" role="banner">
    <h1 class="bgimage-title"><span aria-hidden="true">&#9975;</span> Ski-Manager</h1>
    <p class="bgimage-subtitle" aria-label="Admin Panel">Admin Panel</p>
</header>';

echo '<div class="sm-navbar"><nav aria-label="' . ($this->lang->line('home')['main_nav'] ?? 'Main navigation') . '"><div class="wrapper">';
    $this->load->view('includes/navbar');
echo '</div></nav></div>';


echo '<div class="clearfix"></div>';

echo '<div class="wrapper">';
    $this->load->view('includes/sidebar');
    echo '<main id="main-content" tabindex="-1">';
    echo '<div id="page-content-wrapper" class="prose max-w-none">';
        $this->load->view($main_content);
    echo '</div>';
    echo '</main>';

    echo '<div class="clearfix"></div>';
echo '</div>';
$this->load->view('includes/footer_admin');

?>