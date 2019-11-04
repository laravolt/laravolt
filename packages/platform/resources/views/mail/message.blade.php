<table cellspacing="0" cellpadding="0" border="0" width="100%"
       style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; border-collapse: collapse !important; mso-table-lspace: 0pt; mso-table-rspace: 0pt; margin: 20px 0;">
    <tbody>
    <tr>
        <td align="center"
            style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; font-family: Open Sans, Helvetica, Arial, sans-serif; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
            <p style="-moz-osx-font-smoothing: grayscale; -webkit-font-smoothing: antialiased; border: 0; color: {{ config('laravolt.ui.mail.content.color') }}; font: 400 16px/25px apple-system, BlinkMacSystemFont, Arial, 'Segoe UI', 'Helvetica Neue', sans-serif; margin: 0px 0 10px; padding: 0; vertical-align: baseline;">
            {!! $slot !!}
            </p>
        </td>
    </tr>
    </tbody>
</table>
