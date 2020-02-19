{literal}
<script>
  CRM.$(function($) {
    cj("head").find('style').remove();
    cj("head").find('link').remove();
    cj("head").append('<meta name="viewport" content="width=device-width, initial-scale=1">');

    cj("head").append('<link rel="stylesheet" type="text/css" href="{/literal}{$absURL}{literal}/css/crm-i.css"/>');
    cj("head").append('<link rel="stylesheet" type="text/css" href="{/literal}{$absURL}{literal}/bower_components/datatables/media/css/jquery.dataTables.min.css"/>');
    cj("head").append('<link rel="stylesheet" type="text/css" href="{/literal}{$absURL}{literal}/bower_components/font-awesome/css/font-awesome.min.css"/>');
    cj("head").append('<link rel="stylesheet" type="text/css" href="{/literal}{$absURL}{literal}/bower_components/jquery-ui/themes/smoothness/jquery-ui.min.css"/>');
    cj("head").append('<link rel="stylesheet" type="text/css" href="{/literal}{$absURL}{literal}/bower_components/select2/select2.min.css"/>');
    cj("head").append('<link rel="stylesheet" type="text/css" href="{/literal}{$absURL}{literal}/css/civicrm.css"/>');

    cj('#Register').parents().siblings().hide();
    cj('#Confirm').parents().siblings().hide();
    cj('#ThankYou').parents().siblings().hide();
    cj('#Register').find('#priceset, #priceset *').hide();
    cj('#pricesetTotal').parents().show();
    cj('#pricesetTotal, #pricesetTotal *').show();
    cj('.event_info_link-section').hide();
  });
</script>

<style>
  @media only screen and (max-width: 767px) {
    body {
      font-size: 16px;
      line-height: 1.2;
    }
    #printer-friendly {
      display: none;
    }
    #branding .breadcrumb {
      display: flex;
      flex-wrap: wrap;
    }
    #branding .breadcrumb a {
      font-size: 16px;
      line-height: 1.2;
    }
    #page {
      margin: 0 15px !important;
    }
    #page .messages {
      font-size: 16px !important;
      padding: 10px;
    }
    #page .messages #errorList {
      margin-top: 5px;
    }
    #page .messages #errorList li {
      margin-bottom: 5px;
      line-height: 1.2;
    }
    #page .messages #errorList li:last-of-type {
      margin-bottom: 0;
    }
    #page .crm-container fieldset legend {
      font-size: 14px !important;
      padding-left: 0;
    }
    #page .crm-event-register-form-block #priceset #pricesetTotal .label {
      width: unset !important;
    }
    #page .crm-event-register-form-block #priceset #pricesetTotal {
      flex-wrap: wrap;
      flex-direction: row;
      justify-content: space-between;
    }
    #page #crm-container.crm-public .label,
    #page #crm-container.crm-public .price-field-amount {
      font-size: 15px !important;
      width: 100% !important;
      text-align: left !important;
    }
    #page #crm-container.crm-public .calc-value,
    #page #crm-container.crm-public .content {
      margin-left: 0;
      font-size: 15px !important;
    }
    #page #crm-container.crm-public #crm-submit-buttons {
      margin-left: 0;
      display: flex;
      flex-wrap: wrap;
      justify-content: flex-end;
    }
    .crm-container.crm-public .select2-container, .crm-container.crm-public .select2-results {
      width: 100% !important;
    }
    #page #crm-container.crm-public .crm-section {
      display: flex;
      flex-wrap: wrap;
      flex-direction: column;
    }
    #page #crm-container.crm-public .crm-section .content input[type="text"] {
      width: 100%;
      height: 40px;
      box-sizing: border-box;
    }
    #page #crm-container.crm-public .crm-section .content input[type="radio"] {
      margin-top: 10px;
      margin-right: 5px;
      margin-left: 0;
    }
    #page #crm-container.crm-public .crm-section .content input[type="radio"]:first-of-type {
      margin-left: 0;
    }
    #page #crm-submit-buttons .crm-button {
      font-size: 13px !important;
      line-height: 1;
    }
    #page #crm-submit-buttons .crm-button:last-of-type {
      margin-right: 0;
    }
    #page .crm-container #civicrm-footer.crm-public-footer {
      font-size: 16px !important;
    }
    #page #billing-payment-block .crm-section.credit_card_info-section > .crm-section {
      margin-top: 10px;
    }
    #page #billing-payment-block .crm-section.cvv2-section > .content {
      display: flex;
      flex-wrap: wrap;
    }
    #page #billing-payment-block .crm-section.cvv2-section > .content input[type="text"] {
      width: calc(100% - 65px);
      order: 1;
    }
    #page #billing-payment-block .crm-section.cvv2-section > .content .crm-error {
      order: 5;
    }
    #page #billing-payment-block .crm-section.cvv2-section > .content .cvv2-icon {
      order: 2;
    }
    #page #billing-payment-block .crm-section.credit_card_exp_date-section > .content select {
      width: 49%;
    }
    .crm-container .crm-error {
      padding: 0 3px;
      box-sizing: border-box;
      line-height: 1.5;
    }
    .crm-credit_card_type-icons {
      margin: 0;
    }
    .crm-credit_card_type-icons {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
    }
    .payment_processor-section .content label {
      width: calc(100% - 40px);
      display: inline-block;
    }
  }
</style>
{/literal}
