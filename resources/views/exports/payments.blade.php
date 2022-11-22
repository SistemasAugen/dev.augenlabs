<table>
    <tr>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">RX</th>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">Cliente</th>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">Laboratorio</th>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">Fecha de Creación</th>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">Fecha de Pago</th>
    </tr>
    @foreach ($orders as $order)
    <tr>
        <td>{{ $order->rx}}</td>
        <td>{{ $order->client->name}}</td>
        <td>{{ $order->laboratory->name}}</td>
        <td>{{ date('d/m/Y', strtotime($order->created_at)) }}</td>
        <td>{{ date('d/m/Y', strtotime($order->payment_date)) }}</td>
    </tr>
    @endforeach
</table>
