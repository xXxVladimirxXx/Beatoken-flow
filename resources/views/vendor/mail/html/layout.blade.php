<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="color-scheme" content="light">
<meta name="supported-color-schemes" content="light">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link
    href="https://fonts.googleapis.com/css2?family=Roboto:wght@100&display=swap"
    rel="stylesheet"
/>
<style>
    body {
        margin: 0;
        font-family: "Roboto", sans-serif;
    }
    p {
        margin: 0;
    }
</style>
<body>
<table style="border-spacing: 0 !important;border-collapse: collapse;margin: 0;padding: 0;max-width: 600px !important;min-width: 320px !important;height: 100% !important;margin: auto;">
    <tbody>
        <tr>
            <td align="center" style="position: relative; background-color: #000">
                <div class="header-block">
                    <div class="header-title" style="position: absolute;top: 50%; left: 50%;transform: translate(-50%, -50%);">
                        <img
                            src="{{asset('assets/img/logo-logged.png')}}"
                            width="317px"
                            height="58px"
                            alt="logo"
                        />
                        <p style=" margin-top: 5px;font-size: 20px;text-align: center;color: #fff;">
                            Music NFT Marketplace
                        </p>
                    </div>
                </div>
            </td>
        </tr>

        {{-- {{ $header ?? '' }}--}}
        <!-- Email Body -->
        <tr class="email-body">
            <td class="body" width="100%" cellpadding="0" cellspacing="0">
                <table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
                    <!-- Body content -->
                    <tr>
                        <td class="content-cell">
                            {{ Illuminate\Mail\Markdown::parse($slot) }}
                            {{-- {{ $subcopy ?? '' }} --}}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        {{-- {{ $footer ?? '' }}--}}

        <tr>
            <td style="position: relative; background-color: #000">
                <div class="header-block">
                    <div
                        class="header-title"
                        style="position: absolute;top: 50%;left: 50%;transform: translate(-50%, -50%);">
                        <p style="margin-top: 5px;font-size: 20px;text-align: center;color: #fff;">
                            Copyright © 2021 BEATOKEN. All rights reserved
                        </p>
                        <p style="margin-top: 5px;font-size: 20px;text-align: center;color: #fff;">
                            Bredgade 19E, 1266 København K
                        </p>
                        <p style="margin-top: 5px;font-size: 20px;text-align: center;color: #fff;">
                            Manage preferences
                        </p>
                    </div>
                </div>
            </td>
        </tr>

    </tbody>
</table>
</body>
</html>
