<?php

declare(strict_types=1);

namespace DalleyIt\ContaoCatalogue\Controller\FrontendModule;

use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsFrontendModule;
use Contao\FrontendTemplate;
use Contao\ModuleModel;
use Contao\PageModel;
use DalleyIt\ContaoCatalogue\Dictionary\DictionaryProvider;
use DalleyIt\ContaoCatalogue\Repository\RecordRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsFrontendModule(category: 'miscellaneous', template: 'mod_dait_catalogue_list', type: 'dait_catalogue_list')]
final class CatalogueListController extends AbstractFrontendModuleController
{
    public function __construct(
        private readonly RecordRepository $records,
        private readonly DictionaryProvider $dictionaryProvider,
    ) {
    }

    protected function getResponse(FrontendTemplate $template, ModuleModel $model, Request $request): Response
    {
        $catalogueId = (int) $model->cc_catalogue;

        $taxonomy = (string) $request->query->get('taxonomy', '');
        $page = max(1, (int) $request->query->get('page', 1));
        $perPage = (int) ($model->cc_perPage ?: 0);
        $sortMode = (string) ($model->cc_sortMode ?: 'title_asc');

        // Use the request locale as language key (e.g. "de", "en").
        $language = (string) $request->getLocale();

        $enableTaxonomyFilter = ((string) ($model->cc_enableTaxonomyFilter ?? '1')) === '1';
        $taxonomyFilter = $enableTaxonomyFilter ? $taxonomy : '';

        $result = $this->records->findList(
            catalogId: $catalogueId,
            language: $language,
            taxonomy: $taxonomyFilter,
            page: $page,
            perPage: $perPage,
            sortMode: $sortMode,
        );

        $template->items = $result['items'];
        $template->total = $result['total'];
        $template->page = $page;
        $template->perPage = $perPage;
        $template->taxonomy = $taxonomy;

        // Taxonomy options are provided via the catalogue dictionary key (optional).
        $dictKey = $this->records->getCatalogDictionaryKey($catalogueId);
        $template->taxonomyOptions = $dictKey !== '' ? $this->dictionaryProvider->getOptions($dictKey, $language) : [];

        // Reader base URL (jumpTo from catalogue).
        $jumpTo = $this->records->getCatalogJumpToPageId($catalogueId);
        $template->readerPage = $jumpTo ? PageModel::findById($jumpTo) : null;

        return $template->getResponse();
    }
}
