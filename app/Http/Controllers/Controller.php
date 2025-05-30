<?php

namespace App\Http\Controllers;
use App\Http\Traits\ArchiveTrait;
use App\Http\Traits\ResponsesTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, ResponsesTrait, ValidatesRequests, ArchiveTrait;
}
