<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: Arial, sans-serif;
      font-size: 7pt;
    }

    .page {
      width: 100%;
      padding: 10px;
      page-break-after: always;
    }

    .page:last-child {
      page-break-after: avoid;
    }

    .page-title {
      text-align: center;
      font-size: 12pt;
      font-weight: bold;
      margin-bottom: 12px;
    }

    .barcode-grid {
      width: 100%;
      border-collapse: collapse;
    }

    .barcode-cell {
      width: 20%;
      border: 1px solid #ccc;
      padding: 3px 2px;
      text-align: center;
      vertical-align: top;
    }

    .product-name {
      font-size: 6.5pt;
      font-weight: normal;
      line-height: 1.2;
      margin-bottom: 1px;
      word-break: break-word;
    }

    .product-price {
      font-size: 6.5pt;
      margin-bottom: 2px;
    }

    .barcode-img {
      display: block;
      margin: 0 auto;
      width: 100%;
      max-width: 100px;
      height: auto;
    }

    .barcode-number {
      font-size: 6pt;
      margin-top: 1px;
      letter-spacing: 0.5px;
    }
  </style>
</head>
<body>

@php
  use Milon\Barcode\DNS1D;
  $barcodeGen = new DNS1D();
@endphp

@foreach($products as $product)
  @php
    $formattedPrice = 'Rp. ' . number_format($product->price, 0, ',', '.');
    $pageTitle = 'Barcode: ' . $product->name . ' - ' . $formattedPrice;
    $barcodeValue = $product->barcode;

    try {
      if (strlen($barcodeValue) == 13 && ctype_digit($barcodeValue)) {
        $barcodeBase64 = $barcodeGen->getBarcodePNG($barcodeValue, 'EAN13', 1.5, 40);
      } elseif (strlen($barcodeValue) == 12 && ctype_digit($barcodeValue)) {
        $barcodeBase64 = $barcodeGen->getBarcodePNG($barcodeValue, 'UPCA', 1.5, 40);
      } else {
        $barcodeBase64 = $barcodeGen->getBarcodePNG($barcodeValue, 'C128', 1.5, 40);
      }
    } catch (\Exception $e) {
      $barcodeBase64 = $barcodeGen->getBarcodePNG($barcodeValue, 'C128', 1.5, 40);
    }

    $labels = array_fill(0, $copies, [
      'name' => $product->name,
      'price' => $formattedPrice,
      'barcode' => $barcodeBase64,
      'code' => $barcodeValue,
    ]);
    $rows = array_chunk($labels, 5);
  @endphp

  <div class="page">
    <div class="page-title">{{ $pageTitle }}</div>

    <table class="barcode-grid">
      @foreach($rows as $row)
        <tr>
          @foreach($row as $label)
            <td class="barcode-cell">
              <div class="product-name">{{ $label['name'] }}</div>
              <div class="product-price">{{ $label['price'] }}</div>
              <img class="barcode-img" src="data:image/png;base64,{{ $label['barcode'] }}" alt="{{ $label['code'] }}">
              <div class="barcode-number">{{ $label['code'] }}</div>
            </td>
          @endforeach
          @if(count($row) < 5)
            @for($i = count($row); $i < 5; $i++)
              <td class="barcode-cell"></td>
            @endfor
          @endif
        </tr>
      @endforeach
    </table>
  </div>
@endforeach

</body>
</html>
