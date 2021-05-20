<?php

class __Mustache_3d178fb78a9348fa9fe8ae24516b663b extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';
        $newContext = array();

        $buffer .= $indent . '<a href="';
        $value = $this->resolveValue($context->findDot(' syncButton.href'), $context, $indent);
        $buffer .= htmlspecialchars($value, 2, 'UTF-8');
        $buffer .= '" class="button pacsoft-button pacsoft-icon-sync syncOrderToPacsoft" data-order="';
        $value = $this->resolveValue($context->find('orderId'), $context, $indent);
        $buffer .= htmlspecialchars($value, 2, 'UTF-8');
        $buffer .= '" data-service="';
        $value = $this->resolveValue($context->find('serviceId'), $context, $indent);
        $buffer .= htmlspecialchars($value, 2, 'UTF-8');
        $buffer .= '" title="';
        $value = $this->resolveValue($context->findDot('syncButton.title'), $context, $indent);
        $buffer .= htmlspecialchars($value, 2, 'UTF-8');
        $buffer .= '" ></a>
';
        $buffer .= $indent . '<a href="';
        $value = $this->resolveValue($context->findDot('printButton.href'), $context, $indent);
        $buffer .= htmlspecialchars($value, 2, 'UTF-8');
        $buffer .= '" class="button pacsoft-button pacsoft-icon-print printPacsoftOrder" data-order-id="';
        $value = $this->resolveValue($context->find('orderId'), $context, $indent);
        $buffer .= htmlspecialchars($value, 2, 'UTF-8');
        $buffer .= '" data-nonce="';
        $value = $this->resolveValue($context->find('nonce'), $context, $indent);
        $buffer .= htmlspecialchars($value, 2, 'UTF-8');
        $buffer .= '" title="';
        $value = $this->resolveValue($context->findDot('printButton.title'), $context, $indent);
        $buffer .= htmlspecialchars($value, 2, 'UTF-8');
        $buffer .= '"></a>
';
        $buffer .= $indent . '<span class="pacsoft-status pacsoft-icon-';
        // 'isSynced' section
        $value = $context->find('isSynced');
        $buffer .= $this->section602649636b0fa71647501d8da7e56a70($context, $indent, $value);
        // 'isSynced' inverted section
        $value = $context->find('isSynced');
        if (empty($value)) {
            
            $buffer .= 'cross';
        }
        $buffer .= '"></span>
';
        $buffer .= $indent . '<span class="spinner pacsoft-spinner"></span>';

        return $buffer;
    }

    private function section602649636b0fa71647501d8da7e56a70(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
        if (!is_string($value) && is_callable($value)) {
            $source = 'tick';
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
                
                $buffer .= 'tick';
                $context->pop();
            }
        }
    
        return $buffer;
    }

}
