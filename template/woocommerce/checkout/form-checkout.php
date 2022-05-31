
<div>
    <div id="qr_pay_error" style="display:none"></div>
    <div id="qr_pay_buttons">
        <a href="javascript:void(0)" class="cash button alt">Pay by Cash</a>
        <a href="javascript:void(0)" class="card button alt">Pay by Card</a>
    </div>
    <div id="qr_pay_code_container" style="display:none">
        <div id="qr_orderdetails"></div>
        <div id="qrcode"></div>
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
       $qr_pay_buttons : g("#qr_pay_buttons"),
       $qr_pay_code_container : g("#qr_pay_code_container"),
       $qr_pay_error: g("#qr_pay_error"),
       $qr_orderdetails: g("#qr_orderdetails"),
       init: function() {
           this.$qr_pay_buttons.on("click","a.cash",this.pay_cash_process), this.$qr_pay_buttons.on("click","a.card",this.pay_card_process)
       },
       pay_cash_process: function(e) {
           var h;
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
       pay_card_process: function(e) {
           var h;
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
                       f.$qr_orderdetails.html("Scan QR Code on your Phone to enter card details on Phone with Order #"+e.orderid);
                       QR_CODE.makeCode(e.paylink);
                       setInterval(function () {f.check_order_status(e.orderid)}, 5000);
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