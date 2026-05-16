<?php
namespace App\Http\Middleware;use Closure;use Illuminate\Http\Request;class EnsureRole{public function handle(Request $r,Closure $n,string ...$roles){if(!$r->user()||!in_array($r->user()->role,$roles,true))abort(403);return $n($r);}}
