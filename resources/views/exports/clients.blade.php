<table>
    <tr>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">#</th>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">NOMBRE DEL CLIENTE</th>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">NOMBRE OMERCIAL</th>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">DIRECCIÓN COMPLETA</th>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">ESTADO</th>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">CIUDAD</th>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">PDV ASIGNADO</th>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">NOMBRE DE CONTACTO</th>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">TELEFONO</th>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">CELULAR</th>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">CORREO</th>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">DIVISIÓN</th>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">FECHA DE INGRESO</th>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">ORIGEN DEL CLIENTE</th>
        <th style="background-color: #0096a4;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">MONOFOCAL</th>
        <th style="background-color: #0096a4;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">BIFOCAL</th>
        <th style="background-color: #0096a4;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">PROGRESIVOS</th>
        <th style="background-color: #0096a4;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">PAQUETES</th>
        <th style="background-color: #0096a4;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">TERMINADOS</th>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">CORREO DE NOTIFICACIONES</th>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">NOMBRE DE CONTACTO</th>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">TELEFONO DE CONTACTO</th>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">CORREO DE CONTACTO</th>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">PLAZO</th>
    </tr>
    @foreach ($clients as $client)
        <tr>
            <td>{{ $client->id }}</td>
            <td>{{ $client->name }}</td>
            <td>{{ $client->comertial_name }}</td>
            <td>{{ mb_strtoupper(trim(sprintf('%s, %s, %s, %s, %s', $client->address, $client->suburb, $client->postal_code, $client->town->name, $client->state->name))) }}</td>
            <td>{{ $client->state->name }}</td>
            <td>{{ $client->town->name }}</td>
            <td>{{ @$client->branch->name ?: '-' }}</td>
            <td>{{ @$client->name ?: '-' }}</td>
            <td>{{ @$client->phone ?: '-' }}</td>
            <td>{{ @$client->celphone ?: '-' }}</td>
            <td>{{ @$client->email ?: '-' }}</td>
            <td>{{ @$client->category ?: '-' }}</td>
            <td>{{ date('d/m/Y', strtotime($client->created_at)) }}</td>
            <td>{{ @$client->contact_celphone ?: '-' }}</td>
            <td>{{ @$client->monofocalDiscount ?: '0' }} %</td>
            <td>{{ @$client->bifocalDiscount ?: '0' }} %</td>
            <td>{{ @$client->progresiveDiscount ?: '0' }} %</td>
            <td>{{ @$client->packagesDiscount ?: '0' }} %</td>
            <td>{{ @$client->finishedDiscount ?: '0' }} %</td>
            <td>{{ @$client->notification_mail ?: '-' }}</td>
            <td>{{ @$client->contact_name ?: '-' }}</td>
            <td>{{ @$client->contact_phone ?: '-' }}</td>
            <td>{{ @$client->contact_email ?: '-' }}</td>
            <td>{{ @$client->payment_plan ?: 0 }}</td>
        </tr>
    @endforeach
</table>
