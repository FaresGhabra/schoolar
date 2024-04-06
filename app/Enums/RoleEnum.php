<?php

namespace App\Enums;

enum RoleEnum : int {
    case OWNER = 1;
    case ADMIN = 2;
    case PROMPT = 3;
    case TEACHER = 4;
    case PARENT = 5;
    case STUDENT = 6;
    case ONLINE_STUDENT = 7;
}