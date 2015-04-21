<?php

namespace Octo\Pages\Admin\Controller;

use b8\Config;
use b8\Form\Element\CheckboxGroup;
use b8\Form\Element\Hidden;
use b8\Form\Element\Radio;
use b8\Form\Element\Select;
use b8\Form\Element\Submit;
use b8\Form\Element\Text;
use b8\Form\Element\TextArea;
use b8\Http\Response\RedirectResponse;
use Octo\Admin\Form;
use Octo\Admin\Menu;
use Octo\Admin\Controller;
use Octo\Block;
use Octo\Pages\Model\ContentType;
use Octo\Store;
use Octo\Admin\Template;

class ContentTypeController extends Controller
{
    /**
     * @var \Octo\Pages\Store\ContentTypeStore
     */
    protected $store;

    /**
     * @var \Octo\Pages\Model\ContentTypeCollection
     */
    protected $types;

    protected $defaultDefinition = [
        ['name' => 'Properties', 'protected' => true, 'properties' => []],
        ['name' => 'Content', 'protected' => false, 'properties' => []],
    ];

    public static function registerMenus(Menu $menu)
    {
        $dev = $menu->getRoot('Developer');

        if (!$dev) {
            $dev = $menu->addRoot('Developer', '/developer', false)->setIcon('cogs');
        }

        $contentType = new Menu\Item('Content Types', '/content-type');
        $contentType->addChild(new Menu\Item('Add', '/content-type/add', true));
        $contentType->addChild(new Menu\Item('Edit', '/content-type/edit', true));
        $contentType->addChild(new Menu\Item('Delete', '/content-type/delete', true));
        $contentType->addChild(new Menu\Item('Save', '/content-type/save', true));
        $dev->addChild($contentType);
    }

    public function init()
    {
        $this->addBreadcrumb('Content Types', '/content-type');
        $this->store = Store::get('ContentType');
        $this->types = $this->store->all();
    }

    public function index()
    {
        $this->setTitle('Content Types');
        $this->view->items = $this->types;
    }

    public function add()
    {
        $this->setTitle('Add Content Type', 'Content Types');
        $this->addBreadcrumb('Add', '/content-type/add');
        $form = $this->typeForm();

        if ($this->request->getMethod() == 'POST') {
            $type = new ContentType();
            $type->setName($this->getParam('name'));
            $type->setAllowedChildren(json_encode($this->getParam('allowed_children')));
            $type->setDefinition(json_encode($this->defaultDefinition));

            $parent = $this->getParam('parent_id', null);

            if (empty($parent)) {
                $parent = null;
            }

            $type->setParentId($parent);
            $type->setIcon($this->getParam('icon', 'file-o'));

            $type = $this->store->saveByInsert($type);

            $this->successMessage('Content type created: ' . $type->getName(), true);

            $redirect =  '/'.$this->config->get('site.admin_uri').'/content-type/edit/' . $type->getId();
            $this->response = new RedirectResponse();
            $this->response->setHeader('Location', $redirect);
            return;
        }

        $this->view->form = $form;
    }

    public function edit($typeId)
    {
        /**
         * @var \Octo\Pages\Model\ContentType
         */
        $type = $this->store->getById($typeId);

        $this->setTitle($type->getName(), 'Edit Content Type');
        $this->addBreadcrumb($type->getName(), '/content-type/edit');
        $form = $this->typeForm();

        $values = $type->toArray(1);

        $form->setAction('/'.$this->config->get('site.admin_uri').'/content-type/edit/' . $typeId);
        $form->setValues($values);

        if ($this->request->getMethod() == 'POST') {
            $type->setName($this->getParam('name'));
            $type->setAllowedChildren($this->getParam('allowed_children'));
            $parent = $this->getParam('parent_id', null);

            if (empty($parent)) {
                $parent = null;
            }

            $type->setParentId($parent);
            $type->setIcon($this->getParam('icon', 'file-o'));

            $this->store->saveByUpdate($type);

            $this->successMessage('Content type updated: ' . $type->getName(), true);

            $this->response = new RedirectResponse();
            $this->response->setHeader('Location', '/'.$this->config->get('site.admin_uri').'/content-type');
            return;
        }

        $def = $type->getDefinition();

        if (empty($def) || (is_array($def) && !count($def))) {
            $def = $this->defaultDefinition;
        }

        $this->view->id = $typeId;
        $this->view->propertyEditor = $this->getPropertyEditor($typeId, 0, $def)->render();
        $this->view->form = $form;
    }

    public function delete($typeId)
    {
        $item = $this->store->getById($typeId);

        $this->store->delete($item);

        $this->successMessage('Content type deleted.', true);

        $this->response = new RedirectResponse();
        $this->response->setHeader('Location', '/'.$this->config->get('site.admin_uri').'/content-type');
        return;
    }


    protected function typeForm()
    {
        $assets = Config::getInstance()->get('Octo.AssetManager');
        $assets->addJs('Pages', 'content-type');

        $form = new Form('content-type');
        $form->setAction('/'.$this->config->get('site.admin_uri').'/content-type/add');
        $form->addField(Text::create('name', 'Name', true));

        $options = ['' => 'No Parent'];
        foreach ($this->types as $type) {
            $options[$type->getId()] = $type->getName();
        }

        $select = new Select();
        $select->setClass('select2');
        $select->setName('parent_id');
        $select->setLabel('Parent Type');
        $form->addField($select);
        $select->setOptions($options);

        unset($options['']);

        $select = new Select();
        $select->setClass('select2');
        $select->setName('allowed_children');
        $select->setLabel('Allow child pages of type:');
        $select->setMultiple(true);
        $form->addField($select);
        $select->setOptions($options);

        $select = new Select();
        $select->setClass('select2 icon');
        $select->setName('icon');
        $select->setLabel('Icon');
        $form->addField($select);
        $select->setOptions($this->faIcons());

        $submit = Submit::create('submit', 'Save')->setValue('Save Content Type');
        $submit->setClass('btn-success');
        $form->addField($submit);

        return $form;
    }

    public function save($typeId)
    {
        /**
         * @var \Octo\Pages\Model\ContentType
         */
        $type = $this->store->getById($typeId);
        $type->setValues($this->getParams());

        $type = $this->store->save($type);

        $this->response->disableLayout();
        $tab = $this->getParam('activeTab', 0);
        $this->view = $this->getPropertyEditor($type->getId(), $tab, $type->getDefinition());
    }

    protected function getPropertyEditor($typeId, $activeTab, $definition)
    {
        $blocks = Block::getBlocks();

        uasort($blocks, function ($a, $b) {
            if ($a['title'] > $b['title']) {
                return 1;
            }

            if ($a['title'] < $b['title']) {
                return -1;
            }

            return 0;
        });

        $view = Template::getAdminTemplate('ContentType/property-editor');
        $view->activeTab = $activeTab;
        $view->id = $typeId;
        $view->definition = json_decode($definition, true);
        $view->blockTypes = $blocks;

        return $view;
    }

    protected function faIcons()
    {
        return [
            'glass' => 'glass',
            'music' => 'music',
            'search' => 'search',
            'envelope-o' => 'envelope-o',
            'heart' => 'heart',
            'star' => 'star',
            'star-o' => 'star-o',
            'user' => 'user',
            'film' => 'film',
            'th-large' => 'th-large',
            'th' => 'th',
            'th-list' => 'th-list',
            'check' => 'check',
            'times' => 'times',
            'search-plus' => 'search-plus',
            'search-minus' => 'search-minus',
            'power-off' => 'power-off',
            'signal' => 'signal',
            'cog' => 'cog',
            'trash-o' => 'trash-o',
            'home' => 'home',
            'file-o' => 'file-o',
            'clock-o' => 'clock-o',
            'road' => 'road',
            'download' => 'download',
            'arrow-circle-o-down' => 'arrow-circle-o-down',
            'arrow-circle-o-up' => 'arrow-circle-o-up',
            'inbox' => 'inbox',
            'play-circle-o' => 'play-circle-o',
            'repeat' => 'repeat',
            'refresh' => 'refresh',
            'list-alt' => 'list-alt',
            'lock' => 'lock',
            'flag' => 'flag',
            'headphones' => 'headphones',
            'volume-off' => 'volume-off',
            'volume-down' => 'volume-down',
            'volume-up' => 'volume-up',
            'qrcode' => 'qrcode',
            'barcode' => 'barcode',
            'tag' => 'tag',
            'tags' => 'tags',
            'book' => 'book',
            'bookmark' => 'bookmark',
            'print' => 'print',
            'camera' => 'camera',
            'font' => 'font',
            'bold' => 'bold',
            'italic' => 'italic',
            'text-height' => 'text-height',
            'text-width' => 'text-width',
            'align-left' => 'align-left',
            'align-center' => 'align-center',
            'align-right' => 'align-right',
            'align-justify' => 'align-justify',
            'list' => 'list',
            'outdent' => 'outdent',
            'indent' => 'indent',
            'video-camera' => 'video-camera',
            'picture-o' => 'picture-o',
            'pencil' => 'pencil',
            'map-marker' => 'map-marker',
            'adjust' => 'adjust',
            'tint' => 'tint',
            'pencil-square-o' => 'pencil-square-o',
            'share-square-o' => 'share-square-o',
            'check-square-o' => 'check-square-o',
            'arrows' => 'arrows',
            'step-backward' => 'step-backward',
            'fast-backward' => 'fast-backward',
            'backward' => 'backward',
            'play' => 'play',
            'pause' => 'pause',
            'stop' => 'stop',
            'forward' => 'forward',
            'fast-forward' => 'fast-forward',
            'step-forward' => 'step-forward',
            'eject' => 'eject',
            'chevron-left' => 'chevron-left',
            'chevron-right' => 'chevron-right',
            'plus-circle' => 'plus-circle',
            'minus-circle' => 'minus-circle',
            'times-circle' => 'times-circle',
            'check-circle' => 'check-circle',
            'question-circle' => 'question-circle',
            'info-circle' => 'info-circle',
            'crosshairs' => 'crosshairs',
            'times-circle-o' => 'times-circle-o',
            'check-circle-o' => 'check-circle-o',
            'ban' => 'ban',
            'arrow-left' => 'arrow-left',
            'arrow-right' => 'arrow-right',
            'arrow-up' => 'arrow-up',
            'arrow-down' => 'arrow-down',
            'share' => 'share',
            'expand' => 'expand',
            'compress' => 'compress',
            'plus' => 'plus',
            'minus' => 'minus',
            'asterisk' => 'asterisk',
            'exclamation-circle' => 'exclamation-circle',
            'gift' => 'gift',
            'leaf' => 'leaf',
            'fire' => 'fire',
            'eye' => 'eye',
            'eye-slash' => 'eye-slash',
            'exclamation-triangle' => 'exclamation-triangle',
            'plane' => 'plane',
            'calendar' => 'calendar',
            'random' => 'random',
            'comment' => 'comment',
            'magnet' => 'magnet',
            'chevron-up' => 'chevron-up',
            'chevron-down' => 'chevron-down',
            'retweet' => 'retweet',
            'shopping-cart' => 'shopping-cart',
            'folder' => 'folder',
            'folder-open' => 'folder-open',
            'arrows-v' => 'arrows-v',
            'arrows-h' => 'arrows-h',
            'bar-chart' => 'bar-chart',
            'twitter-square' => 'twitter-square',
            'facebook-square' => 'facebook-square',
            'camera-retro' => 'camera-retro',
            'key' => 'key',
            'cogs' => 'cogs',
            'comments' => 'comments',
            'thumbs-o-up' => 'thumbs-o-up',
            'thumbs-o-down' => 'thumbs-o-down',
            'star-half' => 'star-half',
            'heart-o' => 'heart-o',
            'sign-out' => 'sign-out',
            'linkedin-square' => 'linkedin-square',
            'thumb-tack' => 'thumb-tack',
            'external-link' => 'external-link',
            'sign-in' => 'sign-in',
            'trophy' => 'trophy',
            'github-square' => 'github-square',
            'upload' => 'upload',
            'lemon-o' => 'lemon-o',
            'phone' => 'phone',
            'square-o' => 'square-o',
            'bookmark-o' => 'bookmark-o',
            'phone-square' => 'phone-square',
            'twitter' => 'twitter',
            'facebook' => 'facebook',
            'github' => 'github',
            'unlock' => 'unlock',
            'credit-card' => 'credit-card',
            'rss' => 'rss',
            'hdd-o' => 'hdd-o',
            'bullhorn' => 'bullhorn',
            'bell' => 'bell',
            'certificate' => 'certificate',
            'hand-o-right' => 'hand-o-right',
            'hand-o-left' => 'hand-o-left',
            'hand-o-up' => 'hand-o-up',
            'hand-o-down' => 'hand-o-down',
            'arrow-circle-left' => 'arrow-circle-left',
            'arrow-circle-right' => 'arrow-circle-right',
            'arrow-circle-up' => 'arrow-circle-up',
            'arrow-circle-down' => 'arrow-circle-down',
            'globe' => 'globe',
            'wrench' => 'wrench',
            'tasks' => 'tasks',
            'filter' => 'filter',
            'briefcase' => 'briefcase',
            'arrows-alt' => 'arrows-alt',
            'users' => 'users',
            'link' => 'link',
            'cloud' => 'cloud',
            'flask' => 'flask',
            'scissors' => 'scissors',
            'files-o' => 'files-o',
            'paperclip' => 'paperclip',
            'floppy-o' => 'floppy-o',
            'square' => 'square',
            'bars' => 'bars',
            'list-ul' => 'list-ul',
            'list-ol' => 'list-ol',
            'strikethrough' => 'strikethrough',
            'underline' => 'underline',
            'table' => 'table',
            'magic' => 'magic',
            'truck' => 'truck',
            'pinterest' => 'pinterest',
            'pinterest-square' => 'pinterest-square',
            'google-plus-square' => 'google-plus-square',
            'google-plus' => 'google-plus',
            'money' => 'money',
            'caret-down' => 'caret-down',
            'caret-up' => 'caret-up',
            'caret-left' => 'caret-left',
            'caret-right' => 'caret-right',
            'columns' => 'columns',
            'sort' => 'sort',
            'sort-desc' => 'sort-desc',
            'sort-asc' => 'sort-asc',
            'envelope' => 'envelope',
            'linkedin' => 'linkedin',
            'undo' => 'undo',
            'gavel' => 'gavel',
            'tachometer' => 'tachometer',
            'comment-o' => 'comment-o',
            'comments-o' => 'comments-o',
            'bolt' => 'bolt',
            'sitemap' => 'sitemap',
            'umbrella' => 'umbrella',
            'clipboard' => 'clipboard',
            'lightbulb-o' => 'lightbulb-o',
            'exchange' => 'exchange',
            'cloud-download' => 'cloud-download',
            'cloud-upload' => 'cloud-upload',
            'user-md' => 'user-md',
            'stethoscope' => 'stethoscope',
            'suitcase' => 'suitcase',
            'bell-o' => 'bell-o',
            'coffee' => 'coffee',
            'cutlery' => 'cutlery',
            'file-text-o' => 'file-text-o',
            'building-o' => 'building-o',
            'hospital-o' => 'hospital-o',
            'ambulance' => 'ambulance',
            'medkit' => 'medkit',
            'fighter-jet' => 'fighter-jet',
            'beer' => 'beer',
            'h-square' => 'h-square',
            'plus-square' => 'plus-square',
            'angle-double-left' => 'angle-double-left',
            'angle-double-right' => 'angle-double-right',
            'angle-double-up' => 'angle-double-up',
            'angle-double-down' => 'angle-double-down',
            'angle-left' => 'angle-left',
            'angle-right' => 'angle-right',
            'angle-up' => 'angle-up',
            'angle-down' => 'angle-down',
            'desktop' => 'desktop',
            'laptop' => 'laptop',
            'tablet' => 'tablet',
            'mobile' => 'mobile',
            'circle-o' => 'circle-o',
            'quote-left' => 'quote-left',
            'quote-right' => 'quote-right',
            'spinner' => 'spinner',
            'circle' => 'circle',
            'reply' => 'reply',
            'github-alt' => 'github-alt',
            'folder-o' => 'folder-o',
            'folder-open-o' => 'folder-open-o',
            'smile-o' => 'smile-o',
            'frown-o' => 'frown-o',
            'meh-o' => 'meh-o',
            'gamepad' => 'gamepad',
            'keyboard-o' => 'keyboard-o',
            'flag-o' => 'flag-o',
            'flag-checkered' => 'flag-checkered',
            'terminal' => 'terminal',
            'code' => 'code',
            'reply-all' => 'reply-all',
            'star-half-o' => 'star-half-o',
            'location-arrow' => 'location-arrow',
            'crop' => 'crop',
            'code-fork' => 'code-fork',
            'chain-broken' => 'chain-broken',
            'question' => 'question',
            'info' => 'info',
            'exclamation' => 'exclamation',
            'superscript' => 'superscript',
            'subscript' => 'subscript',
            'eraser' => 'eraser',
            'puzzle-piece' => 'puzzle-piece',
            'microphone' => 'microphone',
            'microphone-slash' => 'microphone-slash',
            'shield' => 'shield',
            'calendar-o' => 'calendar-o',
            'fire-extinguisher' => 'fire-extinguisher',
            'rocket' => 'rocket',
            'maxcdn' => 'maxcdn',
            'chevron-circle-left' => 'chevron-circle-left',
            'chevron-circle-right' => 'chevron-circle-right',
            'chevron-circle-up' => 'chevron-circle-up',
            'chevron-circle-down' => 'chevron-circle-down',
            'html5' => 'html5',
            'css3' => 'css3',
            'anchor' => 'anchor',
            'unlock-alt' => 'unlock-alt',
            'bullseye' => 'bullseye',
            'ellipsis-h' => 'ellipsis-h',
            'ellipsis-v' => 'ellipsis-v',
            'rss-square' => 'rss-square',
            'play-circle' => 'play-circle',
            'ticket' => 'ticket',
            'minus-square' => 'minus-square',
            'minus-square-o' => 'minus-square-o',
            'level-up' => 'level-up',
            'level-down' => 'level-down',
            'check-square' => 'check-square',
            'pencil-square' => 'pencil-square',
            'external-link-square' => 'external-link-square',
            'share-square' => 'share-square',
            'compass' => 'compass',
            'caret-square-o-down' => 'caret-square-o-down',
            'caret-square-o-up' => 'caret-square-o-up',
            'caret-square-o-right' => 'caret-square-o-right',
            'eur' => 'eur',
            'gbp' => 'gbp',
            'usd' => 'usd',
            'inr' => 'inr',
            'jpy' => 'jpy',
            'rub' => 'rub',
            'krw' => 'krw',
            'btc' => 'btc',
            'file' => 'file',
            'file-text' => 'file-text',
            'sort-alpha-asc' => 'sort-alpha-asc',
            'sort-alpha-desc' => 'sort-alpha-desc',
            'sort-amount-asc' => 'sort-amount-asc',
            'sort-amount-desc' => 'sort-amount-desc',
            'sort-numeric-asc' => 'sort-numeric-asc',
            'sort-numeric-desc' => 'sort-numeric-desc',
            'thumbs-up' => 'thumbs-up',
            'thumbs-down' => 'thumbs-down',
            'youtube-square' => 'youtube-square',
            'youtube' => 'youtube',
            'xing' => 'xing',
            'xing-square' => 'xing-square',
            'youtube-play' => 'youtube-play',
            'dropbox' => 'dropbox',
            'stack-overflow' => 'stack-overflow',
            'instagram' => 'instagram',
            'flickr' => 'flickr',
            'adn' => 'adn',
            'bitbucket' => 'bitbucket',
            'bitbucket-square' => 'bitbucket-square',
            'tumblr' => 'tumblr',
            'tumblr-square' => 'tumblr-square',
            'long-arrow-down' => 'long-arrow-down',
            'long-arrow-up' => 'long-arrow-up',
            'long-arrow-left' => 'long-arrow-left',
            'long-arrow-right' => 'long-arrow-right',
            'apple' => 'apple',
            'windows' => 'windows',
            'android' => 'android',
            'linux' => 'linux',
            'dribbble' => 'dribbble',
            'skype' => 'skype',
            'foursquare' => 'foursquare',
            'trello' => 'trello',
            'female' => 'female',
            'male' => 'male',
            'gratipay' => 'gratipay',
            'sun-o' => 'sun-o',
            'moon-o' => 'moon-o',
            'archive' => 'archive',
            'bug' => 'bug',
            'vk' => 'vk',
            'weibo' => 'weibo',
            'renren' => 'renren',
            'pagelines' => 'pagelines',
            'stack-exchange' => 'stack-exchange',
            'arrow-circle-o-right' => 'arrow-circle-o-right',
            'arrow-circle-o-left' => 'arrow-circle-o-left',
            'caret-square-o-left' => 'caret-square-o-left',
            'dot-circle-o' => 'dot-circle-o',
            'wheelchair' => 'wheelchair',
            'vimeo-square' => 'vimeo-square',
            'try' => 'try',
            'plus-square-o' => 'plus-square-o',
            'space-shuttle' => 'space-shuttle',
            'slack' => 'slack',
            'envelope-square' => 'envelope-square',
            'wordpress' => 'wordpress',
            'openid' => 'openid',
            'university' => 'university',
            'graduation-cap' => 'graduation-cap',
            'yahoo' => 'yahoo',
            'google' => 'google',
            'reddit' => 'reddit',
            'reddit-square' => 'reddit-square',
            'stumbleupon-circle' => 'stumbleupon-circle',
            'stumbleupon' => 'stumbleupon',
            'delicious' => 'delicious',
            'digg' => 'digg',
            'pied-piper' => 'pied-piper',
            'pied-piper-alt' => 'pied-piper-alt',
            'drupal' => 'drupal',
            'joomla' => 'joomla',
            'language' => 'language',
            'fax' => 'fax',
            'building' => 'building',
            'child' => 'child',
            'paw' => 'paw',
            'spoon' => 'spoon',
            'cube' => 'cube',
            'cubes' => 'cubes',
            'behance' => 'behance',
            'behance-square' => 'behance-square',
            'steam' => 'steam',
            'steam-square' => 'steam-square',
            'recycle' => 'recycle',
            'car' => 'car',
            'taxi' => 'taxi',
            'tree' => 'tree',
            'spotify' => 'spotify',
            'deviantart' => 'deviantart',
            'soundcloud' => 'soundcloud',
            'database' => 'database',
            'file-pdf-o' => 'file-pdf-o',
            'file-word-o' => 'file-word-o',
            'file-excel-o' => 'file-excel-o',
            'file-powerpoint-o' => 'file-powerpoint-o',
            'file-image-o' => 'file-image-o',
            'file-archive-o' => 'file-archive-o',
            'file-audio-o' => 'file-audio-o',
            'file-video-o' => 'file-video-o',
            'file-code-o' => 'file-code-o',
            'vine' => 'vine',
            'codepen' => 'codepen',
            'jsfiddle' => 'jsfiddle',
            'life-ring' => 'life-ring',
            'circle-o-notch' => 'circle-o-notch',
            'rebel' => 'rebel',
            'empire' => 'empire',
            'git-square' => 'git-square',
            'git' => 'git',
            'hacker-news' => 'hacker-news',
            'tencent-weibo' => 'tencent-weibo',
            'qq' => 'qq',
            'weixin' => 'weixin',
            'paper-plane' => 'paper-plane',
            'paper-plane-o' => 'paper-plane-o',
            'history' => 'history',
            'circle-thin' => 'circle-thin',
            'header' => 'header',
            'paragraph' => 'paragraph',
            'sliders' => 'sliders',
            'share-alt' => 'share-alt',
            'share-alt-square' => 'share-alt-square',
            'bomb' => 'bomb',
            'futbol-o' => 'futbol-o',
            'tty' => 'tty',
            'binoculars' => 'binoculars',
            'plug' => 'plug',
            'slideshare' => 'slideshare',
            'twitch' => 'twitch',
            'yelp' => 'yelp',
            'newspaper-o' => 'newspaper-o',
            'wifi' => 'wifi',
            'calculator' => 'calculator',
            'paypal' => 'paypal',
            'google-wallet' => 'google-wallet',
            'cc-visa' => 'cc-visa',
            'cc-mastercard' => 'cc-mastercard',
            'cc-discover' => 'cc-discover',
            'cc-amex' => 'cc-amex',
            'cc-paypal' => 'cc-paypal',
            'cc-stripe' => 'cc-stripe',
            'bell-slash' => 'bell-slash',
            'bell-slash-o' => 'bell-slash-o',
            'trash' => 'trash',
            'copyright' => 'copyright',
            'at' => 'at',
            'eyedropper' => 'eyedropper',
            'paint-brush' => 'paint-brush',
            'birthday-cake' => 'birthday-cake',
            'area-chart' => 'area-chart',
            'pie-chart' => 'pie-chart',
            'line-chart' => 'line-chart',
            'lastfm' => 'lastfm',
            'lastfm-square' => 'lastfm-square',
            'toggle-off' => 'toggle-off',
            'toggle-on' => 'toggle-on',
            'bicycle' => 'bicycle',
            'bus' => 'bus',
            'ioxhost' => 'ioxhost',
            'angellist' => 'angellist',
            'cc' => 'cc',
            'ils' => 'ils',
            'meanpath' => 'meanpath',
            'buysellads' => 'buysellads',
            'connectdevelop' => 'connectdevelop',
            'dashcube' => 'dashcube',
            'forumbee' => 'forumbee',
            'leanpub' => 'leanpub',
            'sellsy' => 'sellsy',
            'shirtsinbulk' => 'shirtsinbulk',
            'simplybuilt' => 'simplybuilt',
            'skyatlas' => 'skyatlas',
            'cart-plus' => 'cart-plus',
            'cart-arrow-down' => 'cart-arrow-down',
            'diamond' => 'diamond',
            'ship' => 'ship',
            'user-secret' => 'user-secret',
            'motorcycle' => 'motorcycle',
            'street-view' => 'street-view',
            'heartbeat' => 'heartbeat',
            'venus' => 'venus',
            'mars' => 'mars',
            'mercury' => 'mercury',
            'transgender' => 'transgender',
            'transgender-alt' => 'transgender-alt',
            'venus-double' => 'venus-double',
            'mars-double' => 'mars-double',
            'venus-mars' => 'venus-mars',
            'mars-stroke' => 'mars-stroke',
            'mars-stroke-v' => 'mars-stroke-v',
            'mars-stroke-h' => 'mars-stroke-h',
            'neuter' => 'neuter',
            'facebook-official' => 'facebook-official',
            'pinterest-p' => 'pinterest-p',
            'whatsapp' => 'whatsapp',
            'server' => 'server',
            'user-plus' => 'user-plus',
            'user-times' => 'user-times',
            'bed' => 'bed',
            'viacoin' => 'viacoin',
            'train' => 'train',
            'subway' => 'subway',
            'medium' => 'medium',
        ];
    }
}