<table style="font-family: 'Helvetica';">
    {{-- SHEET HEADER --}}
    <tr>
        <td rowspan="5" colspan="3"></td>
        <td rowspan="5" colspan="3">
            <b>CLIENTE:</b>{{ $client->name }} <br/><br/>
            <b>FECHA DE CONSULTA:</b> {{ date('d/m/Y') }}
        </td>
        <td rowspan="5" colspan="6" style="font-weight: bold;"> ESTADO DE CUENTA</td>
    </tr>
    <tr></tr>
    <tr></tr>
    <tr></tr>
    <tr></tr>
    <tr></tr>
    {{-- END SHEET HEADER --}}

    {{-- SHEET BODY --}}
    <tr>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">RX</th>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">Fecha</th>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">Semanas Restantes</th>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;"></th>
        {{-- <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;"></th> --}}
        {{-- <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;"></th> --}}
        {{-- <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;"></th> --}}
        {{-- <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;"></th> --}}
        {{-- <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">Plazo</th> --}}
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">Costo</th>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">Descuento</th>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">Descuento Adicional</th>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">Subtotal</th>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">IVA</th>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">Total</th>
    </tr>
    @foreach ($data as $i => $row)
        <tr>
            <td style="text-align: center; border: 1px solid black; background-color: {{ $i % 2 == 0 ? '#d8d8d8' : '#ffffff'}};">{{ $row['rx'] }}</td>
            <td style="text-align: center; border: 1px solid black; background-color: {{ $i % 2 == 0 ? '#d8d8d8' : '#ffffff'}};">{{ $row['fecha'] }}</td>
            <td style="text-align: center; border: 1px solid black; background-color: {{ $i % 2 == 0 ? '#d8d8d8' : '#ffffff'}};">{{ $row['semanas_restantes'] }}</td>
            <td style="text-align: center; border: 1px solid black; background-color: {{ $i % 2 == 0 ? '#d8d8d8' : '#ffffff'}};">{{ $row['diseno'] }} / {{ $row['material'] }} / {{ $row['caracteristica'] }} / {{ $row['antireflejante'] }}</td>
            {{-- <td style="text-align: center; border: 1px solid black; background-color: {{ $i % 2 == 0 ? '#d8d8d8' : '#ffffff'}};"></td> --}}
            {{-- <td style="text-align: center; border: 1px solid black; background-color: {{ $i % 2 == 0 ? '#d8d8d8' : '#ffffff'}};"></td> --}}
            {{-- <td style="text-align: center; border: 1px solid black; background-color: {{ $i % 2 == 0 ? '#d8d8d8' : '#ffffff'}};"></td> --}}
            {{-- <td style="text-align: center; border: 1px solid black; background-color: {{ $i % 2 == 0 ? '#d8d8d8' : '#ffffff'}};">{{ $row['plazo'] }}</td> --}}
            <td style="text-align: center; border: 1px solid black; background-color: {{ $i % 2 == 0 ? '#d8d8d8' : '#ffffff'}};">{{ $row['subtotal'] }}</td>
            <td style="text-align: center; border: 1px solid black; background-color: {{ $i % 2 == 0 ? '#d8d8d8' : '#ffffff'}};">{{ $row['descuentos_sistema'] }}</td>
            <td style="text-align: center; border: 1px solid black; background-color: {{ $i % 2 == 0 ? '#d8d8d8' : '#ffffff'}};">{{ $row['descuento'] }}</td>
            <td style="text-align: center; border: 1px solid black; background-color: {{ $i % 2 == 0 ? '#d8d8d8' : '#ffffff'}};">{{ $row['total'] - $row['iva'] }}</td>
            <td style="text-align: center; border: 1px solid black; background-color: {{ $i % 2 == 0 ? '#d8d8d8' : '#ffffff'}};">{{ $row['iva'] }}</td>
            <td style="text-align: center; border: 1px solid black; background-color: {{ $i % 2 == 0 ? '#d8d8d8' : '#ffffff'}};">{{ $row['total'] }}</td>
        </tr>
    @endforeach
    {{-- END SHEET BODY --}}

    {{-- SHEET FOOTER --}}
    <tr>
        <td rowspan="6" colspan="4">
            Le recordamos que el número de cuenta donde debe realizar la transferencia es:
            <br/>
            <br/>
            <b>DISTRIBUIDORA DE LABORATORIOS ÓPTICOS, S DE RL DE CV <br/> BANORTE</b>
            <p>Clabe Interbancaria: <b>072 320 01027637749 8</b> </p>
            <p>Cuenta: <b></b> 1027637749</p>
        </td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    {{-- <tr> --}}
        {{-- <td>% Desc.</td> --}}
        {{-- <td>-</td> --}}
    {{-- </tr> --}}
    {{-- <tr> --}}
        {{-- <td>IVA</td> --}}
        {{-- <td></td> --}}
    {{-- </tr> --}}
    {{-- <tr> --}}
        {{-- <td>TOTAL</td> --}}
        {{-- <td></td> --}}
    {{-- </tr> --}}
    {{-- END SHEET FOOTER --}}
</table>
