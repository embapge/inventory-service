<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Support\ItemNotFoundException;
use InvalidArgumentException;

class Handler extends ExceptionHandler
{
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
     */
    public function register(): void
    {
        $this->renderable(function (ItemNotFoundException $e, $request) {
            // if ($request->expectsJson()) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 404);
            // }
        });

        $this->renderable(function (InvalidArgumentException $e, $request) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        });

        $this->renderable(function (ForeignKeyConstraintException $e, $request) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        });

        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
