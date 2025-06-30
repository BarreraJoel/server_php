<?php

namespace App\Enums;

enum CrudEnum : string
{
    case SELECT = 'SELECT';
    case INSERT = 'INSERT INTO';
    case UPDATE = 'UPDATE';
    case DELETE = 'DELETE FROM';
}
