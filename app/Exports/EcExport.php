<?php

namespace App\Exports;

use App\Models\Ec;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class EcExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        return Ec::with('ue')->get()->map(function ($ec) {
            return [
                $ec->code_ec,
                $ec->label_ec,
                $ec->desc_ec,
                $ec->ue->label_ue ?? 'N/A',
                $ec->created_at,
                $ec->updated_at,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Code EC',
            'Label',
            'Description',
            'Unité d\'Enseignement',
            'Créé le',
            'Modifié le'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style pour l'en-tête (ligne 1)
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2ECC71'], // Vert moderne
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'border' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '2ECC71'],
                ],
            ],
        ];

        // Appliquer le style à l'en-tête
        $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Style pour les lignes de données alternées
        $lastRow = $sheet->getHighestRow();

        for ($i = 2; $i <= $lastRow; $i++) {
            $rowStyle = [
                'font' => [
                    'color' => ['rgb' => '333333'],
                    'size' => 10,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'border' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC'],
                    ],
                ],
            ];

            // Alternance de couleurs (vert clair)
            if ($i % 2 == 0) {
                $rowStyle['fill'] = [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E6F5EB'],
                ];
            } else {
                $rowStyle['fill'] = [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFFFFF'],
                ];
            }

            $sheet->getStyle("A{$i}:F{$i}")->applyFromArray($rowStyle);
            $sheet->getRowDimension($i)->setRowHeight(18);
        }

        return [];
    }

    /**
     * Définir la largeur des colonnes
     */
    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 25,
            'C' => 40,
            'D' => 30,
            'E' => 18,
            'F' => 18,
        ];
    }
}
