<?php

function ensureUploadDirectory(string $absolutePath): bool
{
    if (is_dir($absolutePath)) {
        return true;
    }

    return mkdir($absolutePath, 0777, true);
}

function uploadFileFromField(string $field, string $absoluteDir, string $relativeDir, array $allowedExtensions, int $maxSizeBytes, string $prefix, string &$errorMessage): ?array
{
    if (!isset($_FILES[$field]) || !is_array($_FILES[$field])) {
        $errorMessage = 'Upload field "' . $field . '" is missing.';
        return null;
    }

    $file = $_FILES[$field];

    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        $errorMessage = 'Failed to upload ' . str_replace('_', ' ', $field) . '.';
        return null;
    }

    $originalName = basename((string) ($file['name'] ?? ''));
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    if ($originalName === '' || !in_array($extension, $allowedExtensions, true)) {
        $errorMessage = 'Invalid file type for ' . str_replace('_', ' ', $field) . '.';
        return null;
    }

    $size = (int) ($file['size'] ?? 0);
    if ($size <= 0 || $size > $maxSizeBytes) {
        $errorMessage = str_replace('_', ' ', ucfirst($field)) . ' exceeds the size limit.';
        return null;
    }

    if (!ensureUploadDirectory($absoluteDir)) {
        $errorMessage = 'Unable to create upload directory.';
        return null;
    }

    $safeName = preg_replace('/[^A-Za-z0-9._-]/', '_', $originalName);
    $targetName = $prefix . '_' . $field . '_' . time() . '_' . $safeName;
    $absolutePath = rtrim($absoluteDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $targetName;

    if (!move_uploaded_file((string) $file['tmp_name'], $absolutePath)) {
        $errorMessage = 'Unable to save uploaded ' . str_replace('_', ' ', $field) . '.';
        return null;
    }

    return [
        'original_name' => $originalName,
        'relative_path' => trim($relativeDir, '/\\') . '/' . $targetName,
        'absolute_path' => $absolutePath,
        'mime_type' => (string) (($file['type'] ?? '') !== '' ? $file['type'] : strtoupper($extension)),
        'extension' => $extension,
    ];
}

