<?php

namespace Octo\Pages\Admin\Controller;

use b8\Form;
use b8\Http\Response\RedirectResponse;
use Octo\Admin\Controller;
use Octo\Admin\Form as FormElement;
use Octo\Admin\Template as AdminTemplate;
use Octo\Admin\Menu;
use Octo\Block;
use Octo\Event;
use Octo\Form\Element\DateOfBirth;
use Octo\Pages\Model\Page;
use Octo\Pages\Model\PageVersion;
use Octo\Store;
use Octo\System\Model\ContentItem;
use Octo\System\Model\Log;
use Octo\Html\Template;

class PageController extends Controller
{
    /**
     * @var \Octo\Pages\Store\PageStore
     */
    protected $pageStore;

    /**
     * @var \Octo\Pages\Store\PageVersionStore
     */
    protected $versionStore;

    /**
     * @var \Octo\System\Store\ContentItemStore
     */
    protected $contentStore;

    public static function registerMenus(Menu $menu)
    {
        $pages = $menu->addRoot('Pages', '/page')->setIcon('sitemap');
        $pages->addChild(new Menu\Item('Search', '/page/autocomplete', true));
        $pages->addChild(new Menu\Item('Create Homepage', '/page/create-homepage', true));
        $pages->addChild(new Menu\Item('Add Page', '/page/add', true));
        $pages->addChild(new Menu\Item('Edit Page', '/page/edit', true));
        $pages->addChild(new Menu\Item('Edit Page (Autosave)', '/page/edit-ping', true));
        $pages->addChild(new Menu\Item('Duplicate Page', '/page/duplicate', true));
        $pages->addChild(new Menu\Item('Delete Page', '/page/delete', true));
        $pages->addChild(new Menu\Item('Save Page', '/page/save', true));
        $pages->addChild(new Menu\Item('Publish Page', '/page/publish', true));
        $pages->addChild(new Menu\Item('Page Metadata', '/page/metadata', true));
        $pages->addChild(new Menu\Item('Sort Pages', '/page/sort', true));
    }

    public function init()
    {
        $this->pageStore = Store::get('Page');
        $this->versionStore = Store::get('PageVersion');
        $this->contentStore = Store::get('ContentItem');

        $this->addBreadcrumb('Pages', '/page');
    }

    public function index()
    {
        $this->setTitle('Manage Pages');

        $parentId = $this->getParam('parent', null);

        $types = [];
        foreach (Store::get('ContentType')->all() as $type) {
            $thisTypes = [];

            foreach ($type->getAllowedChildTypes() as $childType) {
                $thisTypes[$childType->getId()] = ['name' => $childType->getName(), 'icon' => $childType->getIcon()];
            }

            $types[$type->getId()] = $thisTypes;
        }

        $this->view->types = $types;

        if (is_null($parentId)) {
            $parent = $this->pageStore->getHomepage();

            if (is_null($parent)) {
                $this->successMessage('Create your first page using the form below.', true);

                $this->response = new RedirectResponse();
                $this->response->setHeader('Location', '/'.$this->config->get('site.admin_uri').'/page/add');
                return;
            }

            $pages = [$parent];
            $this->view->pages = $pages;
            $this->view->open = $this->getParam('open', []);
        } else {
            $pages = $this->pageStore->getByParentId($parentId, ['order' => [['position', 'ASC']]]);

            $list = AdminTemplate::getAdminTemplate('Page/list');
            $list->pages = $pages;

            die($list->render());
        }
    }

    public function add($type = null, $parentId = null)
    {
        if ($this->request->getMethod() == 'POST') {
            return $this->createPage();
        }

        $this->setTitle('Add Page');
        $this->addBreadcrumb('Add Page', '/page/add');


        $form = $this->getPageDetailsForm('add', (is_null($type) && is_null($parentId)));

        if ($form) {
            $form->setValues(['parent_id' => $parentId, 'type' => $type]);
        }

        $this->view->form = $form;
    }

    protected function getPageDetailsForm($type = 'add', $isHomepage = false)
    {
        $form = new FormElement();

        if ($type == 'add') {
            $form->setMethod('POST');
            $form->setAction('/' . $this->config->get('site.admin_uri') . '/page/add');
        }

        $form->setClass('smart-form');

        $fieldset = new Form\FieldSet('fieldset');
        $form->addField($fieldset);

        $fieldset->addField(Form\Element\Text::create('title', 'Page Title', true));
        $fieldset->addField(Form\Element\Text::create('short_title', 'Short Title', true));
        $fieldset->addField(Form\Element\Text::create('description', 'Description', true));
        $fieldset->addField(Form\Element\Text::create('meta_description', 'Meta Description', true));

        $templates = [];
        foreach ($this->getTemplates() as $template) {
            $templates[$template] = ucwords($template);
        }

        if (!count($templates)) {
            $this->errorMessage('You cannot create pages until you have created at least one page template.', true);

            $this->response = new RedirectResponse();
            $this->response->setHeader('Location', '/'.$this->config->get('site.admin_uri'));
            return;
        }

        $field = Form\Element\Select::create('template', 'Template', true);
        $field->setOptions($templates);
        $field->setClass('select2');
        $fieldset->addField($field);

        $fieldset->addField(Form\Element\Hidden::create('parent_id', '', false));

        if ($isHomepage) {
            $types = [];

            foreach (Store::get('ContentType')->all() as $cType) {
                $types[$cType->getId()] = $cType->getName();
            }

            if (count($types) == 0) {
                $this->errorMessage('You cannot create pages until you have created at least one content type.', true);

                $this->response = new RedirectResponse();
                $this->response->setHeader('Location', '/'.$this->config->get('site.admin_uri') . '/content-type');
                return;
            }

            $field = Form\Element\Select::create('type', 'Page Type', false);
            $field->setOptions($types);
            $field->setClass('select2');
            $fieldset->addField($field);
        } else {
            $fieldset->addField(Form\Element\Hidden::create('type', '', false));
        }

        $field = Form\Element\Text::create('publish_date', 'Publish Date', false);
        $field->setClass('datetime-picker');
        $fieldset->addField($field);

        $field = Form\Element\Text::create('expiry_date', 'Expiry Date', false);
        $field->setClass('datetime-picker');
        $fieldset->addField($field);


        $field = Form\Element\Text::create('image_id', 'Page Image', false);
        $field->setClass('octo-image-picker');
        $fieldset->addField($field);


        if ($type == 'add') {
            $field = new Form\Element\Submit();
            $field->setValue('Create Page');
            $field->setClass('btn-success');
            $fieldset->addField($field);
        }

        return $form;
    }

    protected function createPage()
    {
        // Create the models that we'll be using:
        $page = new Page();
        $version = new PageVersion();

        // Determine our page's parent, and set it if required:
        $parentId = $this->getParam('parent_id', null);

        if (!empty($parentId)) {
            $parent = $this->pageStore->getById($parentId);
            $page->setParent($parent);
        }

        // Create an ID for the page, which will also create a temporary URI for it:
        $page->generateId();
        $page->setContentTypeId($this->getParam('type'));

        $page->setPublishDate($this->getParam('publish_date', null));
        $page->setExpiryDate($this->getParam('expiry_date', null));

        /** @var \Octo\Pages\Model\Page $page */
        $page = $this->pageStore->saveByInsert($page);

        // Set up the current version of the page:
        $version->setValues($this->getParams());

        if (empty($this->getParam('image_id', null))) {
            $version->setImageId(null);
        }

        $version->setPage($page);
        $version->setVersion(1);
        $version->setUserId($this->currentUser->getId());
        $version->setUpdatedDate(new \DateTime());

        $content = '{}';
        $hash = md5($content);
        $contentObject = $this->contentStore->getById($hash);

        if (is_null($contentObject)) {
            $contentObject = new ContentItem();
            $contentObject->setId($hash);
            $contentObject->setContent($content);

            $this->contentStore->saveByInsert($contentObject);
        }
        $version->setContentItemId($hash);
        $version = $this->versionStore->saveByInsert($version);

        $page->setCurrentVersion($version);

        $page->generateUri();
        $this->pageStore->save($page);

        $uri = '/'.$this->config->get('site.admin_uri').'/page/edit/' . $page->getId();

        Log::create(Log::TYPE_CREATE, 'page', $version->getTitle(), $page->getId(), $uri);

        $this->response = new RedirectResponse();
        $this->response->setHeader('Location', $uri);
    }

    public function edit($pageId)
    {
        $page = $this->pageStore->getById($pageId);
        $latest = $this->pageStore->getLatestVersion($page);

        if ($page->getCurrentVersionId() == $latest->getId()) {
            $data = $latest->getDataArray();
            $data['version']++;
            unset($data['id']);

            $latest = new PageVersion();
            $latest->setValues($data);
        }

        $latest->setUpdatedDate(new \DateTime());
        $latest->setUser($this->currentUser);
        $latest = $this->versionStore->save($latest);

        $this->setTitle($latest->getTitle(), 'Manage Pages');
        $this->addBreadcrumb($latest->getTitle(), '/page/edit/' . $pageId);

        $blockTypes = Block::getBlocks();
        $contentDefinition = $page->getContentType()->getFullDefinition();

        $pageContent = [];

        if ($latest->getContentItemId()) {
            $pageContent = json_decode($latest->getContentItem()->getContent(), true);
        }

        $tabId = 0;
        foreach ($contentDefinition as &$tab) {
            $tab['idx'] = $tabId++;
            foreach ($tab['properties'] as $key => &$property) {
                if (array_key_exists($property['type'], $blockTypes)) {
                    $property['handler'] = $blockTypes[$property['type']];
                }

                if (array_key_exists($key, $pageContent)) {
                    $property['value'] = $pageContent[$key];
                } else {
                    $property['value'] = null;
                }

                if (!array_key_exists('handler', $property) || !array_key_exists('editor', $property['handler'])) {
                    $property['editable'] = false;
                    $property['editor'] = null;
                }
                else {
                    $property['editable'] = true;

                    $edit = [
                        'id' => $key,
                        'name' => $tab['name'],
                        'content' => $property['value'],
                    ];

                    $property['editor'] = $property['handler']['editor']($edit);
                }


            }
        }

        $this->view->page = $page;
        $this->view->latest = $latest;
        $this->view->blocks = $blockTypes;
        $this->view->contentDefinition = $contentDefinition;
        $this->view->templates = json_encode($this->getTemplates());
        $this->view->pages = json_encode($this->pageStore->getParentPageOptions());

        $form = $this->getPageDetailsForm('edit');
        $form->setValues($page->getDataArray());
        $form->setValues($latest->getDataArray());

        if ($page->getPublishDate()) {
            $form->setValues(['publish_date' => $page->getPublishDate()->format('Y-m-d H:i')]);
        }

        if ($page->getExpiryDate()) {
            $form->setValues(['expiry_date' => $page->getExpiryDate()->format('Y-m-d H:i')]);
        }

        // Prevent users from changing the parent of the homepage:
        if (is_null($page->getParentId())) {
            $form->getChild('element-fieldset')->removeChild('parent_id');
        }

        $this->view->pageDetailsForm = $form;

        if ($latest->getContentItemId()) {
            $this->view->pageContent = $latest->getContentItem()->getContent();
        } else {
            $this->view->pageContent = '{}';
        }
    }

    protected function getTemplates()
    {
        $rtn = [];
        $dir = new \DirectoryIterator(SITE_TEMPLATE_PATH);

        foreach ($dir as $item) {
            if ($item->isDot()) {
                continue;
            }

            if (!$item->isFile()) {
                continue;
            }

            if ($item->getExtension() !== 'html') {
                continue;
            }

            $rtn[$item->getBasename('.html')] = $item->getBasename('.html');
        }

        return $rtn;
    }

    protected function parseTemplate($template)
    {
        $blocks = array();
        $template = Template::load($template);
        $template->addFunction('block', function ($args) use (&$blocks) {
            $blocks[] = $args;
        });

        $template->render();

        return $blocks;
    }

    public function editPing($pageId)
    {
        $page = $this->pageStore->getById($pageId);

        if ($page) {
            $latest = $page->getLatestVersion();
            $latest->setUpdatedDate(new \DateTime());
            $this->versionStore->save($latest);
        }

        die('OK');
    }

    public function save($pageId)
    {
        $content = $this->getParam('content', null);

        if (!is_null($content)) {
            $page = $this->pageStore->getById($pageId);
            $latest = $this->pageStore->getLatestVersion($page);

            $uri = '/'.$this->config->get('site.admin_uri').'/page/edit/' . $page->getId();

            Log::create(Log::TYPE_EDIT, 'page', $latest->getTitle(), $page->getId(), $uri);

            $hash = md5($content);

            if ($latest->getContentItemId() !== $hash) {
                $contentObject = $this->contentStore->getById($hash);

                if (is_null($contentObject)) {
                    $contentObject = new ContentItem();
                    $contentObject->setId($hash);
                    $contentObject->setContent($content);

                    $this->contentStore->saveByInsert($contentObject);
                }

                $latest->setContentItemId($hash);
                $latest->setUpdatedDate(new \DateTime());
                $latest->setUser($this->currentUser);

                $this->versionStore->save($latest);
            }

            die(json_encode(['content_id' => $hash]));
        }

        $pageData = $this->getParam('page', null);

        if (!is_null($pageData)) {
            /** @var \Octo\Pages\Model\Page $page */
            $page = $this->pageStore->getById($pageId);

            if (array_key_exists('publish_date', $pageData)) {
                $page->setPublishDate(empty($pageData['publish_date']) ? null : $pageData['publish_date']);
            }

            if (array_key_exists('publish_date', $pageData)) {
                $page->setExpiryDate(empty($pageData['expiry_date']) ? null : $pageData['expiry_date']);
            }

            $newParent = $pageData['parent_id'];
            $curParent = $page->getParentId();

            if ($newParent != $curParent && !is_null($page->getParentId()) && $newParent != $page->getId()) {
                $page->setParentId($pageData['parent_id']);
                $page->generateUri();
            }

            $this->pageStore->saveByUpdate($page);

            $latest = $this->pageStore->getLatestVersion($page);

            if (array_key_exists('image_id', $pageData) && empty($pageData['image_id'])) {
                $latest->setImageId(null);
                unset($pageData['image_id']);
            }

            $latest->setValues($pageData);

            $latest->setUpdatedDate(new \DateTime());
            $latest->setUser($this->currentUser);

            $this->versionStore->save($latest);
        }

        die('OK');
    }

    public function publish($pageId)
    {
        $page = $this->pageStore->getById($pageId);
        $latest = $this->pageStore->getLatestVersion($page);
        $latest->setUpdatedDate(new \DateTime());

        $uri = '/'.$this->config->get('site.admin_uri').'/page/edit/' . $page->getId();

        Log::create(Log::TYPE_PUBLISH, 'page', $latest->getTitle(), $page->getId(), $uri);

        $this->versionStore->save($latest);

        $page->setCurrentVersion($latest);
        $page->generateUri();

        if (!$page->getPublishDate()) {
            $page->setPublishDate(new \DateTime());
        }

        /** @var \Octo\Pages\Model\Page $page */
        $page = $this->pageStore->save($page);

        $content = $latest->getContentItem()->getContent();

        $data = ['model' => $page, 'content_id' => $page->getId(), 'content' => $content];
        Event::trigger('ContentPublished', $data);

        $this->successMessage($latest->getTitle() . ' has been published!', true);
        $this->response = new \b8\Http\Response\RedirectResponse($this->response);

        $ancestors = $page->getAncestors();

        $open = [];
        foreach ($ancestors as $ancestor) {
            if (!$ancestor->getParentId() || $ancestor->getId() == $page->getId()) {
                continue;
            }

            $open[] = 'open[]=' . $ancestor->getId();
        }

        $append = implode('&amp;', $open);

        $this->response->setHeader('Location', '/'.$this->config->get('site.admin_uri').'/page?'.$append);
    }

    public function duplicate($pageId)
    {
        $page = $this->pageStore->getById($pageId);
        $latest = $this->pageStore->getLatestVersion($page);

        // Create a copy of the page:
        $newPage = new Page();
        $newPage->setParentId($page->getParentId());
        $newPage->setPosition($page->getPosition() + 1);
        $newPage->setContentType($page->getContentType());
        $newPage->setExpiryDate($page->getExpiryDate());
        $newPage->setPublishDate(null);
        $newPage->generateId();

        $newPage = $this->pageStore->saveByInsert($newPage);

        $copyContent = $latest->getDataArray();
        $copyContent['version'] = 1;
        $copyContent['page_id'] = $newPage->getId();
        $copyContent['title'] = 'Copy of ' . $latest->getTitle();
        $copyContent['short_title'] = 'Copy of ' . $latest->getShortTitle();
        $copyContent['user_id'] = $this->currentUser->getId();
        $copyContent['updated_date'] = new \DateTime();
        unset($copyContent['id']);

        $newVersion = new PageVersion();
        $newVersion->setValues($copyContent);
        $newVersion = $this->versionStore->save($newVersion);

        $newPage->setCurrentVersion($newVersion);
        $newPage->generateUri();
        $newPage = $this->pageStore->saveByUpdate($newPage);

        header('Location: /'.$this->config->get('site.admin_uri').'/page/edit/' . $newPage->getId());
        die;
    }

    public function delete($pageId)
    {
        $page = $this->pageStore->getById($pageId);
        $this->successMessage($page->getCurrentVersion()->getShortTitle() . ' has been deleted.', true);

        Log::create(Log::TYPE_DELETE, 'page', $page->getCurrentVersion()->getTitle(), $page->getId());

        $this->pageStore->delete($page);

        $this->response = new RedirectResponse();
        $this->response->setHeader('Location', '/'.$this->config->get('site.admin_uri').'/page');
    }

    public function autocomplete($identifier = 'id')
    {
        $pages = $this->pageStore->search($this->getParam('q', ''));

        $rtn = ['results' => [], 'more' => false];

        foreach ($pages as $page) {

            $id = $page->getId();

            if ($identifier == 'uri') {
                $id = $page->getUri();
            }

            $rtn['results'][] = ['id' => $id, 'text' => $page->getCurrentVersion()->getTitle()];
        }

        die(json_encode($rtn));
    }

    // Get meta information about a set of pages described by Id.
    public function metadata()
    {
        $pageIds = json_decode($this->getParam('q', '[]'));
        $rtn = ['results' => [], 'more' => false];

        foreach ($pageIds as $pageId) {
            $page = $this->pageStore->getById($pageId);

            if ($page) {
                $rtn['results'][] = ['id' => $page->getId(), 'text' => $page->getCurrentVersion()->getTitle()];
            }
        }

        die(json_encode($rtn));
    }

    public function sort()
    {
        $positions = $this->getParam('positions', []);

        foreach ($positions as $id => $position) {
            $page = $this->pageStore->getById($id);

            if ($page instanceof Page) {
                $page->setPosition($position);
                $this->pageStore->save($page);
            }
        }

        die('OK');
    }
}
