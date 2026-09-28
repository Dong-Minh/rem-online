<?php

$caPath = getenv('MYSQL_ATTR_SSL_CA');

if (!$caPath) {
    exit(0);
}

if (!file_exists($caPath) || !is_readable($caPath)) {
    fwrite(STDERR, "Error: CA certificate file not found or unreadable at '$caPath'\n");
    exit(1);
}

$content = file_get_contents($caPath);

if (!str_contains($content, 'BEGIN CERTIFICATE') || !str_contains($content, 'END CERTIFICATE')) {
    fwrite(STDERR, "Error: CA certificate file at '$caPath' is not a valid PEM certificate.\n");
    exit(1);
}

fwrite(STDOUT, "MySQL CA certificate verified successfully.\n");
exit(0);
