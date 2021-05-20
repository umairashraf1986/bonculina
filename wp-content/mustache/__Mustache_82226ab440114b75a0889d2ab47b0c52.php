<?php

class __Mustache_82226ab440114b75a0889d2ab47b0c52 extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';
        $newContext = array();

        $buffer .= $indent . '<div id="pacsoft-sync-options-dialog" hidden>
';
        $buffer .= $indent . '	<p class="selected-service-indicator">
';
        $buffer .= $indent . '		<span>';
        $value = $this->resolveValue($context->findDot('i18n.selectedServiceIndicator'), $context, $indent);
        $buffer .= htmlspecialchars($value, 2, 'UTF-8');
        $buffer .= '</span>
';
        $buffer .= $indent . '		<span class="selected-service">';
        $value = $this->resolveValue($context->findDot('i18n.noServiceSelected'), $context, $indent);
        $buffer .= htmlspecialchars($value, 2, 'UTF-8');
        $buffer .= '</span>
';
        $buffer .= $indent . '	</p>
';
        $buffer .= $indent . '
';
        $buffer .= $indent . '	<table class="form-table">
';
        $buffer .= $indent . '		<tr valign="top" hidden>
';
        $buffer .= $indent . '			<th scope="row">';
        $value = $this->resolveValue($context->findDot('i18n.selectPacsoftService'), $context, $indent);
        $buffer .= htmlspecialchars($value, 2, 'UTF-8');
        $buffer .= '</th>
';
        $buffer .= $indent . '			<td>
';
        $buffer .= $indent . '				<select class="pacsoft-services">
';
        $buffer .= $indent . '					<option value=""></option>
';
        // 'services' section
        $value = $context->find('services');
        $buffer .= $this->sectionBb2841ed22527412dc03b9733efa519e($context, $indent, $value);
        $buffer .= $indent . '				</select>
';
        $buffer .= $indent . '			</td>
';
        $buffer .= $indent . '		</tr>
';
        $buffer .= $indent . '		<tr valign="form">
';
        $buffer .= $indent . '			<th scope="row">';
        $value = $this->resolveValue($context->findDot('i18n.selectPacsoftService'), $context, $indent);
        $buffer .= htmlspecialchars($value, 2, 'UTF-8');
        $buffer .= '</th>
';
        $buffer .= $indent . '			<td>
';
        $buffer .= $indent . '				<input class="filter" placeholder="';
        $value = $this->resolveValue($context->findDot('i18n.placeHolderSearchServices'), $context, $indent);
        $buffer .= htmlspecialchars($value, 2, 'UTF-8');
        $buffer .= '" type="text"/>
';
        $buffer .= $indent . '			</td>
';
        $buffer .= $indent . '			<td>
';
        $buffer .= $indent . '				<p class="submit">
';
        $buffer .= $indent . '					<a href="#" class="button syncPacsoftOrderWithOptions">';
        $value = $this->resolveValue($context->findDot('i18n.syncOrder'), $context, $indent);
        $buffer .= htmlspecialchars($value, 2, 'UTF-8');
        $buffer .= '</a>
';
        $buffer .= $indent . '				</p>
';
        $buffer .= $indent . '			</td>
';
        $buffer .= $indent . '		</tr>
';
        $buffer .= $indent . '	</table>
';
        $buffer .= $indent . '
';
        $buffer .= $indent . '	<ul class="services-to-filter">
';
        // 'services' section
        $value = $context->find('services');
        $buffer .= $this->section4f3c9f942903ea7a745f20e5e5411d07($context, $indent, $value);
        $buffer .= $indent . '	</ul>
';
        $buffer .= $indent . '</div>';

        return $buffer;
    }

    private function sectionBb2841ed22527412dc03b9733efa519e(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
        if (!is_string($value) && is_callable($value)) {
            $source = '
					<option value="{{ code }}" data-woocommerce-pacsoft-service-base-country="{{ country }}">{{ title }}</option>
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
                
                $buffer .= $indent . '					<option value="';
                $value = $this->resolveValue($context->find('code'), $context, $indent);
                $buffer .= htmlspecialchars($value, 2, 'UTF-8');
                $buffer .= '" data-woocommerce-pacsoft-service-base-country="';
                $value = $this->resolveValue($context->find('country'), $context, $indent);
                $buffer .= htmlspecialchars($value, 2, 'UTF-8');
                $buffer .= '">';
                $value = $this->resolveValue($context->find('title'), $context, $indent);
                $buffer .= htmlspecialchars($value, 2, 'UTF-8');
                $buffer .= '</option>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section4f3c9f942903ea7a745f20e5e5411d07(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
        if (!is_string($value) && is_callable($value)) {
            $source = '
		<li value="{{ code }}" data-service="{{ title }}" data-woocommerce-pacsoft-service-base-country="{{ country }}" hidden>{{ title }}</li>
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
                
                $buffer .= $indent . '		<li value="';
                $value = $this->resolveValue($context->find('code'), $context, $indent);
                $buffer .= htmlspecialchars($value, 2, 'UTF-8');
                $buffer .= '" data-service="';
                $value = $this->resolveValue($context->find('title'), $context, $indent);
                $buffer .= htmlspecialchars($value, 2, 'UTF-8');
                $buffer .= '" data-woocommerce-pacsoft-service-base-country="';
                $value = $this->resolveValue($context->find('country'), $context, $indent);
                $buffer .= htmlspecialchars($value, 2, 'UTF-8');
                $buffer .= '" hidden>';
                $value = $this->resolveValue($context->find('title'), $context, $indent);
                $buffer .= htmlspecialchars($value, 2, 'UTF-8');
                $buffer .= '</li>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

}
