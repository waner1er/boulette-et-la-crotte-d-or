<?php

declare(strict_types=1);

namespace Boulette\Scene;

/** Les cinq zones du fast-food MÉGA MIAM, quatre niveaux chacune. */
enum Zone: string
{
    case Parking = 'parking';
    case Dining = 'dining';
    case Kitchen = 'kitchen';
    case Freezer = 'freezer';
    case Lab = 'lab';
}
