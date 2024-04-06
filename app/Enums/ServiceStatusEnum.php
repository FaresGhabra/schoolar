<?php

namespace App\Enums;

enum ServiceStatus: string
{
    case ACTIVATED = 'activated';
    case ONHOLD = 'ohhold';
    case UNACTIVE = 'unactive';
    case EXPIRED = 'expired';
}