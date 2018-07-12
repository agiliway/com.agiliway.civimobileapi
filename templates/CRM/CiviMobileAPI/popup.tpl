<div class="civimobile-bg"></div>
<div class="civimobile-popup">
  <div class="civimobile-container">
    <div id="discribe-text">
      <?=ts('Congratulations, your CiviCRM supports CiviMobile application now. You can download the mobile application at AppStore or Google PlayMarket.');?>
    </div>
    <div class="logo-content">
      <a class="civimobile-logo civimobile-google" href="<?=$google_link;?>" target="_blank"></a>
      <a class="civimobile-logo civimobile-apple" href="<?=$apple_link;?>" target="_blank"></a>
    </div>
    <div id="got-text" class="close" ><span><?=ts('Got it');?></span> <span class="close">&#10005;</span></div>
    <div id="ignore-text" class="close"><?=ts('Ignore');?></div>
  </div>
</div>
<style>
  .civimobile-bg {
    display: none;
  }
  .civimobile-popup {
    position: fixed;
    display: none;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 99999999;
    font-family: Helvetica, Open Sans, sans-serif;
  }
  .civimobile-popup .logo-content .civimobile-logo {
    background: url('<?=CRM_CiviMobileAPI_ExtensionUtil::url("img/logo.png");?>');
    width: 180px;
    height: 55px;
    display: inline-block;
    margin: 0 10px;
  }
  .civimobile-popup .logo-content .civimobile-google {
    background-position: -232px -24px;
  }
  .civimobile-popup .logo-content .civimobile-apple {
    background-position: -26px -24px;
  }
  @media only screen and (min-width: 960px) {
    .civimobile-popup .logo-content .civimobile-logo {
      float: left; 
    }
    .civimobile-popup {
      width: 100%;
      padding: 20px 0;
      background: <?=$bg_color;?>;
      color:#FFFFFF;
      font-size: 14px;
      line-height: 24px;
    }
    .civimobile-popup .civimobile-container {
      width: 960px;
      margin: 0 auto;
    }
    .civimobile-popup #discribe-text {
      width: 45%;
      float:left;
    }
    .civimobile-popup .logo-content {
      float: left;
      margin-left:10px;
    }
    #ignore-text {
      display:none;
    }
    #got-text {
      margin-bottom: 34px;
      color:#FFFFFF;
      float: right;
      text-transform: uppercase;
    }
    #got-text:hover {
      cursor: pointer;
    }
    #got-text span{
      float: left;
    }
    #got-text span.close{
      margin: 0 0 0 9px; 
      font-size: 20px;
    }
  }

@media only screen and (max-width: 960px) {
  .civimobile-bg {
    z-index: 9999999;
    background: rgba(0,0,0,0.5);
    position: fixed;
    left: 0;
    bottom: 0;
    right: 0;
    top: 0;
  }
  .civimobile-popup {
    position: fixed;
    left: 0;
    bottom: 0;
    right: 0;
    z-index: 99999999;
    width: 100%;
    background: #FFFFFF;
    color: #000000;
    font-size: 17px;
    line-height: 22px;
  }
  .civimobile-container {
    height: 100%;
    text-align: center;
  }
  .civimobile-popup #discribe-text {
    padding: 30px 20px;
  }
  .civimobile-popup .logo-content {
    margin: 0 auto 40px;
    text-align: center;
  }
  #ignore-text {
    border-top: 1px solid #BCBBC1;
    margin: 0 auto;
    padding: 30px 0;
    cursor: pointer;
  }
  #got-text{
    display:none;
  }
</style>
<script type="text/javascript">
function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}
function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}
jQuery(document).ready(function() {
  jQuery('#ignore-text, #got-text').click(function() {
    setCookie("civimobile_popup_close", true, 30);
    jQuery('.civimobile-bg, .civimobile-popup').hide();
  });
  jQuery('.civimobile-bg').click(function() {
    jQuery('.civimobile-bg, .civimobile-popup').hide();
  });
  if(getCookie('civimobile_popup_close') === ""){
    jQuery('.civimobile-bg, .civimobile-popup').show();
  }
});
</script>