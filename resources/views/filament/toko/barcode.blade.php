@php
    use Milon\Barcode\DNS1D;
    $toko = $getRecord();
@endphp
@if($toko->barcode)
    <img src="data:image/png;base64,{{ (new DNS1D)->getBarcodePNG($toko->barcode, 'C128') }}" alt="Barcode" style="height:40px;">
@endif 