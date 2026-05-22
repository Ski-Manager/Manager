<div class="w-full">
    <?php

// General page title
echo '<h2 class="h2">'.$this->lang->line('home')['reporting_title'].'</h2>';

?>     
    <!-- START account Block -->
    <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
        <div class="md:col-span-12">
            <?php
            echo $this->lang->line('reporting')['intro'].'<br><br>';
            echo $this->lang->line('reporting')['cost_report'].' '.COST_GENEPIS_REPORT.' '.$this->lang->line('navbar')['genepis'].'.<br>';
            echo $this->lang->line('reporting')['example_below'];

            echo anchor(base_url().'files/reports/3d397ea1-457e-44da-9003-fb4357cb0f17.pdf', 'View example').'<br><br>';

            echo $this->lang->line('reporting')['reporting_wiki'].'<br><br>';
            
            echo $this->lang->line('reporting')['order_report'].'<br><br>';
            echo '<button id="order_report" class="btn btn-lg btn-primary">'.$this->lang->line('reporting')['order'].'</button>';
            echo '<p/>';
        
        echo '</div>';
        
        echo '<div class="md:col-span-12">';
            foreach ($list_reports->result() as $list_reports_data ) {
                echo '<b>'.$this->lang->line('reporting')['date'].':</b> '.$list_reports_data->date.' ';
                echo '<b>'.$this->lang->line('reporting')['status'].':</b> '.$list_reports_data->status.' ';
                if ($list_reports_data->status == 'created' ){
                echo '<a href="'.base_url().'files/reports/'.$list_reports_data->uuid_report.'.pdf" class="btn btn-primary">'.$this->lang->line('reporting')['view'].'</a> ';
                //.$this->lang->line('reporting')['or'].' ';
                //echo '<a href="reporting_controller/download/'.$list_reports_data->uuid_report.'.pdf" class="btn btn-primary">'.$this->lang->line('reporting')['download'].'</a>';
                }
                echo '<br><br>';
            }
            ?>

        </div>
        
        <div class="md:col-span-12">
            <div id="result_order" ></div>   
        </div>
        
    
    </div>
          
</div>
    
    
    
    
    <!-- END account block -->
