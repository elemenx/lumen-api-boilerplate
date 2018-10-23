<?php

namespace App\Essential\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Auth\AuthenticationException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthenticationException::class,
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        TokenExpiredException::class,
        TokenInvalidException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        if (env('APP_ENV') == 'production' && app()->bound('sentry') && $this->shouldReport($e)) {
            app('sentry')->captureException($e);
        }

        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        $message = trans_fb('exception.' . get_class($e), $e->getMessage());
        if (empty($message)) {
            $message = trans('exception.system_error');
        }

        $rendered = parent::render($request, $e);

        $statusCode = config('exception_code.' . get_class($e), method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500);

        $data = [
            'code'    => $statusCode,
            'message' => $message
        ];

        if (env('APP_DEBUG')) {
            $data['data'] = [
                'trace' => explode("\n", str_replace("\r\n", "\n", $e->getTraceAsString()))
            ];
        }

        if ($e instanceof ValidationException) {
            $data['code'] = 422;
            $data['data'] = [];
            foreach ($e->validator->errors()->toArray() as $field => $errors) {
                foreach ($errors as $error) {
                    $data['data'][] = [
                        'field'   => $field,
                        'content' => $error,
                    ];
                }
            }
        }

        return response()->json($data, $statusCode);
    }
}
