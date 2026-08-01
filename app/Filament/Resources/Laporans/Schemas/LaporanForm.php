<?php

namespace App\Filament\Resources\Laporans\Schemas;

use Filament\Schemas\Schema;

class LaporanForm
{
    public static function configure(Schema $schema): Schema
    {
        // Resource laporan bersifat read-only dan hanya memiliki halaman index.
        return $schema->components([]);
    }
}

