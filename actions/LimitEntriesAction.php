<?php

namespace YesWiki\Contrib\Action;

use YesWiki\Content\Service\EntryManager;
use YesWiki\Core\YesWikiAction;
use YesWiki\Kernel\Component\Category;
use YesWiki\Kernel\Component\Component;
use YesWiki\Kernel\Component\ProvidesComponents;
use YesWiki\Kernel\Component\Setting;
use YesWiki\Kernel\Performable\RegisteredAction;
use YesWiki\Render\Service\ActionRunner;

/** `{{limitentries id="2" limit="30"}}`: the form's entry form, until it holds that many entries. */
class LimitEntriesAction extends YesWikiAction implements RegisteredAction, ProvidesComponents
{
    public static function performableName(): string
    {
        return 'limitentries';
    }

    public function components(): array
    {
        return [
            Component::for('limitentries')
                ->category(Category::Forms)
                ->label(_t('CONTRIB_LIMITENTRIES_LABEL'))
                ->description(_t('CONTRIB_LIMITENTRIES_DESCRIPTION'))
                ->icon('list-numbers')
                ->settings(
                    Setting::form('id')->label(_t('CONTRIB_LIMITENTRIES_ID'))->required(),
                    Setting::number('limit')->label(_t('CONTRIB_LIMITENTRIES_LIMIT'))->required(),
                    Setting::text('message_max')->label(_t('CONTRIB_LIMITENTRIES_MESSAGE_MAX'))->hint(_t('CONTRIB_LIMITENTRIES_PLACEHOLDERS_HINT')),
                    Setting::text('message_count')->label(_t('CONTRIB_LIMITENTRIES_MESSAGE_COUNT'))->hint(_t('CONTRIB_LIMITENTRIES_PLACEHOLDERS_HINT')),
                ),
        ];
    }

    public function formatArguments($arg): array
    {
        return [
            'id' => trim((string)($arg['id'] ?? '')),
            'limit' => (int)($arg['limit'] ?? 0),
            'message_max' => (string)($arg['message_max'] ?? _t('CONTRIB_LIMITENTRIES_DEFAULT_MAX')),
            'message_count' => (string)($arg['message_count'] ?? _t('CONTRIB_LIMITENTRIES_DEFAULT_COUNT')),
        ];
    }

    public function run(): string
    {
        if ($this->arguments['id'] === '') {
            return $this->alert('danger', 'limitentries : ' . _t('CONTRIB_LIMITENTRIES_ID_REQUIRED'));
        }
        if ($this->arguments['limit'] <= 0) {
            return $this->alert('danger', 'limitentries : ' . _t('CONTRIB_LIMITENTRIES_LIMIT_REQUIRED'));
        }

        $count = count($this->getService(EntryManager::class)->search(['formsIds' => [$this->arguments['id']]]));
        $placeholders = ['%{limit}' => (string)$this->arguments['limit'], '%{nb}' => (string)$count];

        if ($count >= $this->arguments['limit']) {
            return $this->alert('warning', strtr($this->arguments['message_max'], $placeholders));
        }

        $countMessage = $this->arguments['message_count'] === '' ? '' : $this->alert('info', strtr($this->arguments['message_count'], $placeholders));

        return $countMessage . $this->getService(ActionRunner::class)->action('bazar', [
            'view' => 'saisir',
            'id' => $this->arguments['id'],
            'showmenu' => '0',
        ]);
    }

    private function alert(string $kind, string $message): string
    {
        return '<div class="yw-alert yw-alert--' . $kind . '">' . htmlspecialchars($message) . '</div>';
    }
}
