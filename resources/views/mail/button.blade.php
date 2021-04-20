<table border="0" cellspacing="0" cellpadding="0"
       style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; border-collapse: collapse !important; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
    <tbody>
    <tr>
        <td align="center"
            style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; border-radius: 26px; mso-table-lspace: 0pt; mso-table-rspace: 0pt;padding: 0 0 20px 0">
            <a href="{{ $url }}" target="_blank"
               style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; background: {{ config('laravolt.ui.mail.button.background') }}; color: {{ config('laravolt.ui.mail.button.color') }}; border-radius: 26px; display: block; font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; padding: 14px 26px; text-decoration: none;">
                {{ $slot }}
            </a>
        </td>
    </tr>
    </tbody>
</table>
