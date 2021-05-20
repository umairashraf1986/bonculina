<?php

class __Mustache_9492c3e68f01ae42fa22b960b79edb7a extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';
        $newContext = array();

        $buffer .= $indent . '<tr>
';
        // 'columns' section
        $value = $context->find('columns');
        $buffer .= $this->section024a83da24e2888c35cdd146e0f12010($context, $indent, $value);
        $buffer .= $indent . '</tr>
';

        return $buffer;
    }

    private function section024a83da24e2888c35cdd146e0f12010(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
        if (!is_string($value) && is_callable($value)) {
            $source = '
	<td class="column-{{ column.name }}">{{{ column.content }}}</td>
	';
            $result = call_user_func($value, $source, $this->lambdaHelper);
            if (strpos($result, '{{') === false) {
                $buffer .= $result;
            } else {
                $buffer .= $this->mustache
                    ->loadLambda((string) $result)
                    ->renderInternal($context);
            }
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '	<td class="column-';
                $value = $this->resolveValue($context->findDot('column.name'), $context, $indent);
                $buffer .= htmlspecialchars($value, 2, 'UTF-8');
                $buffer .= '">';
                $value = $this->resolveValue($context->findDot('column.content'), $context, $indent);
                $buffer .= $value;
                $buffer .= '</td>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

}
