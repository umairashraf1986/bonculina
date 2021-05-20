<?php

class __Mustache_f079373c57bcef4f5e5c6ca4d76cde02 extends Mustache_Template
{
    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $buffer = '';
        $newContext = array();

        if ($partial = $this->mustache->loadPartial('admin/settings/table-row')) {
            $buffer .= $partial->renderInternal($context);
        }

        return $buffer;
    }
}
