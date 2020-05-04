<div class="crm-page-civimobile-checklist-block">
  <div class="checklist-table" id="checklist-items-block">
    {foreach from=$checklist_params item=param}
      <div class="checklist-table-row {if $param.status eq 'success'} success-row {elseif $param.status eq 'warning'} warning-row {elseif $param.status eq 'error'} error-row {/if}">
        <div class="checklist-table-cell status-icon">
          <i class="crm-i {if $param.status eq 'success'} fa-check {elseif $param.status eq 'warning'} fa-exclamation-triangle {elseif $param.status eq 'error'} fa-times {/if}"></i>
        </div>
        <div class="checklist-table-cell">
          {$param.title}
        </div>
        <div class="checklist-table-cell">
          {$param.message}
        </div>
      </div>
    {/foreach}
  </div>

  <h2>System Info</h2>

  <div class="checklist-table">
    {foreach from=$system_info item=param}
      <div class="checklist-table-row">
        <div class="checklist-table-cell full-cell">
          {$param.title}
        </div>
        <div class="checklist-table-cell full-cell">
          {$param.message}
        </div>
      </div>
    {/foreach}
  </div>

</div>

{literal}
<script>
  var authUrl = '{/literal}{$authUrl}{literal}';
  var restPathUrl = '{/literal}{$restPathUrl}{literal}';
  var restUrl = '{/literal}{$restUrl}{literal}';

  CRM.$(function ($) {
    $.get(authUrl).always(function(data) {
      if (data.responseJSON.is_error !== null) {
        $("#checklist-items-block").append(generateCheckBlock('Is auth link correct?', 'Auth link is correct.', 'success'));
      } else {
        $("#checklist-items-block").append(generateCheckBlock('Is auth link correct?', 'Auth link is incorrect.', 'error'));
      }
    });

    CRM.api3('CiviMobileSystem', 'get', {
      "sequential": 1
    }).then(function(result) {
      $.get(restUrl).always(function(data) {
        if (JSON.stringify(result) === JSON.stringify(data)) {
          $("#checklist-items-block").append(generateCheckBlock('Is rest url correct?', 'Rest url is not correct.', 'success'));
        } else {
          $("#checklist-items-block").append(generateCheckBlock('Is rest url correct?', 'Rest url is not correct.', 'error'));
        }
      });

      $.get(restPathUrl).always(function(data) {
        if (JSON.stringify(result) === JSON.stringify(data)) {
          $("#checklist-items-block").append(generateCheckBlock('Is rest path correct?', 'Rest path is not correct.', 'success'));
        } else {
          $("#checklist-items-block").append(generateCheckBlock('Is rest path correct?', 'Rest path is not correct.', 'error'));
        }
      });

    });

  });

  function generateCheckBlock($title, $message, $status) {
    var iconClass = '';
    var classRow = '';

    if ($status === 'success') {
      classRow = 'success-row';
      iconClass = 'fa-check';
    } else if ($status === 'warning') {
      classRow = 'warning-row';
      iconClass = 'fa-exclamation-triangle';
    } else if ($status === 'error') {
      classRow = 'error-row';
      iconClass = 'fa-times';
    }

    return "<div class=\"checklist-table-row " + classRow + "\">\n" +
           "  <div class=\"checklist-table-cell status-icon\">\n" +
           "    <i class=\"crm-i " + iconClass + "\"></i>\n" +
           "  </div>\n" +
           "  <div class=\"checklist-table-cell\">\n" +
           "    " + $title + "\n" +
           "  </div>\n" +
           "  <div class=\"checklist-table-cell\">\n" +
           "    " + $message + "\n" +
           "  </div>\n" +
           "</div>";
  }

</script>
{/literal}
