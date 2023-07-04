<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use App\Helpers\JsonFormatter;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function handleException($request, Throwable $exception)
    {

        if ($exception instanceof AuthenticationException) {
            return JsonFormatter::error(
                null,
                'Unauthenticated',
                401
            );
        }

        if ($exception instanceof MethodNotAllowedHttpException) {
            return JsonFormatter::error(
                null,
                'The specified method for the request is invalid',
                405
            );
        }

        if ($exception instanceof NotFoundHttpException) {
            return JsonFormatter::error(
                null,
                'The specified URL cannot be found',
                404
            );
        }

        if ($exception instanceof HttpException) {
            DB::rollback();
            return JsonFormatter::error(
                null,
                $exception->getMessage(),
                $exception->getStatusCode()
            );
        }

        DB::rollback();
        return JsonFormatter::error(
            null,
            $exception->getMessage(),
            // 'Something went wrong. Try later',
            500
        );

    }
}
