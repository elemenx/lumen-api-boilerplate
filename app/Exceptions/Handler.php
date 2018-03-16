<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
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
        if (in_array(env('APP_ENV'), ['production', 'testing']) && app()->bound('sentry') && $this->shouldReport($e)) {
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

        $statusCode = method_exists($e, 'getStatusCode')
            ? $e->getStatusCode()
            : 500;

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
            $data['data'] = [
                'fields' => $e->validator->errors()
            ];
        }

        return response()->json($data, $statusCode);
    }
}
