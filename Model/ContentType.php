<?php

/**
 * ContentType model for table: content_type */

namespace Octo\Pages\Model;

use Octo;

/**
 * ContentType Model
 */
class ContentType extends Octo\Model
{
    use Base\ContentTypeBase;

    /**
     * @return array
     */
    public function getFullDefinition($inherited = false)
    {
        $rtn = [];

        if ($this->getParentId()) {
            $rtn = $this->getParent()->getFullDefinition(true);
        }

        $def = json_decode($this->getDefinition(), true);

        foreach ($def as $tab) {
            if (!array_key_exists($tab['name'], $rtn)) {
                $rtn[$tab['name']] = [
                    'name' => $tab['name'],
                    'protected' => $tab['protected'],
                    'properties' => [],
                ];
            }

            foreach ($tab['properties'] as $key => $value) {
                $rtn[$tab['name']]['properties'][$key] = $tab['properties'][$key];
                $rtn[$tab['name']]['properties'][$key]['inherited'] = $inherited;
            }
        }

        return $rtn;
    }

    public function setAllowedChildren($value)
    {
        if (is_array($value)) {
            $value = json_encode($value);
        }

        if (empty($value)) {
            $value = null;
        }

        $this->validateString('AllowedChildren', $value);

        if ($this->data['allowed_children'] === $value) {
            return;
        }

        $this->data['allowed_children'] = $value;
        $this->setModified('allowed_children');
    }

    public function getAllowedChildren()
    {
        $rtn = $this->data['allowed_children'];

        if (!empty($rtn)) {
            $rtn = json_decode($rtn, true);
        } else {
            $rtn = [];
        }

        return $rtn;
    }

    public function getAllowedChildTypes()
    {
        $rtn = [];

        $allowed = $this->getAllowedChildren();

        if (is_array($allowed)) {
            foreach ($allowed as $childId) {
                $rtn[$childId] = Octo\Store::get('ContentType')->getById($childId);
            }
        }

        return $rtn;
    }
}
