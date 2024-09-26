<?php

declare(strict_types=1);

namespace ZipStream;

enum CompressionMethod: int
{
    case STORE = 0x00;

    case DEFLATE = 0x08;

}
