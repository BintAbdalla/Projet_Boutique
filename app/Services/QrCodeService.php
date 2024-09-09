<?php

namespace App\Services;

// use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
// use BaconQrCode\Renderer\ImageRenderer;
// use BaconQrCode\Renderer\RendererStyle\RendererStyle;
// use BaconQrCode\Writer;
// use Endroid\QrCode\Builder\Builder;
// use Endroid\QrCode\Writer\PngWriter;
// use Barryvdh\DomPDF\Facade\Pdf;
// use Illuminate\Support\Facades\Storage;

// class QrCodeService
// {
//     // Fonction pour générer le QR code et l'enregistrer localement
//     public function generateQRCode(string $qrContent, string $qrCodePath)
//     {
//         $renderer = new ImageRenderer(
//             new RendererStyle(400),
//             new ImagickImageBackEnd()
//         );
//         $writer = new Writer($renderer);
//         $qrcode = $writer->writeString('Hello World!');
//         return base64_encode($qrcode);
//         stora
      

//     }

// }



// namespace App\Services\Rcode;

use App\Services\Contracts\QrCodeServiceInterface;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class QrCodeService 
{
    public function generateQRCode(string $data, string $filePath): string
    {
        $qrCode = QrCode::format('png')->size(300)->generate($data);
        Storage::disk('public')->put($filePath, $qrCode);
        return $filePath;
    }
}