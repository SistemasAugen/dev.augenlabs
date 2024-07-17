<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <style>
            body {
                background: #E6E6E6;
                padding-top: 35px;
                padding-bottom: 35px;
                font-family: 'Raleway', sans-serif;
            }
            .container {
                background: #FFFFFF;
                max-width: 600px;
                width: 100%;
                margin: 0 auto;
                /* padding: 55px; */
                text-align: center;
                color: #333333;
            }
            .container img.main-logo {
                max-width: 150px;
                display: block;
                margin: 0 auto;
                margin-top: 25px !important;
            }
            .title {
                text-align: center;
            }
            .title h1 {
                font-weight: bold;
                color: #f38b00;
                font-size: 35px;
            }
            .summary {
                text-align: center;
                margin-top: 20px;
            }
            .summary h3 {
                font-weight: bold;
                color: #f38b00;
                font-size: 18px;
            }
            .summary .rx-title {
                text-align: left;
                font-weight: bold;
                font-size: 16px;
                margin-bottom: 5px;
            }
            .summary .rx-body {
                text-align: left;
                color: #333333;
                margin-top: 0px;
                font-size: 14px;
            }
            .message {
                padding: 10px 20px;
                font-size: 16px;
                line-height: 24px;
                text-align: justify;
            }
            .help {
                margin-top: 25px;
            }
            .help img {
                max-width: 150px;
            }
            .contact-button {
                background: #f38b00;
                color: #fff;
                padding: 15px 25px;
                margin: 20px 0px;
                display: block;
                border-radius: 5px;
                text-decoration: none;
                font-weight: bold;
                font-size: 16px;
                width: 200px;
                text-align: center;
                border-radius: 20px;
            }
            hr {
                border-color: #f38b00;
                width: 90%;
                margin: 0;
            }
            h3 {
                font-weight: normal;
            }
            .banner {
                width: 100%;
            }
            .footer {
                padding-top: 15px;
                padding-bottom: 15px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div style="padding-top: 15px">
                <img src="https://sistema.augenlabs.com/public/images/mailing/augen_icon.png" class="main-logo" alt="Augen Labs"/>
            </div>
            <div class="title">
                <h1>¡Tu orden está lista!</h1>
            </div>
            <div style="height: 260px;">
                <img src="https://sistema.augenlabs.com/public/images/mailing/order_ready_banner.png" class="banner" style="margin-bottom: -5px"/>
            </div>
            <div>
                <table style="border-spacing: 0px;">
                    <tr>
                        <td style="width: 25%; background-color: #f38b00;">
                            <img src="https://sistema.augenlabs.com/public/images/mailing/augen_icon_iso.png" style="max-width: 50%; margin-top: 10px;margin-bottom: 10px;"/>
                        </td>
                        <td style="width: 75%">
                            <div class="summary" style="width: 100%; padding-left: 25px;">
                                @foreach ($orders as $order)
                                    <p class="rx-title">RX {{ $order->rx }}</p>
                                    <p class="rx-body">{{ $order->product['name']}}, {{ $order->product['subcategory_name'] }}, {{ $order->product['type_name'] }}</p>
                                    @if (!$loop->last)
                                        <hr/>
                                    @endif
                                @endforeach
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            <div style="background-color: rgb(242, 242, 242)">
                <table>
                    <tr>
                        <td style="width: 30%">
                            <img src="https://sistema.augenlabs.com/public/images/mailing/experiencia_icon.png" alt="Ayuda Augen" style="max-width: 80%">
                        </td>
                        <td style="width: 70%">
                            <div class="message">
                                Tienes alguna duda o inquietud con tu orden? Ponte en contacto con el <b>Departamento de Experiencia</b> para resolver todas tus dudas.
                                <a href="https://wa.me/3310620319?text=¡Hola! Tengo una duda sobre mis Rxs" class="contact-button">
                                    <img src="https://sistema.augenlabs.com/public/images/mailing/whatsapp_icon.png" style="width: 20px; vertical-align: middle;"/>
                                    Contactanos aqui!</a>
                            </div>
                        </td>  
                    </tr>
                </table>
            </div>
            <div class="footer">
                <table style="width: 100%; padding-left: 15px; padding-right: 15px;">
                    <tr>
                        <td style="width:50%; text-align: left;">
                            <img src="https://sistema.augenlabs.com/public/images/mailing/www_icon.png" style="width: 20px; vertical-align: middle;"/>
                            <a href="https://www.augenlabs.com/" style="font-weight: bold; text-decoration: ">augenlabs.com</a>   
                        </td>
                        <td style="width:50%; text-align: right;">
                            <img src="https://sistema.augenlabs.com/public/images/mailing/augen_icon.png" alt="Augen Labs" style="width: 150px;"/>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </body>
</html>
