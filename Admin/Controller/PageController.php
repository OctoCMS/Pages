<?php

namespace Octo\Pages\Admin\Controller;

use b8\Form;
use Octo\Admin\Controller;
use Octo\Admin\Form as FormElement;
use Octo\Admin\Template as AdminTemplate;
use Octo\Admin\Menu;
use Octo\Block;
use Octo\Event;
use Octo\Form\Element\DateOfBirth;
use Octo\Pages\Model\ContentType;
use Octo\Pages\Model\Page;
use Octo\Pages\Model\PageVersion;
use Octo\Store;
use Octo\System\Model\ContentItem;
use Octo\System\Model\Log;
use Octo\Template;

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

        if ($this->request->isAjax()) {
            $pages = $this->pageStore->where('parent_id', $parentId)
                ->order('position', 'ASC')
                ->order('publish_date', 'DESC')
                ->get();

            $rtn = [];

            /** @var Page $page */
            foreach ($pages as $page) {
                $latest = $page->getLatestVersion();

                $rtn[] = [
                    'id' => $page->getId(),
                    'title' => $latest->getTitle(),
                    'short_title' => $latest->getShortTitle(),
                    'children' => $page->hasChildren(),
                    'updated' => $latest->getUpdatedDate()->format('Y-m-d H:i:s'),
                    'author' => $latest->getUser()->getName(),
                    'locked' => $page->getIsLocked(),
                    'type' => $page->getContentTypeId(),
                    'root' => $page->getParentId() ? false : true,
                    'icon' => $page->getContentType()->getIcon(),
                ];
            }

            return $this->json($rtn);
        }

        $allTypes = [];
        foreach (Store::get('ContentType')->all() as $type) {
            $allTypes[$type->getId()] = $type;
        }

        $types = [];
        /** @var ContentType $type */
        foreach ($allTypes as $type) {
            $thisTypes = [];

            $childTypes = $type->getAllowedChildren() ?: [];
            foreach ($childTypes as $typeId) {
                $thisTypes[$typeId] = [
                    'name' => $allTypes[$typeId]->getName(),
                    'icon' => $allTypes[$typeId]->getIcon()]
                ;
            }

            $types[$type->getId()] = $thisTypes;
        }

        $this->template->types = $types;
        $parent = $this->pageStore->getHomepage();

        if (is_null($parent)) {
            return $this->redirect('/page/add')->success('Create your first page using the form below.');
        }

        $pages = [$parent];
        $this->template->pages = $pages;
        $this->template->open = $this->getParam('open', []);
    }

    public function add($type = null, $parentId = null)
    {
        if ($this->request->getMethod() == 'POST') {
            return $this->createPage();
        }

        $this->setTitle('Add Page');
        $this->addBreadcrumb('Add Page', '/page/add');

        if (is_null($type) || is_null($parentId)) {
            return $this->redirect('/page/create-homepage');
        }

        $contentType = Store::get('ContentType')->getById($type);

        $form = $this->getPageDetailsForm('add', $contentType);

        if ($form) {
            $form->setValues(['parent_id' => $parentId, 'type' => $type]);
        }

        $this->view->form = $form;
    }

    protected function getPageDetailsForm($type = 'add', ContentType $contentType)
    {
        $form = new FormElement();

        if ($type == 'add') {
            $form->setMethod('POST');
            $form->setAction($this->config->get('site.full_admin_url') . '/page/add');
        }

        $form->setClass('smart-form');

        $fieldset = new Form\FieldSet('fieldset');
        $form->addField($fieldset);

        $fieldset->addField(Form\Element\Text::create('title', 'Page Title', true));

        if ($type != 'add') {
            $fieldset->addField(Form\Element\Text::create('short_title', 'Short Title', true));
        }

        $fieldset->addField(Form\Element\Text::create('description', 'Description', true));

        if ($type != 'add') {
            $fieldset->addField(Form\Element\Text::create('meta_description', 'Meta Description', true));
        }

        $allowedTemplates = $contentType->getAllowedTemplates() ?: [];
        $templates = [];
        foreach ($allowedTemplates as $template) {
            $templates[$template] = ucwords($template);

        }

        $templateCount = count($templates);

        if ($templateCount == 0) {
            return $this->redirect('/')
                ->error('You cannot create pages until you have created at least one page template.');
        } elseif ($templateCount == 1) {
            $field = Form\Element\Hidden::create('template', 'Template', true);
            $field->setValue($template);
            $fieldset->addField($field);
        } else {
            $field = Form\Element\Select::create('template', 'Template', true);
            $field->setOptions($templates);
            $field->setClass('select2');
            $fieldset->addField($field);
        }

        $fieldset->addField(Form\Element\Hidden::create('parent_id', '', false));
        $fieldset->addField(Form\Element\Hidden::create('type', '', false));

        $field = Form\Element\Text::create('publish_date', 'Publish Date', false);
        $field->setClass('datetime-picker');
        $fieldset->addField($field);

        if ($type != 'add') {
            $field = Form\Element\Text::create('expiry_date', 'Expiry Date', false);
            $field->setClass('datetime-picker');
            $fieldset->addField($field);
        }

        $field = Form\Element\Select::create('image_id', 'Page Image', false);
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
        $page->setPublishDate($this->getParam('publish_date', new \DateTime()));
        $page->setExpiryDate($this->getParam('expiry_date', null));

        /** @var \Octo\Pages\Model\Page $page */
        $page = $this->pageStore->insert($page);

        // Set up the current version of the page:
        $version->setValues($this->getParams());

        $shortTitle = $version->getTitle();

        if (strlen($shortTitle) > 50) {
            $shortTitle = substr($shortTitle, 0, 47) . '...';
        }

        $version->setShortTitle($shortTitle);
        $version->setMetaDescription($version->getDescription());

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

            $this->contentStore->insert($contentObject);
        }
        $version->setContentItemId($hash);
        $version = $this->versionStore->insert($version);

        $page->setCurrentVersion($version);

        $page->generateUri();
        $this->pageStore->save($page);

        $uri = $this->config->get('site.full_admin_url').'/page/edit/' . $page->getId();

        Log::create(Log::TYPE_CREATE, 'page', $version->getTitle(), $page->getId(), $uri);

        return $this->redirect('/page/edit/' . $page->getId());
    }

    public function edit($pageId)
    {
        $page = $this->pageStore->getById($pageId);
        $latest = $this->pageStore->getLatestVersion($page);

        if ($page->getCurrentVersionId() == $latest->getId()) {
            $data = $latest->toArray();
            $data['version']++;
            unset($data['id']);

            $latest = new PageVersion();
            $latest->setValues($data);
        }

        $latest->setUpdatedDate(new \DateTime());
        $latest->setUser($this->currentUser);

        /** @var PageVersion $latest */
        $latest = $this->versionStore->save($latest);

        $this->setTitle($latest->getTitle(), 'Manage Pages');
        $this->addBreadcrumb($latest->getTitle(), '/page/edit/' . $pageId);

        $blockTypes = Block::getBlocks();
        $contentDefinition = $page->getContentType()->getFullDefinition();

        $pageContent = [];

        if ($latest->getContentItemId()) {
            $pageContent = $latest->getContentItem()->getContent();
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
                    $property['full'] = !empty($property['handler']['full']);

                    $edit = [
                        'id' => $key,
                        'name' => $tab['name'],
                        'content' => $property['value'],
                    ];

                    $property['editor'] = $property['handler']['editor']($edit);
                }


            }
        }

        $this->template->page = $page;
        $this->template->latest = $latest;
        $this->template->blocks = $blockTypes;
        $this->template->contentDefinition = $contentDefinition;
        $this->template->pages = json_encode($this->pageStore->getParentPageOptions());

        $form = $this->getPageDetailsForm('edit', $page->getContentType());

        $imageId = $latest->getImageId();

        if (!empty($imageId)) {
            $image = $form->find('image_id');
            $image->setOptions([$imageId => $latest->getImage()->getTitle()]);
        }

        $form->setValues($page->toArray());
        $form->setValues($latest->toArray());

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

        $this->template->pageDetailsForm = $form;

        if ($latest->getContentItemId()) {
            $this->template->pageContent = $latest->getContentItem()->getContent();
        } else {
            $this->template->pageContent = [];
        }
    }

    public function editPing($pageId)
    {
        $page = $this->pageStore->getById($pageId);

        if ($page) {
            $latest = $page->getLatestVersion();
            $latest->setUpdatedDate(new \DateTime());
            $this->versionStore->save($latest);
        }

        return $this->json('OK');
    }

    public function save($pageId)
    {
        $content = $this->getParam('content', null);

        if (!is_null($content)) {
            $page = $this->pageStore->getById($pageId);
            $latest = $this->pageStore->getLatestVersion($page);

            $uri = $this->config->get('site.full_admin_url').'/page/edit/' . $page->getId();

            Log::create(Log::TYPE_EDIT, 'page', $latest->getTitle(), $page->getId(), $uri);

            $hash = md5($content);

            if ($latest->getContentItemId() !== $hash) {
                $contentObject = $this->contentStore->getById($hash);

                if (is_null($contentObject)) {
                    $contentObject = new ContentItem();
                    $contentObject->setId($hash);
                    $contentObject->setContent($content);

                    $this->contentStore->replace($contentObject);
                }

                $latest->setContentItemId($hash);
                $latest->setUpdatedDate(new \DateTime());
                $latest->setUser($this->currentUser);

                $this->versionStore->save($latest);
            }

            return $this->json(['content_id' => $hash]);
        }

        $pageData = $this->getParam('page', null);

        if (!is_null($pageData)) {
            /** @var \Octo\Pages\Model\Page $page */
            $page = $this->pageStore->getById($pageId);

            if (array_key_exists('publish_date', $pageData)) {
                $page->setPublishDate(empty($pageData['publish_date']) ? null : $pageData['publish_date']);
            }

            if (array_key_exists('expiry_date', $pageData)) {
                $page->setExpiryDate(empty($pageData['expiry_date']) ? null : $pageData['expiry_date']);
            }

            $newParent = $pageData['parent_id'];
            $curParent = $page->getParentId();

            if ($newParent != $curParent && !is_null($page->getParentId()) && $newParent != $page->getId()) {
                $page->setParentId($pageData['parent_id']);
                $page->generateUri();
            }

            $this->pageStore->update($page);

            $latest = $this->pageStore->getLatestVersion($page);

            if (empty($pageData['image_id'])) {
                $latest->setImageId(null);
                unset($pageData['image_id']);
            }

            $latest->setValues($pageData);

            $latest->setUpdatedDate(new \DateTime());
            $latest->setUser($this->currentUser);

            $this->versionStore->save($latest);
        }

        return $this->json('OK');
    }

    public function publish($pageId)
    {
        $page = $this->pageStore->getById($pageId);
        $latest = $this->pageStore->getLatestVersion($page);
        $latest->setUpdatedDate(new \DateTime());

        $uri = $this->config->get('site.full_admin_url').'/page/edit/' . $page->getId();

        Log::create(Log::TYPE_PUBLISH, 'page', $latest->getTitle(), $page->getId(), $uri);

        $this->versionStore->save($latest);

        $page->setCurrentVersion($latest);
        $page->generateUri();

        if (!$page->getPublishDate()) {
            $page->setPublishDate(new \DateTime());
        }

        /** @var \Octo\Pages\Model\Page $page */
        $page = $this->pageStore->save($page);

        $ancestors = $page->getAncestors();

        $open = [];
        foreach ($ancestors as $ancestor) {
            if (!$ancestor->getParentId() || $ancestor->getId() == $page->getId()) {
                continue;
            }

            $open[] = 'open[]=' . $ancestor->getId();
        }

        return $this->redirect('/page?' . implode('&amp;', $open))->success($latest->getTitle() . ' has been published!');
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

        $newPage = $this->pageStore->insert($newPage);

        $copyContent = $latest->toArray();
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
        $newPage = $this->pageStore->update($newPage);

        return $this->redirect('/page/edit/' . $newPage->getId());
    }

    public function delete($pageId)
    {
        $page = $this->pageStore->getById($pageId);

        if (empty($page)) {
            return $this->redirect('/page')->error('Page ID '. $pageId . ' does not exist.');
        }

        $shortTitle = $page->getCurrentVersion()->getShortTitle();
        $this->pageStore->delete($page);

        Log::create(Log::TYPE_DELETE, 'page', $shortTitle, $page->getId());

        return $this->redirect('/page')->success($shortTitle. ' has been deleted.');
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

            $rtn['results'][] = [
                'id' => $id,
                'text' => $page->getCurrentVersion()->getTitle(),
                'uri' => $page->getUri(),
                'image' => $page->getCurrentVersion()->getImageId(),
            ];
        }

        return $this->json($rtn);
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

        return $this->json($rtn);
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

        return $this->json('OK');
    }
}
