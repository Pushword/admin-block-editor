<?php

namespace Pushword\AdminBlockEditor\EventSuscriber;

use Pushword\AdminBlockEditor\BlockEditorFilter;
use Pushword\Core\Component\App\AppConfig;
use Pushword\Core\Component\EntityFilter\FilterEvent;
use Pushword\Core\Entity\PageInterface;
use Twig\Environment as Twig;

class EnityFilterSuscriber extends AbstractEventSuscriber
{
    /** @required */
    public Twig $twig;

    public static function getSubscribedEvents(): array
    {
        return [
            'pushword.entity_filter.before_filtering' => 'convertJsBlockToHtml',
        ];
    }

    public function convertJsBlockToHtml(FilterEvent $filterEvent): void
    {
        $page = $filterEvent->getManager()->getEntity();
        $appConfig = $this->apps->get($page->getHost());

        if (! $page instanceof PageInterface
            || 'MainContent' != $filterEvent->getProperty()
            || ! $this->mayUseEditorBlock($page)
            || true === $appConfig->get('admin_block_editor_disable_listener')) {
            return;
        }

        $this->removeMarkdownFilter($appConfig);

        $blockEditorFilter = (new BlockEditorFilter())
            ->setApp($appConfig)
            ->setEntity($page)
            ->setTwig($this->twig)
        ;

        $page->setMainContent($blockEditorFilter->apply($page->getMainContent()));
        //dump($page->getMainContent());
    }

    private function removeMarkdownFilter(AppConfig $appConfig): void
    {
        $filters = $appConfig->getFilters();
        $filters['main_content'] = str_replace(',markdown', '', $filters['main_content']);
        $appConfig->setFilters($filters);
    }
}
