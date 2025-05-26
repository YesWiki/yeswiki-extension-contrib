<?php

use YesWiki\Bazar\Service\EntryManager;
use YesWiki\Core\YesWikiAction;

class LimitEntriesAction extends YesWikiAction
{
    public function formatArguments($arg): array
    {
        $id = $arg['id'] ?? null;
        if (empty($id)) {
            throw new Exception('Action limitentries, le paramètre id (numéro du formulaire) est obligatoire');
        }
        $limit = (int)$arg['limit'] ?? null;
        if (empty($limit) || $limit <= 0) {
            throw new Exception('Action limitentries, le paramètre limit est obligatoire et doit etre supérieur à zéro');
        }

        return [
            'id' => $id,
            'limit' => $limit,
            'message_max' => $arg['message_max'] ?? 'Nombre maximum de saisies atteint (%{limit}), il n\'est plus possible d\'en ajouter..',
            'message_count' => $arg['message_count'] ?? 'Nombres de fiches saisies: %{nb} sur %{limit}.',
        ];
    }

    public function run()
    {
        // dirty hack to be able to use %{limit} syntax in translated strings even if custom string are used
        $msgCount = $this->arguments['message_count'];
        if (!empty($msgCount)) {
            $GLOBALS['translations'][$msgCount] = $msgCount;
        }
        $msgMax = $this->arguments['message_max'];
        if (!empty($msgMax)) {
            $GLOBALS['translations'][$msgMax] = $msgMax;
        }
        $entryManager = $this->getService(EntryManager::class);
        $nbEntries = count($entryManager->search(['formsIds' => [$this->arguments['id']]]));

        dump($nbEntries, $this->arguments['limit']);
        if ($nbEntries >= $this->arguments['limit']) {
            return '<div class="alert alert-warning">' . _t($msgMax, ['limit' => $this->arguments['limit']]) . '</div>';
        } else {
            $output = !empty($msgCount) ? '<div class="alert alert-info">' . _t($msgCount, ['limit' => $this->arguments['limit'], 'nb' => $nbEntries]) . '</div>' : '';
            $output .= $this->wiki->Format('{{bazar vue="saisir" id="' . $this->arguments['id'] . '" voirmenu="0"}}');

            return $output;
        }
    }
}
