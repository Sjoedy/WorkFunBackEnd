<?php

namespace App\Exceptions;

use App\Traits\ResponseTrait;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use League\OAuth2\Server\Exception\OAuthServerException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ResponseTrait;
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
            if ($e instanceof OAuthServerException) {
                return $this->error($e->getHint(), $e->getHttpStatusCode());
            }
            /************ *************/

            if ($e instanceof NotFoundHttpException) {
                if ($e->getMessage())
                    return $this->error($e->getMessage(), $e->getStatusCode());
                return $this->error(__('fail.route_not_found'), $e->getStatusCode());
            }

            if ($e instanceof AccessDeniedHttpException) {
                return $this->error(__('fail.no_permission'), $e->getStatusCode());
            }

            if ($e instanceof HttpException) {
                return $this->error($e->getMessage(), $e->getStatusCode());
            }
            return $e;
        });
    }
}
