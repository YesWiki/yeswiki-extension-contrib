<?php

namespace YesWiki\Contrib\Controller;

use Symfony\Component\Routing\Annotation\Route;
use YesWiki\Core\ApiResponse;
use YesWiki\Core\YesWikiController;

class ApiController extends YesWikiController
{
    /**
     * @Route("/api/images/resize/{file}", methods={"GET"}, options={"acl":{"public"}},priority=2)
     */
    public function resizeImg($file)
    {
        if (!file_exists('files/' . $file)) {
            return new ApiResponse(['error' => 'File ' . $file . ' not found'], 400);
        }
        $file_extension = strtolower(substr(strrchr($file, "."), 1));
        switch ($file_extension) {
            case "webp":
                $ctype = "image/webp";
                break;
            case "gif":
                $ctype = "image/gif";
                break;
            case "png":
                $ctype = "image/png";
                break;
            case "jpeg":
            case "jpg":
                $ctype = "image/jpeg";
                break;
            default:
                return new ApiResponse(['error', 'file should be a image (webp, gif, png, jpg, jpeg)'], 400);
        }

        if (empty($_GET['w']) || empty($_GET['h']) || empty($_GET['fit'])) {
            return new ApiResponse(['error' => 'GET param for width (w) or height (h) or fit (fit), not found'], 400);
        }

        $server = \League\Glide\ServerFactory::create([
            'source' => 'files',
            'cache' => 'cache',
            'max_image_size' => 2000 * 2000,
            'defaults' => [
                'fm' => 'webp',
                'q' => 70
            ],
        ]);

        // only admins can resize, otherwise the original image will be sent
        if ($this->wiki->UserIsAdmin()) {
            if (!empty($_GET['refresh'])) {
                $server->deleteCache($file);
            }
            $server->outputImage($file, $_GET);
            exit;
        } else {
            header('Content-type: ' . $ctype);
            // if a reduced image exists, we use it, otherwise we use original size
            if ($server->cacheFileExists($file, $_GET)) {
                $server->outputImage($file, $_GET);
            } else {
                readfile('files/' . $file);
            }
            exit;
        }
    }
}
