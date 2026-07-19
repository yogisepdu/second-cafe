<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CafeTable;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CafeTableQrCodeController extends Controller
{
    public function show(CafeTable $cafeTable): View
    {
        $this->ensureAdmin();

        $result = $this->generateQrCode($cafeTable);

        return view('admin.cafe-tables.qr-code', [
            'cafeTable' => $cafeTable,
            'orderingUrl' => $cafeTable->qr_url,
            'qrCodeDataUri' => $result->getDataUri(),
        ]);
    }

    public function image(CafeTable $cafeTable): Response
    {
        $this->ensureAdmin();

        $result = $this->generateQrCode($cafeTable);

        return response(
            $result->getString(),
            200,
            [
                'Content-Type' => $result->getMimeType(),
                'Content-Length' => strlen(
                    $result->getString()
                ),
                'Cache-Control' =>
                'no-store, no-cache, must-revalidate',
            ],
        );
    }

    public function download(
        CafeTable $cafeTable
    ): Response {
        $this->ensureAdmin();

        $result = $this->generateQrCode($cafeTable);

        $filename = 'qr-code-'
            . Str::slug($cafeTable->table_number)
            . '.png';

        return response(
            $result->getString(),
            200,
            [
                'Content-Type' => $result->getMimeType(),
                'Content-Disposition' =>
                'attachment; filename="'
                    . $filename
                    . '"',
                'Content-Length' => strlen(
                    $result->getString()
                ),
                'Cache-Control' =>
                'no-store, no-cache, must-revalidate',
            ],
        );
    }

    private function generateQrCode(
        CafeTable $cafeTable
    ) {
        return Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->data($cafeTable->qr_url)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(
                ErrorCorrectionLevel::High
            )
            ->size(600)
            ->margin(20)
            ->roundBlockSizeMode(
                RoundBlockSizeMode::Margin
            )
            ->validateResult(false)
            ->build();
    }

    private function ensureAdmin(): void
    {
        abort_unless(
            auth()->check()
                && auth()->user()->isAdmin(),
            403,
        );
    }
}
