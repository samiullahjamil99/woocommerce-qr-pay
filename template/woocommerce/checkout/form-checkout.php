<div id="qodef-woo-page">
    <div id="qr_pay_error" style="display:none"></div>
    <div id="qr_pay_buttons">
        <a href="javascript:void(0)" class="cash button alt">Payer en espèce</a>
        <a href="javascript:void(0)" class="card button alt">Payer par carte</a>
    </div>
    <div id="qr_pay_code_container" style="display:none">
        <div id="qr_orderdetails"></div>
        <div id="qrcode"></div>
        <div style="margin-top:10px;">
			<a href="javascript:void(0)" class="cancel button alt">Cancel Order</a>
			<a href="javascript:void(0)" class="cash button alt">Pay at Terminal</a>
		</div>
    </div>
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
                       f.$qr_orderdetails.html("Scannez le code QR sur votre téléphone pour entrer les détails de la carte sur le téléphone avec la commande #"+e.orderid);
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