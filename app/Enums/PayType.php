<?php

namespace App\Enums;

enum PayType: string
{
    case DownPayment = 'down_payment';
    case QuarterPer = 'quarter_per';
    case HalfPer = 'half_per';
    case YearPer = 'year_per';
    case Installment = 'installment';
}
