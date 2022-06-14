@extends('mail-templates.layout.layout')

@section('content')
    <tr>
        <td align="center" bgcolor="#f3f3f3">
            <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td height="50"></td>
                </tr>
                <!-- title -->
                <tr>
                    <td align="center"
                        style="font-size:36px; color:#4f3f71; font-weight: bold; letter-spacing:4px;">
                        Reset your password
                    </td>
                </tr>
                <!-- end title -->
                <tr>
                    <td align="center">
                        <table width="25" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td height="15" style="border-bottom:2px solid #4f3f71;"></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td height="20"></td>
                </tr>
                <!-- content -->
                <tr>
                    <td align="center" style="font-size:14px; color:#7f8c8d; line-height:29px;">
                        <p>You have requested a password reset for you Beatoken account. If it wasn’t you, please ignore this e-mail. If you would like to reset your password, click the button below.</p>
                    </td>
                </tr>
                <!-- end content -->
                <tr>
                    <td height="50"></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td bgcolor="#FFFFFF" style="border-bottom-left-radius: 4px;border-bottom-right-radius: 4px;"
            align="center">
            <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td height="40"></td>
                </tr>
                <!-- button -->
                <tr>
                    <td align="center">
                        <table class="textbutton" align="center" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td class="btn-link" bgcolor="#1c70de" height="55" align="center" style="font-size:16px;font-weight: bold;padding-left: 25px;padding-right: 25px;border-radius:4px">
                                    <a href="{{ asset('/reset-pass/' . $token) }}" style="height:55px;color:#fff;text-decoration:none">Reset password</a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <!-- end button -->
                <tr>
                    <td height="25"></td>
                </tr>
                <!-- preference -->
                <tr>
                    <td align="center" style="font-size:13px; color:#7f8c8d;">
                        <a href="{{ env('APP_URL') }}" target="_blank">Beatoken</a>
                    </td>
                </tr>
                <!-- end preference -->
                <tr>
                    <td height="30"></td>
                </tr>
            </table>
        </td>
    </tr>
@endsection
