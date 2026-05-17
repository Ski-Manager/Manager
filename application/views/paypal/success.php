<div class="lg:col-span-12">
    <h4 class="h4 success"><?php echo $this->lang->line('genepis')['payment_success']; ?></h4>
    <p><?php echo $this->lang->line('genepis')['item_name']; ?> : <span><?php echo $item_name; ?></span></p>
    <p><?php echo $this->lang->line('genepis')['transaction_id']; ?> : <span><?php echo $txn_id; ?></span></p>
    <p><?php echo $this->lang->line('genepis')['amount_payed']; ?> : <span>$<?php echo $payment_amt.' '.$currency_code; ?></span></p>
    <p><?php echo $this->lang->line('genepis')['payment_status']; ?> : <span><?php echo $status; ?></span></p>
    <p><?php echo $this->lang->line('genepis')['genepis_purchased']; ?> : <span><?php echo $amount_genepis; ?></span></p>
    <p><?php echo $this->lang->line('genepis')['cash_purchased']; ?> : <span><?php echo number_format($amount_cash, 0, ',', ' '); ?>€</span></p>
    
    <a href="<?php echo base_url('genepis_controller'); ?>"><?php echo $this->lang->line('genepis')['back_skimanager']; ?></a>
</div>
