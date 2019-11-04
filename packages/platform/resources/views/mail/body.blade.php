<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta charset="utf-8">
    <title></title>
    <style>
        body {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        img {
            -ms-interpolation-mode: bicubic;
        }

        img {
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }

        body {
            height: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }

        @media screen and (max-width: 500px) {
            .img-max {
                width: 100% !important;
                max-width: 100% !important;
                height: auto !important;
            }

            .max-width {
                max-width: 100% !important;
            }

            .mobile-wrapper {
                width: 85% !important;
                max-width: 85% !important;
            }

            .mobile-padding {
                padding-left: 5% !important;
                padding-right: 5% !important;
            }
        }
    </style>
</head>

<body style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; height: 100% !important; margin: 0; padding: 0; width: 100% !important;"
      bgcolor="{{ config('laravolt.ui.mail.body') }}">
<!-- HIDDEN PREHEADER TEXT -->
<div style="display: none; font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden;">
    {{ $preheader ?? '' }}
</div>
<table border="0" cellpadding="0" cellspacing="0"
       width="100%"
       style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; border-collapse: collapse !important; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
    <tbody>
    <tr>
        <td align="center" valign="top" width="100%" bgcolor="{{ config('laravolt.ui.mail.header') }}"
            style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;mso-table-lspace: 0pt; mso-table-rspace: 0pt; padding: 35px 15px 0;"
            class="mobile-padding">
            <!--[if (gte mso 9)|(IE)]>
            <table align="center" border="0" cellspacing="0" cellpadding="0" width="500">
                <tr>
                    <td align="center" valign="top" width="500">
            <![endif]-->
            <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%"
                   style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; border-collapse: collapse !important; max-width: 500px; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                <tbody>
                <tr>
                    <td align="center" bgcolor="{{ config('laravolt.ui.mail.content.background') }}"
                        style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; border-radius: 10px 10px 0 0; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                        <div class="header"
                             style="-moz-osx-font-smoothing: grayscale; -webkit-font-smoothing: antialiased; border-bottom-color: rgba(0, 0, 0, 0.1); border-bottom-style: solid; border-width: 0 0 1px; font-size: normal; font-style: normal; font-variant: normal; font-weight: normal; line-height: normal; margin: auto 30px; padding: 30px 60px; vertical-align: baseline;">

                            <img src="{{ config('laravolt.ui.brand_image') }}" border="0"
                                 style="-ms-interpolation-mode: bicubic; border: 0; display: block; width: 50px; line-height: 100%; outline: none; text-decoration: none;margin: 0 0 10px 0;">

                            <h2 style="-moz-osx-font-smoothing: grayscale; -webkit-font-smoothing: antialiased; border: 0; color: {{ config('laravolt.ui.mail.content.color') }}; font: 400 16px/24px apple-system, BlinkMacSystemFont, Arial, 'Segoe UI', 'Helvetica Neue', sans-serif; margin: 0; vertical-align: baseline;"
                                align="center">{{ config('app.name') }}</h2>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
            <!--[if (gte mso 9)|(IE)]>
            </td>
            </tr>
            </table>
            <![endif]-->
        </td>
    </tr>
    <tr>
        <td align="center" height="100%" valign="top" width="100%"
            style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; mso-table-lspace: 0pt; mso-table-rspace: 0pt; padding: 0 15px 20px;"
            class="mobile-padding">
            <!--[if (gte mso 9)|(IE)]>
            <table align="center" border="0" cellspacing="0" cellpadding="0" width="500">
                <tr>
                    <td align="center" valign="top" width="500">
            <![endif]-->
            <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%"
                   style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; border-collapse: collapse !important; max-width: 500px; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                <tbody>
                <tr>
                    <td align="center" valign="top"
                        style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; font-family: Open Sans, Helvetica, Arial, sans-serif; mso-table-lspace: 0pt; mso-table-rspace: 0pt; padding: 0 0 25px;">
                        <table cellspacing="0" cellpadding="0" border="0" width="100%"
                               style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; border-collapse: collapse !important; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                            <tbody>
                            <tr>
                                <td align="center" bgcolor="{{ config('laravolt.ui.mail.content.background') }}"
                                    style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; border-radius: 0 0 10px 10px; mso-table-lspace: 0pt; mso-table-rspace: 0pt; padding: 25px; color: {{ config('laravolt.ui.mail.content.color') }}">
                                    {!! $slot !!}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
            <!--[if (gte mso 9)|(IE)]>
            </td>
            </tr>
            </table>
            <![endif]-->
        </td>
    </tr>
    <tr>
        <td align="center" height="100%" valign="top" width="100%"
            style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; mso-table-lspace: 0pt; mso-table-rspace: 0pt; padding: 0 15px 40px;">
            <!--[if (gte mso 9)|(IE)]>
            <table align="center" border="0" cellspacing="0" cellpadding="0" width="500">
                <tr>
                    <td align="center" valign="top" width="500">
            <![endif]-->
            <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%"
                   style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; border-collapse: collapse !important; max-width: 500px; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                <tbody>
                <tr>
                    <td align="center" valign="top"
                        style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; color: rgba(140,143,156,0.53); font-family: Open Sans, Helvetica, Arial, sans-serif; mso-table-lspace: 0pt; mso-table-rspace: 0pt; padding: 0;">
                        <p style="font-size: 14px; line-height: 20px;">© {{ config('app.name') }}</p>
                    </td>
                </tr>
                </tbody>
            </table>
            <!--[if (gte mso 9)|(IE)]>
            </td>
            </tr>
            </table>
            <![endif]-->
        </td>
    </tr>
    </tbody>
</table>

</body>

</html>
