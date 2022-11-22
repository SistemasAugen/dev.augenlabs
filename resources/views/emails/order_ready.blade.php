<!DOCTYPE html>
<html lang="en" dir="ltr">
    <style media="screen">
    /*
        Naranja - #eb562d
        Azul - #001899
        Texto - #333333
    */

        /* @font-face {
            font-family: 'Proxima Nova Light';
            src: url('/public/fonts/Proxima-Nova-Light.otf') format("opentype");
        }
        @font-face {
            font-family: 'Proxima Semibold';
            src: url('/public/fonts/Proxima-Nova-Sbold.otf') format("opentype");
        } */

        body {
            background: #E6E6E6;
            padding-top: 35px;
            padding-bottom: 35px;
            font-family: 'Helvetica' !important;
        }

        .container {
            background: #FFFFFF;
            max-width: 600px;
            width: 100%;
            margin: 0 auto;
            padding: 55px;
            text-align: center;
            color: #333333;
        }

        .container img.main-logo {
            max-width: 300px;
            display: block;
            margin: 0 auto;
        }

        .title {
            text-align: left;
            margin-top: 35px;
            margin-left: 60px;
            max-width: 500px;
        }

        .title h1 {
            font-weight: bold;
            /* font-family: 'Proxima Semibold' !important; */
            color: #eb562d;
            font-size: 60px;
            display: inline;
            margin-bottom: 15px;
        }

        .title h3 {
            font-size: 19px;
            display: inline;
        }

        .message {
            padding: 10px 48px;
            padding-bottom: 0px;
            /* border-bottom: 1px solid #333333; */
            font-size: 18px;
            line-height: 30px;
            text-align: left;
        }

        .details {
            text-align: left;
        }

        .details h3 {
            font-weight: bold;
            /* font-family: 'Proxima Semibold' !important; */
            color: #eb562d;
            font-size: 25px;
        }

        .details .content {
            padding: 35px 65px;
            padding-top: 0px !important;
            font-size: 16px;
        }

        .promotion {
            background: #eb562d;
            color: #fff;
            padding-top: 10px;
            padding-bottom: 10px;
            width: 200px !important;
            margin: 0 auto;
            border-radius: 15px;
            display: block;
            height: 60px;
            line-height: 60px;
            margin-top: 25px;
            font-weight: bold;
        }

        .promotion p {
            margin: 0px !important;
            line-height: 25px;
            font-size: 16px;
        }

        .summary {
            max-width: 500px;
            margin-left: 50px;
            text-align: left;
        }

        .summary h3 {
            color: #eb562d;
            margin-top: 45px;
            margin-bottom: 0px;
        }

        .summary .rx-title {
            font-weight: bold;
            color: #001899;
            font-size: 18px;
            line-height: 0px;
            margin-bottom: 0px;
        }

        .summary .rx-body {
            color: #333333;
            margin-top: 0px !important;
        }

        .help {
            margin-top: 25px;
        }

        .help img {
            max-width: 150px;
        }
    </style>
    <body>
        <div class="container">
            <img src="https://sistema.augenlabs.com/public/images/augenlabs.png" class="main-logo" alt="Augen Labs"/>
            <br/>
            <div class="title">
                <h3>Tus recetas</h3><br/>
                <h1>¡ESTÁN LISTAS!</h1><br/>
                <h3>Y todo en orden.<br/>En breve lo estarás recibiendo a tus puerta o nuestro mostrador (depende de lo que hayas elegido)</h3>
            </div>
            <div class="summary">
                <h3>Tus recetas del día</h3>
                @foreach ($orders as $order)
                    <hr/>
                    <p class="rx-title">RX {{ $order->rx }}</p>
                    <p class="rx-body">
                        @if (isset($order->product['id']))
                            <p>{{ $order->product['name']}}, {{ $order->product['subcategory_name'] }}, {{ $order->product['type_name'] }}</p>
                        @endif
                        @if (is_array($order->extras) && count($order->extras) > 0)
                            <p>{{ implode(', ', array_map(function($e) {
                                return $e->name;
                            }, $order->extras)) }}</p>
                        @endif
                    </p>
                @endforeach
            </div>
            <div class="message">
                Si tienes alguna duda sobre este u otros temas relacionados con tus lentes Augen, no dudes en llamarnos.
            </div>
            <div class="help">
                <img src="https://sistema.augenlabs.com/public/images/ayuda-augen.png" alt="Ayuda Augen">
            </div>
        </div>
    </body>
</html>
