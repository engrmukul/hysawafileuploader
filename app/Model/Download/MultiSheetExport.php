<?php

namespace App\Model\Download;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MultiSheetExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        $ordersSheet = new OrdersSheetExport;
        $ordersSheet->prepareRowMap();

        $dressSheet = new DressSheetExport($ordersSheet->getOrderRowMap());
        $dressSheet->prepareRowMap();

        $electronicsSheet = new ElectronicsSheetExport($ordersSheet->getOrderRowMap());
        $electronicsSheet->prepareRowMap();

        $kidsSheet = new KidsSheetExport($ordersSheet->getOrderRowMap());
        $kidsSheet->prepareRowMap();

        $foodSheet = new FoodSheetExport($ordersSheet->getOrderRowMap());
        $foodSheet->prepareRowMap();

        $medicineSheet = new MedicineSheetExport($ordersSheet->getOrderRowMap());
        $medicineSheet->prepareRowMap();

        $usersSheet = new UsersSheetExport($ordersSheet->getFirstOrderRows());
        $usersSheet->prepareRowMap();

        $ordersSheet->setCategorySheets([
            'Dress' => $dressSheet,
            'Electronics' => $electronicsSheet,
            'Kids' => $kidsSheet,
            'Food' => $foodSheet,
            'Medicine' => $medicineSheet,
            'Users' => $usersSheet,
        ]);

        return [
            $usersSheet,
            $ordersSheet,
            $dressSheet,
            $electronicsSheet,
            $kidsSheet,
            $foodSheet,
            $medicineSheet,
        ];
    }
}

