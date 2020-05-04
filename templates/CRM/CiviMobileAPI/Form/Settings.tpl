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
            {if $form.civimobile_custom_site_name.description}
              <br /><span class="description">{$form.civimobile_custom_site_name.description}</span>
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
        <td class="label">{$form.civimobile_is_allow_public_info_api.label} {help id="is-allow-public-area-help"}</td>
        <td>
          <div>
            <div>
              {$form.civimobile_is_allow_public_info_api.html}
            </div>
            <div id="itemsToShowMessage" class="status">
              Public Area will show pages:
              <span class="itemsToShow"></span>
            </div>
            <div>
              <span class="description spec-event-note" style="display: none;"><span class="crm-marker">*</span>Note that the all required permissions for Anonymous user should be enabled.</span>
            </div>
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
    <h3>{ts}News{/ts}</h3>
  </div>

  <div>
    <table class="form-layout-compressed">
      <tbody>

      <tr class="crm-group-form-block-isReserved">
        <td class="label">{$form.civimobile_is_showed_news.label} {help id="show-news-help"}</td>
        <td>

          <div>
            {$form.civimobile_is_showed_news.html}
            {if $form.civimobile_is_showed_news.description}
              <br /><span class="description">{$form.civimobile_is_showed_news.description}</span>
            {/if}
          </div>

        </td>
      </tr>

      <tr class="crm-group-form-block-isReserved">
        <td class="label">{$form.civimobile_news_rss_feed_url.label} {help id="news-rss-feed-url-help"}</td>
        <td>

          <div>
            {$form.civimobile_news_rss_feed_url.html}
            {if $defaultRssFeedUrl}
              <br /><span class="description">
                <button class="crm-button default-rss-feed-url-btn" data-default-rss-feed-url="{$defaultRssFeedUrl}" type="button">{ts}Set default RSS feed on CMS{/ts}</button>
              </span>
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
        <td class="label">{$form.civimobile_is_custom_app.label} {help id="is-custom-app-help"}</td>
        <td>

          <div>
            {$form.civimobile_is_custom_app.html}
            {if $form.civimobile_is_custom_app.description}
              <br /><span class="description">{$form.civimobile_is_custom_app.description}</span>
            {/if}
          </div>

        </td>
      </tr>

      </tbody>
    </table>
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

      <tr class="crm-group-form-block-isReserved">
        <td class="label">{$form.civimobile_firebase_key.label} {help id="firebase-key-help"}</td>
        <td>
          <div>
            {$form.civimobile_firebase_key.html}
            {if $form.civimobile_firebase_key.description}
              <br /><span class="description">{$form.civimobile_firebase_key.description}</span>
            {/if}
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
    var possibleItemsToDisplayInPublicArea = "{/literal}{$possibleItemsToDisplayInPublicArea}{literal}".split(', ');
    CRM.$(function ($) {
      handleSiteName();
      handleFirebaseKey();
      handleShowNews();
      changeShownItemsOnPublicArea();
      $("input[name='civimobile_site_name_to_use']").change(handleSiteName);
      $("input[name='civimobile_is_custom_app']").change(handleFirebaseKey);
      $("input[name='civimobile_is_showed_news']").change(handleShowNews);
      $("input[name='civimobile_is_allow_public_info_api']").change(changeShownItemsOnPublicArea);

      $(".default-rss-feed-url-btn").click(function () {
        $("input[name='civimobile_news_rss_feed_url']").val($(".default-rss-feed-url-btn").data('default-rss-feed-url'));
      });

      function handleSiteName() {
        if ($("input[name='civimobile_site_name_to_use']:checked").val() == 'cms_site_name') {
          $("input[name='civimobile_custom_site_name']").attr('disabled', 'disabled');
        } else {
          $("input[name='civimobile_custom_site_name']").removeAttr('disabled');
        }
      }

      function handleFirebaseKey() {
        var firebaseKey = $("input[name='civimobile_firebase_key']");
        var serverKey = $("input[name='civimobile_server_key']");
        if ($("input[name='civimobile_is_custom_app']:checked").val() != 1) {
          firebaseKey.closest('tr').hide();
          serverKey.closest('tr').show();
        } else {
          serverKey.closest('tr').hide();
          firebaseKey.closest('tr').show();
        }
      }

      function handleShowNews() {
        if ($("input[name='civimobile_is_showed_news']:checked").val() != 1) {
          $("input[name='civimobile_news_rss_feed_url']").attr("disabled", "disabled");
          $(".default-rss-feed-url-btn").attr("disabled", "disabled");
        } else {
          $("input[name='civimobile_news_rss_feed_url']").removeAttr("disabled");
          $(".default-rss-feed-url-btn").removeAttr("disabled");
        }
        changeShownItemsOnPublicArea();
      }

      function changeShownItemsOnPublicArea() {
        let shownItems = [];
        $(".spec-event-note").hide();
        if ($("input[name='civimobile_is_allow_public_info_api']:checked").val() == 1) {
          if ($("input[name='civimobile_is_showed_news']:checked").val() == 1) {
            shownItems.push("News");
          }
          if (possibleItemsToDisplayInPublicArea.indexOf('Events') !== -1) {
            shownItems.push("Events<span class=\"crm-marker\">*</span>");
            $(".spec-event-note").show();
          }
        }

        if (shownItems.length === 0) {
          shownItems.push("none");
          $("#itemsToShowMessage").removeClass('help');
        } else {
          $("#itemsToShowMessage").addClass('help');
        }

        $(".itemsToShow").html(shownItems.join(", "));
      }

    });
  </script>
{/literal}
