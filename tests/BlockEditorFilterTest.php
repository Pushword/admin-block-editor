<?php

declare(strict_types=1);

namespace Pushword\AdminBlockEditor\Tests;

use Pushword\AdminBlockEditor\BlockEditorFilter;
use Pushword\Core\Entity\Page;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BlockEditorFilterTest extends KernelTestCase
{
    public function testIt()
    {
        $filter = $this->getEditorFilterTest();
        $mainContentFiltered = $filter->apply($filter->page->getMainContent());

        $this->assertStringContainsString('</div>', $mainContentFiltered);
        $this->assertStringContainsString('&test&', $mainContentFiltered);
    }

    private function getEditorFilterTest()
    {
        self::bootKernel();
        $filter = new BlockEditorFilter();
        $filter->app = self::$kernel->getContainer()->get(\Pushword\Core\Component\App\AppPool::class)->get();
        $filter->twig = static::getContainer()->get('test.service_container')->get('twig');
        $filter->page = $this->getPage();

        return $filter;
    }

    private function getPage($content = null)
    {
        $page = (new Page())
                ->setH1('Demo Page - Kitchen Sink  Markdown + Twig')
                ->setSlug('kitchen-sink')
                ->setLocale('en')
                ->setCreatedAt(new \DateTime('1 day ago'))
                ->setUpdatedAt(new \DateTime('1 day ago'))
                ->setMainContent(file_get_contents(__DIR__.'/content/content.json'));

        $page->setCustomProperty('toc', true);

        return $page;
    }
}
