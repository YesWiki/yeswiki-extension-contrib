<?php

namespace YesWiki\Test\Contrib\Actions;

use YesWiki\Content\Service\EntryManager;
use YesWiki\Content\Service\FormManager;
use YesWiki\Content\Service\PageManager;
use YesWiki\Identity\Service\AuthenticationService;
use YesWiki\Render\Service\ActionRunner;
use YesWiki\Test\Core\YesWikiTestCase;

require_once 'tests/YesWikiTestCase.php';

/** The contrib actions, run the way a page runs them. */
class ContribActionsTest extends YesWikiTestCase
{
    private string $formId = '';

    /** @var list<string> */
    private array $entries = [];

    protected function setUp(): void
    {
        parent::setUp();
        if (!isset($this->getWiki()->services->get(\YesWiki\Kernel\Service\ExtensionRegistry::class)->all()['contrib'])) {
            $this->markTestSkipped('contrib is not switched on in this wiki');
        }
    }

    protected function tearDown(): void
    {
        $services = $this->getWiki()->services;
        foreach ($this->entries as $tag) {
            $services->get(PageManager::class)->deleteOrphaned($tag);
        }
        if ($this->formId !== '') {
            $services->get(FormManager::class)->delete($this->formId);
        }
        $services->get(AuthenticationService::class)->logout();
        parent::tearDown();
    }

    public function testTheFormShowsUntilTheLimitThenTheMessageDoes(): void
    {
        $services = $this->getWiki()->services;
        $services->get(AuthenticationService::class)->connectFirstAdmin();
        $forms = $services->get(FormManager::class);
        $this->formId = (string)$forms->findNewId();
        $forms->create([
            'id' => $this->formId,
            'label' => 'Contrib limit test',
            'template' => [['type' => 'texte', 'name' => 'bf_titre', 'label' => 'Titre']],
            'entry_title_template' => '{{bf_titre}}',
        ]);
        $this->assertNotSame('', $this->formId);
        $runner = $services->get(ActionRunner::class);

        $open = $runner->action('limitentries', ['id' => $this->formId, 'limit' => '1', 'message_count' => 'Inscrits : %{nb} sur %{limit}']);
        $this->assertStringContainsString('Inscrits : 0 sur 1', $open);
        $this->assertStringContainsString('<form', $open);

        $entry = $services->get(EntryManager::class)->create($this->formId, ['bf_titre' => 'Contrib limit one', 'antispam' => 1], false);
        $this->entries[] = (string)$entry['tag'];

        $full = $runner->action('limitentries', ['id' => $this->formId, 'limit' => '1', 'message_max' => 'Complet (%{limit})']);
        $this->assertStringContainsString('Complet (1)', $full);
        $this->assertStringNotContainsString('<form', $full);
    }

    public function testAMissingParameterSaysWhichOne(): void
    {
        $this->assertStringContainsString('yw-alert--danger', $this->getWiki()->services->get(ActionRunner::class)->action('limitentries', ['limit' => '3']));
    }

    public function testAnAudioFileThatIsNotThereIsSaidToBeMissing(): void
    {
        $html = $this->getWiki()->services->get(ActionRunner::class)->action('sonogramme', ['file' => 'nowhere-to-be-found.mp3']);

        $this->assertStringContainsString('yw-alert--danger', $html);
        $this->assertStringContainsString('nowhere-to-be-found.mp3', $html);
    }
}
