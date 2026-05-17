<div class="lg:col-span-12">
<!-- List all products -->
<?php if(!empty($products)){ foreach($products as $row){ ?>
    <div class="sm:col-span-4 lg:col-span-4 md:col-span-4">
        <div class="card">
            <img src="<?php echo base_url('img/icons/'.$row['image']); ?>" loading="lazy" alt="<?php echo htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?>" />
            <div class="caption">
                <h4 class="h4 float-right">$<?php echo $row['price']; ?> USD</h4>
                <h4 class="h4"><a href="javascript:void(0);"><?php echo $row['name']; ?></a></h4>
            </div>
            <div class="ratings">
                <a href="<?php echo base_url('products/buy/'.$row['id']); ?>">
                    <img src="<?php echo base_url('img/icons/paypal_buy_now.png'); ?>" alt="Buy now with PayPal" />
                </a>
                <p class="float-right">15 reviews</p>
                <p>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                </p>
            </div>
        </div>
    </div>
<?php } }else{ ?>
    <p>Product(s) not found...</p>
<?php } ?>
</div>