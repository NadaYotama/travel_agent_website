<?php

namespace App\Enums;

enum RefundStatus: string
{
    case REQUESTED = 'requested';
    case PROCESSED = 'processed';
    case REJECTED = 'rejected';
}
