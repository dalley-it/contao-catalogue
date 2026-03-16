<?php

declare(strict_types=1);

namespace DalleyIt\ContaoCatalogue\Controller\FrontendModule;

use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsFrontendModule;
use Contao\FrontendTemplate;
use Contao\ModuleModel;
use DalleyIt\ContaoCatalogue\Repository\RecordRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsFrontendModule(category: 'miscellaneous', template: 'mod_dait_catalogue_reader', type: 'dait_catalogue_reader')]
final class CatalogueReaderController extends AbstractFrontendModuleController
{
    public function __construct(private readonly RecordRepository $records)
    {
    }

    protected function getResponse(FrontendTemplate $template, ModuleModel $model, Request $request): Response
    {
        $catalogueId = (int) $model->cc_catalogue;
        $language = (string) $request->getLocale();

        // Reader identifier: ?item=<alias|id>
        $item = (string) $request->query->get('item', '');
        if ($item === '') {
            $template->record = null;
            return $template->getResponse();
        }

        $record = $this->records->findReaderRecord($catalogueId, $language, $item);
        if (!$record) {
            $template->record = null;
            return $template->getResponse();
        }

        $taxonomy = (string) $request->query->get('taxonomy', '');
        $sortMode = (string) ($model->cc_sortMode ?: 'title_asc');

        $nav = $this->records->getPrevNext(
            catalogId: $catalogueId,
            language: $language,
            taxonomy: $taxonomy,
            sortMode: $sortMode,
            currentId: (int) $record['id'],
        );

        $template->record = $record;
        $template->prev = $nav['prev'] ?? null;
        $template->next = $nav['next'] ?? null;

        // Language links (tl_news-like linking via languageMain).
        $template->translations = $this->records->getTranslations((int) $record['id']);

        // Back link: keep query params except item.
        $params = $request->query->all();
        unset($params['item']);
        $template->backParams = $params;

        return $template->getResponse();
    }
}
