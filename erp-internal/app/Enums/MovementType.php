<?php

namespace App\Enums;

enum MovementType: string
{
    case InboundPurchase = 'inbound_purchase';
    case CleaningConversion = 'cleaning_conversion';
    case ProductionConsumption = 'production_consumption';
    case ProductionOutput = 'production_output';
    case WarehouseTransfer = 'warehouse_transfer';
    case BranchTransfer = 'branch_transfer';
    case BranchReceive = 'branch_receive';
    case BranchSale = 'branch_sale';
    case StockAdjustment = 'stock_adjustment';
    case WasteShrinkage = 'waste_shrinkage';
    case StockOpname = 'stock_opname';

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return [
            self::InboundPurchase->value => 'Inbound Purchase',
            self::CleaningConversion->value => 'Cleaning / Sorting',
            self::ProductionConsumption->value => 'Production Consumption',
            self::ProductionOutput->value => 'Production Output',
            self::WarehouseTransfer->value => 'Warehouse Transfer',
            self::BranchTransfer->value => 'Branch Transfer',
            self::BranchReceive->value => 'Branch Receive',
            self::BranchSale->value => 'Branch Sale',
            self::StockAdjustment->value => 'Stock Adjustment',
            self::WasteShrinkage->value => 'Waste / Shrinkage',
            self::StockOpname->value => 'Stock Opname',
        ];
    }
}
