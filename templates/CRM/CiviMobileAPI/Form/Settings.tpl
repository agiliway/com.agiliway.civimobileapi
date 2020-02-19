<div class="crm-block crm-form-block crm-form-civimobilesettings-block">
  <div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="top"}
  </div>

  <div>
    <h3>{ts}Extension Info{/ts}</h3>
  </div>

  <div>
    <table class="form-layout-compressed">
      <tbody>
      <tr class="crm-group-form-block-isReserved">
        <td class="label"><label for="civimobile_site_name_to_use">Site name</label> {help id="site-name-help"}</td>
        <td>

          <div>
            {$form.civimobile_site_name_to_use.cms_site_name.html}
            <br />
            {$form.civimobile_site_name_to_use.custom_site_name.html}
            <br />
            {$form.civimobile_custom_site_name.html}
            {if $form.civimobile_is_allow_public_info_api.description}
              <br /><span class="description">{$form.civimobile_is_allow_public_info_api.description}</span>
            {/if}
          </div>

        </td>
      </tr>

      <tr class="crm-group-form-block-isReserved">
        <td class="label"><label>{ts}Version{/ts}</label></td>
        <td>
          {if $latestCivicrmMessage}
            <div class="help">
              {$latestCivicrmMessage}
            </div>
          {/if}
          {if $oldCivicrmMessage}
            <div class="status">
              {$oldCivicrmMessage}
            </div>
          {/if}
        </td>
      </tr>

      <tr class="crm-group-form-block-isReserved">
        <td class="label">
          <label>{ts}Software update{/ts} {help id="software-update-help"}</label>
        </td>
        <td>
          <div>
            {if $isWritable}
              <div>
                {$form.civimobile_auto_update.html}
                {$form.civimobile_auto_update.label}
                {if $form.civimobile_auto_update.description}
                  <br />
                  <span class="description">{$form.civimobile_auto_update.description}</span>
                {/if}
              </div>
            {else}
              {if $folderPermissionMessage}
                <div class="status">
                  {$folderPermissionMessage}
                </div>
              {/if}
            {/if}

          </div>
        </td>
      </tr>

      {if !$isCorrectExtensionName}
        <tr class="crm-group-form-block-isReserved">
          <td class="label">
            <label>{ts}Extension name doesn't correct{/ts}</label>
          </td>
          <td>
            <div>
              <div class="status">
                <div>
                  <span>{ts}Current extension name:{/ts}</span>
                  <span><strong>{$currentExtensionName}</strong></span>
                </div>
                <div>
                  <span>{ts}Required extension name:{/ts}</span>
                  <span><strong>{$correctExtensionName}</strong></span>
                </div>
                <div>
                  <span>{ts}Current extension path:{/ts}</span>
                  <span><strong>{$currentExtensionPath}</strong></span>
                </div>
              </div>
            </div>
          </td>
        </tr>
      {/if}

      </tbody>
    </table>
  </div>

  <div>
    <h3>{ts}Public Area Settings{/ts}</h3>
  </div>

  <div>
    <table class="form-layout-compressed">
      <tbody>

      <tr class="crm-group-form-block-isReserved">
        <td class="label">{$form.civimobile_is_allow_public_info_api.label} {help id="is-allow-public-api-help"}</td>
        <td>

          <div>
              {$form.civimobile_is_allow_public_info_api.html}
              {if $form.civimobile_is_allow_public_info_api.description}
                <br /><span class="description">{$form.civimobile_is_allow_public_info_api.description}</span>
              {/if}
          </div>

        </td>
      </tr>

      <tr class="crm-group-form-block-isReserved">
        <td class="label">{$form.civimobile_is_allow_public_website_url_qrcode.label} {help id="is-allow-public-website-url-qrcode-help"}</td>
        <td>

          <div>
            {$form.civimobile_is_allow_public_website_url_qrcode.html}
            {if $form.civimobile_is_allow_public_website_url_qrcode.description}
              <br /><span class="description">{$form.civimobile_is_allow_public_website_url_qrcode.description}</span>
            {/if}
          </div>

        </td>
      </tr>

      </tbody>
    </table>
  </div>

  <div>
    <h3>{ts}Push Notifications{/ts} {help id="push-notifications-help"}</h3>
  </div>

  <div>
    <table class="form-layout-compressed">
      <tbody>

      <tr class="crm-group-form-block-isReserved">
        <td class="label">{$form.civimobile_server_key.label} {help id="server-key-help"}</td>
        <td>
          <div>
            {if $serverKeyInValidMessage}
              <div class="status">
                {$serverKeyInValidMessage}
              </div>
            {/if}

            {if $serverKeyValidMessage}
              <div class="help">
                {$serverKeyValidMessage}
              </div>
            {/if}
          </div>

          <div>
            {$form.civimobile_server_key.html}
            {if $form.civimobile_server_key.description}
              <br /><span class="description">{$form.civimobile_server_key.description}</span>
            {/if}
          </div>

          <div>
            <p class="description">
              {$pushNotificationMessage}
            </p>
          </div>
        </td>
      </tr>

      </tbody>
    </table>
  </div>

  <div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="bottom"}
  </div>
</div>

{literal}
  <script>
    CRM.$(function ($) {
      handleSiteName();
      handleIsAllowPublicInfoApi();
      $("input[name='civimobile_site_name_to_use']").change(handleSiteName);
      $("input[name='civimobile_is_allow_public_info_api']").change(handleIsAllowPublicInfoApi);

      function handleSiteName() {
        if ($("input[name='civimobile_site_name_to_use']:checked").val() == 'cms_site_name') {
          $("input[name='civimobile_custom_site_name']").attr('disabled', 'disabled');
        } else {
          $("input[name='civimobile_custom_site_name']").removeAttr('disabled');
        }
      }

      function handleIsAllowPublicInfoApi() {
        var isAllowPublicWebsiteUrlQrCodeCheckbox = $("input[name='civimobile_is_allow_public_website_url_qrcode']");
        if ($("input[name='civimobile_is_allow_public_info_api']:checked").val() != 1) {
          isAllowPublicWebsiteUrlQrCodeCheckbox.attr("disabled", "disabled");
          isAllowPublicWebsiteUrlQrCodeCheckbox.removeAttr("checked");
        } else {
          isAllowPublicWebsiteUrlQrCodeCheckbox.removeAttr("disabled");
        }
      }
    });
  </script>
{/literal}
