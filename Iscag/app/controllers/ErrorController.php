<?php

/**
 * Error Helper
 * Provides standard error views.
 */
class ErrorController
{
  public static function show404(): void
  {
    http_response_code(404);
    echo '<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>404 – Page Not Found</title>
<style>
  body { font-family: "Inter", sans-serif; display: flex; align-items: center;
         justify-content: center; min-height: 100vh; background: #f5f5f5; color: #111; }
  .box  { text-align: center; }
  h1    { font-size: 72px; color: #1c6b3a; margin: 0; }
  p     { color: #555; margin-top: 8px; }
  a     { color: #1c6b3a; text-decoration: none; font-weight: 600; }
  a:hover { text-decoration: underline; }
</style></head>
<body><div class="box">
  <h1>404</h1>
  <p>The page you are looking for does not exist.</p>
  <a href="/">← Back to Home</a>
</div></body></html>';
    exit;
  }
}
