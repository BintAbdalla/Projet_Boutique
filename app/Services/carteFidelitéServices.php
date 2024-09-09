<?php

namespace App\Services;

use App\Models\Client;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class carteFidelitéServices
{
    public function generatePdf(string $view, array $data, string $filePath)
    {
        $pdf = Pdf::loadView($view, $data);
        $pdf->save($filePath);
    }

    public function generateFidelityCard($client, $qrCodePath, $photoPath): string
    {
        // Log::info('QR Code Path: ' . $qrCodePath);
        // Log::info('Filename URL: ' . $photoPath);
        $qrCodeContent = Storage::disk('public')->get($qrCodePath);
        $qrCodeBase64 = 'data:image/png;base64,' . base64_encode($qrCodeContent);
        
        $data = [
            'client' => $client,
            'qrCodePath' => $qrCodeBase64,
            'photoPath' => $photoPath,
        ];
        
        $filePath = storage_path('app/public/pdf/' . $client->id . '.pdf');
        
        // Generate the PDF using the view and data
        $this->generatePdf('CarteFidelite', $data, $filePath);
        // dd($data);
        // dd($filePath);
        
        // Return the path to the generated PDF
        // dd($filePath);
        return $filePath;
    }
    public function generateFidelityCardForClient(Client $client, string $qrCodePath, $photoPath): string
    {

        $photoPath = $client->user->photo;
        $encodedPhoto = $this->encodePhotoToBase64($photoPath);

        $fidelityCardPath = $this->generateFidelityCard($client, $qrCodePath, $encodedPhoto);

        return $fidelityCardPath;
    }
    protected function encodePhotoToBase64($photoUrl)
    {
        if ($photoUrl) {
            try {
                $imageData = file_get_contents($photoUrl);
                $imageExtension = pathinfo(parse_url($photoUrl, PHP_URL_PATH), PATHINFO_EXTENSION);
                return 'data:image/' . $imageExtension . ';base64,' . base64_encode($imageData);
            } catch (\Exception $e) {
                return null;
            }
        }

        return null;
    }
}
