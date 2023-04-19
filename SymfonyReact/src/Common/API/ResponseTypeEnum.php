<?php

namespace App\Common\API;

enum ResponseTypeEnum {
    case FULL;
    case PARTIAL;
    case OBJECT_ONLY;
    case SENSITIVE;
}
