<?php

$this->load->view('includes/header');

echo '<title>Ski-Manager</title>';
echo '<link rel="icon" href="'.base_url().'favicon.ico" type="image/x-icon">';
echo '</head>';
echo '<body>';
echo '<a href="#main-content" class="sr-only-focusable">' . ($this->lang->line('home')['skip_to_main'] ?? 'Skip to main content') . '</a>';

echo '<header class="bgimage" role="banner">
    <div class="container">
    </div>
</header>';

echo '<nav aria-label="' . ($this->lang->line('home')['main_nav'] ?? 'Main navigation') . '"><div class="wrapper">';
    $this->load->view('includes/navbar');
echo '</div></nav>';


echo '<div class="clearfix"></div>';

echo '<div class="wrapper">';
    echo '<main id="main-content" tabindex="-1">';
    echo '<div id="page-content-wrapper">';
        $this->load->view($main_content);
    echo '</div>';
    echo '</main>';

    echo '<div class="clearfix"></div>';
echo '</div>';
$this->load->view('includes/footer');

?>