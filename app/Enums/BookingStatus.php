<?php

namespace App\Enums;

enum BookingStatus: int
{
    case UNASSIGNED = 0;
    case RESERVED = 1;
    case BOOKED = 2;
    case PENDING_APPROVAL = 3;
}
