<table>
  <tr id="default-qrcode-checkin-event-tr">
    <td>&nbsp;</td>
    <td>
      {$form.default_qrcode_checkin_event.html}
      {$form.default_qrcode_checkin_event.label}
      <div class="help">{ts}QR Code generation option accessible is only available when creating an event. It is not possible to tick the QR Code generation when you edit this event.{/ts}</div>
    </td>
  </tr>
  <tr id="warning-to-check-access-to-upload-files">
    <td></td>
    <td><div class="status">{ts}Please check permission "CiviCRM: access uploaded files" for ANONYMOUS USER. If the permission isn`t available ANONYMOUS USER will not be able to upload QR Code from public event.{/ts}</div></td>
  </tr>
</table>

<script type="text/javascript">
    CRM.$('tr#default-qrcode-checkin-event-tr').insertAfter('tr.crm-event-manage-eventinfo-form-block-is_active');
    CRM.$('tr#warning-to-check-access-to-upload-files').insertAfter('tr#default-qrcode-checkin-event-tr');
</script>

