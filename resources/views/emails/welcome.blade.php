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
            /* text-align: left; */
            margin-top: 35px;
        }

        .title h1 {
            font-weight: bold;
            /* font-family: 'Proxima Semibold' !important; */
            color: #eb562d;
            font-size: 145px;
            display: inline;
        }

        .title h3 {
            font-size: 20px;
            display: inline;
        }

        .message {
            padding: 45px 55px;
            padding-bottom: 0px;
            /* border-bottom: 1px solid #333333; */
            font-size: 20px;
            line-height: 30px;
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
    </style>
    <body>
        <div class="container">
            <img src="https://sistema.augenlabs.com/public/images/augenlabs.png" class="main-logo" alt="Augen Labs"/>
            <br/>
            <div class="title">
                <h3>Este es el primer</h3><br/>
                <h1>¡HOLA!</h1><br/>
                <h3>de los miles de “holas” que recibirás de nosotros.</h3>
            </div>
            <div class="message">
                El segundo de ellos será el de nuestro mensajero, que pronto te buscará para recolectar tu primera receta y con ello comenzar la que sabemos que será una larga y amable relación.
                <br/><b>Espéralo.</b>
            </div>
            <div class="message" style="padding-top: 35px;">
                <b>Para cualquier otra cosa que necesites, no dudes en contactar a tu vendedor.</b>
            </div>
            <a class="promotion">MI VENDEDOR</a>
        </div>
    </body>
</html>
