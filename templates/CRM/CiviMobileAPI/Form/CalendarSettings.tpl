<div class="crm-block crm-form-block crm-form-civimobilesettings-block">
    <div class="crm-submit-buttons">
        {include file="CRM/common/formButtons.tpl" location="top"}
    </div>
  <div>
    <table class="form-layout-compressed">
      <tbody>
      {foreach from=$elementNames item=elementName}
        <tr class="crm-group-form-block-isReserved {$elementName}">
          <td class="label" style="width: 30%; min-width: 205px">
            <label for="{$elementName}">
              {$form.$elementName.label} {help id=$elementName title=$form.$elementName.label}
            </label>
          </td>
          <td>
            <div>
              {$form.$elementName.html}
            </div>
          </td>
        </tr>
      {/foreach}
      </tbody>
    </table>
    {if $synchronizationNotice}
        <div class="status">
            {$synchronizationNotice}
        </div>
    {/if}

    <div class="crm-submit-buttons">
        {include file="CRM/common/formButtons.tpl" location="bottom"}
    </div>
  </div>
</div>

{literal}
<script>
  CRM.$(function ($) {
    $(document).ready(function () {
      var synchronizationCheckbox = CRM.$("#synchronize_with_civicalendar");

      if (synchronizationCheckbox.prop('checked')) {
        $('.activity_types, .event_types, .case_types, .hide_past_events').toggle();
      }

      synchronizationCheckbox.change(function() {
        $('.activity_types, .event_types, .case_types, .hide_past_events').toggle();
      });
    });
  });
</script>
{/literal}
