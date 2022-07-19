<div id="qodef-woo-page">
    <div id="qr_pay_error" style="display:none"></div>
    <div id="qr_pay_buttons">
        <a href="javascript:void(0)" class="cash card-button button alt"><div><?php echo __("Pay by Cash","woocommerce-qr-pay"); ?></div><span class="extra-info"><?php _e("Pay Cash on Counter","woocommerce-qr-pay"); ?></span><div><i class="fas fa-wallet"></i></div></a>
        <a href="javascript:void(0)" class="card card-button button alt"><div><?php echo __("Pay by Card","woocommerce-qr-pay"); ?></div><span class="extra-info"><?php _e("Requires Smartphone and Card Details","woocommerce-qr-pay"); ?></span><div><i class="fas fa-credit-card"></i></div></a>
    </div>
    <div id="qr_pay_code_container" style="display:none">
        <div id="qr_orderdetails"></div>
        <div>
            <h2 class="qr-main-heading"><?php _e("Follow These Steps","woocommerce-qr-pay"); ?></h2>
            <div class="qr-orderstep">
                <h3><?php _e("Step 1: QR Scan","woocommerce-qr-pay"); ?></h3>
                <p class="desc"><?php printf(__("Take out your phone and open %s. Point your Camera towards the following QR code.","woocommerce-qr-pay"), '<b>Camera App</b> <i class="fas fa-camera" style="margin:0 5px;"></i>'); ?></p>
                <div id="qrcode"></div>
            </div>
            <div class="qr-orderstep">
                <h3><?php _e("Step 2: Open Link","woocommerce-qr-pay"); ?></h3>
                <p class="desc"><?php _e("After that the app will ask to open link in browser, so open the link in your mobile browser.","woocommerce-qr-pay"); ?></p>
                <i class="fas fa-mobile-alt main-icon"></i>
            </div>
            <div class="qr-orderstep">
                <h3><?php _e("Step 3: Enter Card Details","woocommerce-qr-pay"); ?></h3>
                <p class="desc"><?php _e("Now a page with order details will open having a form with payment details on your phone. Fill it and proceed.","woocommerce-qr-pay"); ?></p>
                <i class="fas fa-credit-card main-icon"></i>
            </div>
            <div class="qr-orderstep">
                <h3><?php _e("Still Confused with Process?","woocommerce-qr-pay"); ?></h3>
                <p class="desc"><?php _e("We have the following options for you.","woocommerce-qr-pay"); ?></p>
                <a href="javascript:void(0)" class="cancel card-button button alt"><div><?php echo __("Cancel Order","woocommerce-qr-pay"); ?></div><div><i class="fas fa-times-circle"></i></div></a>
                <a href="javascript:void(0)" class="cash card-button button alt"><div><?php echo __("Pay at Terminal","woocommerce-qr-pay"); ?></div><div><i class="fas fa-wallet"></i></div></a>
            </div>
        </div>
    </div>
    <?php do_action('wqp_after_checkout'); ?>
</div>
<style>
    #qr_pay_buttons {
        text-align:center;
    }
    #qr_pay_code_container {
        text-align:center;
    }
    #qrcode {
        text-align:center;
        margin-top:10px;
    }
    #qrcode img {
        margin:auto;
    }
    .card-button {
        display: inline-flex;
        flex-direction: column;
        position: relative;
        font-size:30pt !important;
        padding:30px 35px !important;
        margin:0px 10px !important;
    }
    #qodef-woo-page .card-button {
        background-color:white !important;
    }
    .card-button i {
        margin-top:10px;
        font-size: 50pt;
    }
    .extra-info {
        font-size:12pt;
    }
    .qr-orderstep {
        margin-bottom:70px;
    }
    .qr-orderstep .desc {
        font-size:18pt;
        margin:30px 0;
    }
    .qr-orderstep .main-icon {
        font-size:240px;
    }
</style>
<script>
jQuery(function(g) {
    var QR_CODE = new QRCode("qrcode", {
        width: 220,
        height: 220,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H,
    });
    var f = {
        orderid: 0,
        $qr_pay_buttons : g("#qr_pay_buttons"),
        $qr_pay_code_container : g("#qr_pay_code_container"),
        $qr_pay_error: g("#qr_pay_error"),
        $qr_orderdetails: g("#qr_orderdetails"),
        init: function() {
            this.$qr_pay_buttons.on("click","a.cash",this.pay_cash_process), this.$qr_pay_buttons.on("click","a.card",this.pay_card_process),
            this.$qr_pay_code_container.on("click","a.cancel",this.cancel_order),
            this.$qr_pay_code_container.on("click","a.cash",this.repay_cash_process)
        },
        pay_cash_process: function(e) {
            var h;
            f.$qr_pay_buttons.addClass( 'processing' ).block( {
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            } );
            f.xhr && f.xhr.abort(), (h = {
                action: "pay_by_cash",
            }), g.ajax({
                type: "POST",
                url: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
                data: h,
                success: function(e) {
                    console.log(e.message);
                    if (e.redirect)
                        window.location.replace(e.redirect);
                }
            })
        },
        repay_cash_process: function(e) {
		    f.$qr_pay_code_container.addClass( 'processing' ).block( {
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            } );
		   var h;
		   f.xhr && f.xhr.abort(), (h = {
               action: "repay_by_cash",
			   orderid: f.orderid,
           }), g.ajax({
               type: "POST",
               url: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
               data: h,
               success: function(e) {
				   if (e.redirect)
                   window.location.replace(e.redirect);
			   }
		   })
	   },
       pay_card_process: function(e) {
           var h;
           f.$qr_pay_buttons.addClass( 'processing' ).block( {
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            } );
           f.xhr && f.xhr.abort(), (h = {
               action: "pay_by_card",
           }), g.ajax({
               type: "POST",
               url: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
               data: h,
               success: function(e) {
                   console.log(e.message);
                   if (e.paylink) {
                       f.$qr_pay_code_container.show();
                       f.$qr_pay_buttons.hide();
                       g('.bottom-cart-container').hide();
                       g('#bottom_cart_spacer').hide();
                       QR_CODE.makeCode(e.paylink);
                       setInterval(function () {f.check_order_status(e.orderid)}, 5000);
                       f.orderid = e.orderid;
                   }
               }
           })
       },
       cancel_order: function(e) {
		   f.$qr_pay_code_container.addClass( 'processing' ).block( {
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            } );
		   var h;
		   f.xhr && f.xhr.abort(), (h = {
			   action: "qr_cancel_order",
			   orderid: f.orderid,
		   }), g.ajax({
			   type: "POST",
			   url: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
               data: h,
               success: function(e) {
				   if (e.success) {
					   window.location.replace("<?php echo get_permalink( wc_get_page_id( 'shop' ) ); ?>");
				   }
			   }
		   })
	   },
       check_order_status: function(orderid) {
           var h;
           f.xhr && f.xhr.abort(), (h = {
               action: "qr_check_order_status",
               orderid: orderid,
           }), g.ajax({
               type: "POST",
               url: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
               data: h,
               success: function(e) {
                   if (e.status === "processing") 
                   window.location.replace(e.redirect);
               }
           })
       }
   };
   f.init()
});
</script>