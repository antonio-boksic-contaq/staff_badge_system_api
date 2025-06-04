<?php
namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PunchesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $punches;

    public function __construct(Collection $punches)
    {
        $this->punches = $punches;
    }

    public function collection()
    {
        return $this->punches;
    }

    // Intestazioni leggibili
    public function headings(): array
    {
        return [
            'Nome',
            'Cognome',
            'Entrata',
            'Uscita',
            'Note'
        ];
    }

    // Mappa ogni riga: escludi le colonne che non ti servono
    public function map($row): array
    {
        return [
            $row->name,           // Nome
            $row->surname,        // Cognome
            $row->check_in ?? '-',      // Orario entrata (personalizza se serve)
            $row->check_out ?? '-',     // Orario uscita
            $row->notes ?? '',           // Note o messaggi di errore
        ];
    }
}
