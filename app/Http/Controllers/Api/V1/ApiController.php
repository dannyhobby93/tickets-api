<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponses;
use Illuminate\Support\Facades\Gate;

class ApiController extends Controller
{
    use ApiResponses;

    protected string $policy_class;

    public function __construct()
    {
        Gate::guessPolicyNamesUsing(function () {
            return $this->policy_class;
        });
    }

    public function include(string $relationship): bool
    {
        $param = request()->get('include');

        if (!isset($param)) {
            return false;
        }

        $include_values = explode(',', strtolower($param));

        return in_array(strtolower($relationship), $include_values);
    }
}
