<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml"
    xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
    <meta charset="utf-8"> <!-- utf-8 works for most cases -->
    <meta name="viewport" content="width=device-width"> <!-- Forcing initial-scale shouldn't be necessary -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge"> <!-- Use the latest (edge) version of IE rendering engine -->
    <meta name="x-apple-disable-message-reformatting"> <!-- Disable auto-scale in iOS 10 Mail entirely -->
    <title></title> <!-- The title tag shows in email notifications, like Android 4.4. -->

    {{--
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Bangers&family=M+PLUS+Rounded+1c:wght@500&family=Open+Sans:wght@300;400&family=Poppins:wght@100&display=swap"
        rel="stylesheet"> --}}

    <!-- CSS Reset : BEGIN -->
    <style>
        html,
        body {
            margin: 0 auto !important;
            padding: 0 !important;
            height: 100% !important;
            width: 100% !important;
            background: #f1f1f1;
        }
    </style>
</head>

<body width="100%"
    style="margin: 0; padding: 0 !important; mso-line-height-rule: exactly; background-color: #FAFDED;">
    <div style="margin: 70px 90px 0 90px;">
        <table style="width:100%">
            <tr>
                <td style="width: 25%">
                    <img width="140" src="{{ public_path('/images/main-logo.png') }}">
                </td>
                <td style="width: 50%">
                    <h2 style="text-align: center; font-size: 45px; margin: 0; color: brown">CERTIFICATE</h2>
                    <h4 style="text-align: center; font-size: 20px; margin: 0; color: green">OF COMPLETION
                        {{ $data['filtered_flag'] }}</h4>
                </td>
                <td style="width: 25%">
                    <div style="text-align: right">
                        <img width="180" src="{{ public_path('/images/ribbon.png') }}">
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div style="margin-top: 30px">
        <center>PROUDLY PRESENTED TO</center>
    </div>
    <div style="margin-top: 60px">
        <center>
            <h3 style="margin-bottom: 0">
                {{ strtoupper($data['student_name']) }}
            </h3>
            <hr style="width: 60%; border: 1px solid brown; text-align: center">
        </center>
    </div>
    <center>
        <p style="margin: 0; text-align; center; font-size: 17px; line-height: 26px">
            In recognition and appreciation of the hardwork and perseverance of the
        </p>
        <p style="margin: 0; text-align; center; font-size: 17px; line-height: 26px">
            successful completion of the activities brought a profound sense of
        </p>
        <p style="margin: 0; text-align; center; font-size: 17px; line-height: 26px">
            achievement and fullfilment.
        </p>
    </center>
    <div>
        <table style="width:100%">
            <tr>
                <td style="width: 33%">
                    <img width="250" src="{{ public_path('/images/abc.png') }}">
                </td>
                <td style="width: 33%">
                    <div style="margin-top: 100px">
                        <center>
                            <hr style="width: 60%; border: 1px solid brown; text-align: center">
                            <h3 style="margin-bottom: 0; color: brown; line-height: 26px">
                                DREW FEIG
                            </h3>
                            <h5 style="margin-top: 0">President</h5>
                        </center>
                    </div>
                </td>
                <td style="width: 33%">
                    <div style="text-align: right">
                        <img width="250" src="{{ public_path('/images/congratulations.png') }}">
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
