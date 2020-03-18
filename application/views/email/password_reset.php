<html>
<head>
    <meta name='viewport' content='width=device-width' />
    <meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
    <style>
        body {
            background-color: #ddd
        }
        .main_area {
            background: #fff;
            width: 60%;
            margin: 0 auto;
            padding: 50px 60px 83px 60px;
            margin-top: 28px
        }
        .footer_area {
            background: #fff;
            width: 60%;
            margin: 0 auto;
            padding: 0px 60px 0px 60px;
            border-top: 1px solid #ddd;
            margin-bottom: 28px
        }
        .logo td img {
            margin-bottom: 13px
        }
        .title {
            text-align: center
        }
        .logo td,
        .title td h2 {
            text-align: center
        }
        h1 {
            border-bottom: 3px solid red;
            display: inline-block
        }
        p {
            font-size: 17px;
            line-height: 29px
        }
        .footer_area p,
        .footer_area ul li p {
            color: #aaa
        }

        @media only screen and (max-width: 1100px) {
            table[class=body] h1 {
                font-size: 28px !important;
                margin-bottom: 10px !important
            }

            .main_area {
                width: 80%
            }

            .footer_area {
                width: 80%
            }
        }

        @media only screen and (max-width: 720px) {
            table[class=body] h1 {
                font-size: 28px !important;
                margin-bottom: 10px !important
            }

            .main_area {
                width: 100%
            }

            .footer_area {
                width: 100%
            }
        }
    </style>
</head>

<body class="body">
    <table class="main_area">
        <tr class="logo">
            <td><img src="https://spellingbee.champs21.com/wp-content/uploads/2020/01/SB-Logo.png" style="width:120px;"></td>
        </tr>
        <tr class="title">
            <td>
                <h1><?= $subject ?></h1>
            </td>
        </tr>
        <tr class="content">
            <td>
                <p><?= $body ?></p>
            </td>
        </tr>
    </table>
    <table class="footer_area">
        <tr>
            <td style="text-align: center;">
                <ul style="padding: 0px; margin-top: 5px;">
                    <li style="list-style: none; margin-right: 30px; display: inline-block;">
                        <p style="font-size: 14px;"> 
                            <span style="margin-top: 0px; margin-left: 5px;">info@teamworkbd.com</span>
                        </p>
                    </li>
                    <li style="list-style: none; margin-right: 30px; display: inline-block;">
                        <p style="font-size: 14px;">
                            <span style="margin-top: 0px; margin-left: 5px;">+8809612212121</span>
                        </p>
                    </li>
                </ul>
            </td>
        </tr>
    </table>
</body>
</html>