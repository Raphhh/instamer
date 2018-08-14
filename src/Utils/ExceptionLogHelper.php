<?php
namespace App\Utils;

class ExceptionLogHelper
{
    /**
     * @param \Exception $e
     * @param array $context
     * @return array
     */
    public static function formatContext(\Exception $e, array $context = [])
    {
        return array_merge(
            $context,
            [
                'exception_class' => get_class($e),
                'exception_message' => $e->getMessage(),
                'exception_code' => $e->getCode(),
                'exception_file' => $e->getFile(),
                'exception_line' => $e->getLine(),
                'exception_stacktrace' => $e->getTraceAsString(),
            ]
        );
    }
}
