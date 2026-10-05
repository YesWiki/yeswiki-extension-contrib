<?php

namespace YesWiki\Contrib\Action;

use YesWiki\Content\Service\FileManager;
use YesWiki\Core\YesWikiAction;
use YesWiki\Files\Service\AttachedFilePaths;
use YesWiki\Files\Service\Storage;
use YesWiki\Kernel\Component\Category;
use YesWiki\Kernel\Component\Component;
use YesWiki\Kernel\Component\ProvidesComponents;
use YesWiki\Kernel\Component\Setting;
use YesWiki\Kernel\Performable\RegisteredAction;
use YesWiki\Kernel\Service\UrlFormatter;

/** `{{sonogramme file="interview.mp3"}}`: an attached audio file drawn as its waveform, with a player. */
class SonogrammeAction extends YesWikiAction implements RegisteredAction, ProvidesComponents
{
    public static function performableName(): string
    {
        return 'sonogramme';
    }

    public function components(): array
    {
        return [
            Component::for('sonogramme')
                ->category(Category::Media)
                ->label(_t('CONTRIB_SONOGRAMME_LABEL'))
                ->description(_t('CONTRIB_SONOGRAMME_DESCRIPTION'))
                ->icon('microphone-2')
                ->settings(
                    Setting::text('file')->label(_t('CONTRIB_SONOGRAMME_FILE'))->hint(_t('CONTRIB_SONOGRAMME_FILE_HINT'))->required(),
                    Setting::number('height')->label(_t('CONTRIB_SONOGRAMME_HEIGHT'))->hint(_t('CONTRIB_SONOGRAMME_HEIGHT_HINT'))->default(200),
                    Setting::color('color')->label(_t('CONTRIB_SONOGRAMME_COLOR'))->hint(_t('CONTRIB_SONOGRAMME_COLOR_HINT')),
                ),
        ];
    }

    public function formatArguments($arg): array
    {
        $height = (int)($arg['height'] ?? 200);

        return [
            'file' => trim((string)($arg['file'] ?? '')),
            'height' => $height > 0 ? $height : 200,
            'color' => (string)($arg['color'] ?? ''),
        ];
    }

    public function run(): string
    {
        $url = $this->audioUrl($this->arguments['file']);
        if ($url === null) {
            return '<div class="yw-alert yw-alert--danger">' . _t('CONTRIB_SONOGRAMME_NOT_FOUND')
                . ' (' . htmlspecialchars($this->arguments['file']) . ')</div>';
        }

        return $this->render('@contrib/sonogramme.twig', [
            'args' => $this->arguments,
            'url' => $url,
        ]);
    }

    /** Where the file's bytes are served, resolved the way `{{attach}}` resolves it: a File Content tag first, then a legacy upload. */
    private function audioUrl(string $file): ?string
    {
        if ($file === '') {
            return null;
        }
        if ($this->getService(FileManager::class)->getOne($file) !== null) {
            return $this->getService(UrlFormatter::class)->href('', 'api/files/' . $file . '/download', [], false);
        }
        $path = $this->getService(AttachedFilePaths::class)->fullFilename($file);
        $storage = $this->getService(Storage::class);

        return $path !== '' && $storage->exists($path) ? $storage->url($path) : null;
    }
}
