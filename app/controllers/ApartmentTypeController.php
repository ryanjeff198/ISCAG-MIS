<?php

require_once BASE_PATH . '/app/models/ApartmentType.php';

/**
 * ApartmentTypeController
 * JSON API endpoints for apartment type, image, and unit management.
 */
class ApartmentTypeController
{
    private ApartmentType $model;

    public function __construct()
    {
        $this->model = new ApartmentType();
    }

    /**
     * Helper: send JSON response.
     */
    private function json($data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Helper: get POST body as assoc array (supports JSON + form data).
     */
    private function getInput(): array
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (str_contains($contentType, 'application/json')) {
            return json_decode(file_get_contents('php://input'), true) ?? [];
        }
        return $_POST;
    }

    // ═══════════════════════════════════════════
    //  APARTMENT TYPES
    // ═══════════════════════════════════════════

    /**
     * GET /api/apartment-types
     * Returns all active types with thumbnail + availability.
     */
    public function listTypes(): void
    {
        $types = $this->model->getTypesForUserView();
        $this->json(['success' => true, 'data' => $types]);
    }

    /**
     * GET /api/apartment-types/detail?id=N
     * Returns single type with all images.
     */
    public function getType(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $type = $this->model->getTypeById($id);
        if (!$type) {
            $this->json(['success' => false, 'error' => 'Type not found'], 404);
        }
        $this->json(['success' => true, 'data' => $type]);
    }

    /**
     * POST /api/apartment-types/create
     */
    public function createType(): void
    {
        $input = $this->getInput();
        $required = ['type_key', 'label', 'price'];
        foreach ($required as $f) {
            if (empty($input[$f])) {
                $this->json(['success' => false, 'error' => "Missing required field: $f"], 400);
            }
        }

        try {
            $id = $this->model->createType($input);
            $this->json(['success' => true, 'data' => ['type_id' => $id]]);
        } catch (\PDOException $e) {
            $this->json(['success' => false, 'error' => 'Database error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * POST /api/apartment-types/update
     */
    public function updateType(): void
    {
        $input = $this->getInput();
        $id = (int) ($input['type_id'] ?? 0);
        if (!$id) {
            $this->json(['success' => false, 'error' => 'Missing type_id'], 400);
        }

        unset($input['type_id']);
        $ok = $this->model->updateType($id, $input);
        $this->json(['success' => $ok]);
    }

    /**
     * POST /api/apartment-types/delete
     */
    public function deleteType(): void
    {
        $input = $this->getInput();
        $id = (int) ($input['type_id'] ?? 0);
        if (!$id) {
            $this->json(['success' => false, 'error' => 'Missing type_id'], 400);
        }
        $ok = $this->model->deleteType($id);
        $this->json(['success' => $ok]);
    }

    // ═══════════════════════════════════════════
    //  IMAGES
    // ═══════════════════════════════════════════

    /**
     * POST /api/apartment-types/upload-image
     * Expects multipart/form-data with 'image' file and 'type_id'.
     */
    public function uploadImage(): void
    {
        $typeId = (int) ($_POST['type_id'] ?? 0);
        if (!$typeId) {
            $this->json(['success' => false, 'error' => 'Missing type_id'], 400);
        }

        if (empty($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            $this->json(['success' => false, 'error' => 'No valid image uploaded'], 400);
        }

        $file = $_FILES['image'];
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $allowedMimes)) {
            $this->json(['success' => false, 'error' => 'Invalid file type. Allowed: JPG, PNG, GIF, WebP'], 400);
        }

        // Ensure upload directory exists
        $uploadDir = BASE_PATH . '/public/uploads/apartments/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Generate unique filename
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = "type{$typeId}_" . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $destPath = $uploadDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            $this->json(['success' => false, 'error' => 'Failed to save uploaded file'], 500);
        }

        $relativePath = 'uploads/apartments/' . $filename;
        $caption = $_POST['caption'] ?? '';
        $isThumbnail = !empty($_POST['is_thumbnail']);

        $imageId = $this->model->addImage($typeId, $relativePath, $caption, $isThumbnail);
        $this->json(['success' => true, 'data' => [
            'image_id'  => $imageId,
            'file_path' => $relativePath,
            'caption'   => $caption
        ]]);
    }

    /**
     * POST /api/apartment-types/delete-image
     */
    public function deleteImage(): void
    {
        $input = $this->getInput();
        $imageId = (int) ($input['image_id'] ?? 0);
        if (!$imageId) {
            $this->json(['success' => false, 'error' => 'Missing image_id'], 400);
        }

        $filePath = $this->model->removeImage($imageId);
        if ($filePath) {
            // Delete the physical file if it's in uploads/ (don't delete original assets)
            if (str_starts_with($filePath, 'uploads/')) {
                $fullPath = BASE_PATH . '/public/' . $filePath;
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }
        }
        $this->json(['success' => true]);
    }

    /**
     * POST /api/apartment-types/set-thumbnail
     */
    public function setThumbnail(): void
    {
        $input = $this->getInput();
        $imageId = (int) ($input['image_id'] ?? 0);
        if (!$imageId) {
            $this->json(['success' => false, 'error' => 'Missing image_id'], 400);
        }
        $ok = $this->model->setThumbnail($imageId);
        $this->json(['success' => $ok]);
    }

    // ═══════════════════════════════════════════
    //  APARTMENT UNITS
    // ═══════════════════════════════════════════

    /**
     * GET /api/apartment-units
     */
    public function listUnits(): void
    {
        $units = $this->model->getAllUnits();
        $types = $this->model->getAllTypes();
        $this->json(['success' => true, 'data' => ['units' => $units, 'types' => $types]]);
    }

    /**
     * POST /api/apartment-units/create
     */
    public function createUnit(): void
    {
        $input = $this->getInput();
        if (empty($input['type_id']) || empty($input['room_number'])) {
            $this->json(['success' => false, 'error' => 'type_id and room_number are required'], 400);
        }
        $id = $this->model->createUnit($input);
        $this->json(['success' => true, 'data' => ['unit_id' => $id]]);
    }

    /**
     * POST /api/apartment-units/update
     */
    public function updateUnit(): void
    {
        $input = $this->getInput();
        $id = (int) ($input['unit_id'] ?? 0);
        if (!$id) {
            $this->json(['success' => false, 'error' => 'Missing unit_id'], 400);
        }
        unset($input['unit_id']);
        $ok = $this->model->updateUnit($id, $input);
        $this->json(['success' => $ok]);
    }

    /**
     * POST /api/apartment-units/delete
     */
    public function deleteUnit(): void
    {
        $input = $this->getInput();
        $id = (int) ($input['unit_id'] ?? 0);
        if (!$id) {
            $this->json(['success' => false, 'error' => 'Missing unit_id'], 400);
        }
        $ok = $this->model->deleteUnit($id);
        $this->json(['success' => $ok]);
    }
}
