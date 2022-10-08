<?php

namespace Zaoob\Laravel\Team\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;

class ZaoobTeamMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $permission)
    {
        if (!$request->hasMacro('modelTeamable')) {
            throw new Exception('Request not has macro `modelTeamable()`.');
        }

        $member = $request->modelTeamable()->getMembers->where('member_id', $request->user()->id)->first();

        if (!$member) {
            abort(404);
        }

        $rules = config('zaoob.team.rules.' . $member->rule);

        if ($member->rule != '*' && !in_array('zaoobTeam:' . $permission, $rules)) {
            abort(403, 'You do not have permission to it');
        }

        return $next($request);
    }
}
