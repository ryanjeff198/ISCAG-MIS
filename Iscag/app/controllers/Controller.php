<?php

/**
 * Base Controller
 * All controllers extend this class to inherit the view() helper.
 */
class Controller
{
    /**
     * Render a view file.
     *
     * @param string $view  Dot-or-slash-separated path relative to app/views/
     *                      e.g. 'auth/login' loads app/views/auth/login.php
     * @param array  $data  Associative array of variables to extract into the view scope
     */
    protected function view(string $view, array $data = []): void
    {
        // Convert dot notation to directory separators
        $view = str_replace('.', DIRECTORY_SEPARATOR, $view);
        $file = BASE_PATH . '/app/views/' . $view . '.php';

        if (!file_exists($file)) {
            http_response_code(404);
            echo "View [{$view}] not found.";
            return;
        }

        // Extract data so variables are available in the view
        extract($data);

        require $file;
    }
}
