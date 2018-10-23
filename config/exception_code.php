<?php

return [
    'Illuminate\Auth\AuthenticationException'             => 401,
    'Illuminate\Auth\AuthorizationException'              => 401,
    'Illuminate\Auth\Access\AuthorizationException'       => 401,
    'Tymon\JWTAuth\Exceptions\TokenExpiredException'      => 401,
    'Tymon\JWTAuth\Exceptions\TokenInvalidException'      => 401,
    'Illuminate\Database\Eloquent\ModelNotFoundException' => 404,
    'Illuminate\Validation\ValidationException'           => 422
];
