<?php

return [
    'system_error'                                                         => '系统错误',
    'Symfony\Component\HttpKernel\Exception\NotFoundHttpException'         => '路径不存在',
    'Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException' => '请求方式错误',
    'Illuminate\Validation\ValidationException'                            => '表单验证错误',
    'Illuminate\Database\Eloquent\ModelNotFoundException'                  => '对象不存在',
    'Tymon\JWTAuth\Exceptions\TokenExpiredException'                       => '鉴权已过期',
    'Tymon\JWTAuth\Exceptions\TokenInvalidException'                       => '鉴权已失效',
    'Illuminate\Auth\Access\AuthorizationException'                        => '鉴权错误',
    'Illuminate\Auth\AuthenticationException'                              => '鉴权已失效',
];
