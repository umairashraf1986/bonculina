<?php

class __Mustache_cf4db96b2d84debae318f06f3f4d6027 extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';
        $newContext = array();

        $buffer .= $indent . '<div class="wrap">
';
        $buffer .= $indent . '	<h1>';
        $value = $this->resolveValue($context->find('title'), $context, $indent);
        $buffer .= htmlspecialchars($value, 2, 'UTF-8');
        $buffer .= '
';
        $buffer .= $indent . '    	    <a href="https://wetail.se/support/" class="page-title-action" target="_blank">Support</a>
';
        $buffer .= $indent . '    	    <a href="https://docs.wetail.io/woocommerce-pacsoft-unifaun-integration/" class="page-title-action" target="_blank">FAQ</a>
';
        // 'buy' section
        $value = $context->find('buy');
        $buffer .= $this->section2d54a7a1fffc0aa976123874d42e3c98($context, $indent, $value);
        $buffer .= $indent . '     </h1>
';
        $buffer .= $indent . '
';
        $buffer .= $indent . '	<!--<div class="welcome-panel">
';
        $buffer .= $indent . '		<a class="welcome-panel-close" href="#">Dismiss</a>
';
        $buffer .= $indent . '		<h2>Welcome to Unifaun/Pacsoft for WooCommerce!</h2>
';
        $buffer .= $indent . '		<p class="about-description">Some dummy text here.</p>
';
        $buffer .= $indent . '
';
        $buffer .= $indent . '	</div>-->
';
        $buffer .= $indent . '
';
        // 'hasTabs' section
        $value = $context->find('hasTabs');
        $buffer .= $this->section95c618544bbf36b50560645d30d97f9c($context, $indent, $value);
        $buffer .= $indent . '
';
        $buffer .= $indent . '	<form method="post" action="options.php">
';
        $buffer .= $indent . '		';
        $value = $this->resolveValue($context->find('hidden'), $context, $indent);
        $buffer .= $value;
        $buffer .= '
';
        // 'sections' section
        $value = $context->find('sections');
        $buffer .= $this->section875ac618cdf9219d7880ff19b0e18897($context, $indent, $value);
        $buffer .= $indent . '
';
        // 'saveButton' section
        $value = $context->find('saveButton');
        $buffer .= $this->sectionF3d7518a05edec8b8bf99dce18c29959($context, $indent, $value);
        $buffer .= $indent . '	</form>
';
        $buffer .= $indent . '</div>
';

        return $buffer;
    }

    private function section2d54a7a1fffc0aa976123874d42e3c98(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
        if (!is_string($value) && is_callable($value)) {
            $source = '
                <a href="https://wetail.se/service/intergrationer/woocommerce-unifaun/" class="button-primary page-title-action" target="_blank">Order License</a>
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
                
                $buffer .= $indent . '                <a href="https://wetail.se/service/intergrationer/woocommerce-unifaun/" class="button-primary page-title-action" target="_blank">Order License</a>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionF563c69b84c091e04f2424f64d617658(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
        if (!is_string($value) && is_callable($value)) {
            $source = 'nav-tab-active';
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
                
                $buffer .= 'nav-tab-active';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionE51705c307e48fbadc5e7f3e9d14ed39(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
        if (!is_string($value) && is_callable($value)) {
            $source = '
		<a class="nav-tab nav-tab-{{ tab.name }} {{# tab.selected }}nav-tab-active{{/ tab.selected }} {{ tab.class }}" href="options-general.php?page=woocommerce-pacsoft&tab={{ tab.name }}">{{ tab.title }}</a>
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
                
                $buffer .= $indent . '		<a class="nav-tab nav-tab-';
                $value = $this->resolveValue($context->findDot('tab.name'), $context, $indent);
                $buffer .= htmlspecialchars($value, 2, 'UTF-8');
                $buffer .= ' ';
                // 'tab.selected' section
                $value = $context->findDot('tab.selected');
                $buffer .= $this->sectionF563c69b84c091e04f2424f64d617658($context, $indent, $value);
                $buffer .= ' ';
                $value = $this->resolveValue($context->findDot('tab.class'), $context, $indent);
                $buffer .= htmlspecialchars($value, 2, 'UTF-8');
                $buffer .= '" href="options-general.php?page=woocommerce-pacsoft&tab=';
                $value = $this->resolveValue($context->findDot('tab.name'), $context, $indent);
                $buffer .= htmlspecialchars($value, 2, 'UTF-8');
                $buffer .= '">';
                $value = $this->resolveValue($context->findDot('tab.title'), $context, $indent);
                $buffer .= htmlspecialchars($value, 2, 'UTF-8');
                $buffer .= '</a>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section95c618544bbf36b50560645d30d97f9c(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
        if (!is_string($value) && is_callable($value)) {
            $source = '
	<h2 class="nav-tab-wrapper">
		{{# tabs }}
		<a class="nav-tab nav-tab-{{ tab.name }} {{# tab.selected }}nav-tab-active{{/ tab.selected }} {{ tab.class }}" href="options-general.php?page=woocommerce-pacsoft&tab={{ tab.name }}">{{ tab.title }}</a>
		{{/ tabs }}
	</h2>
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
                
                $buffer .= $indent . '	<h2 class="nav-tab-wrapper">
';
                // 'tabs' section
                $value = $context->find('tabs');
                $buffer .= $this->sectionE51705c307e48fbadc5e7f3e9d14ed39($context, $indent, $value);
                $buffer .= $indent . '	</h2>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionBe470298515d99f4233ee908265e5ec7(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
        if (!is_string($value) && is_callable($value)) {
            $source = '
				<h2 class="title">{{ . }}</h2>
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
                
                $buffer .= $indent . '				<h2 class="title">';
                $value = $this->resolveValue($context->last(), $context, $indent);
                $buffer .= htmlspecialchars($value, 2, 'UTF-8');
                $buffer .= '</h2>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section255b20b123ef65708f6c6ce98e49aaf1(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
        if (!is_string($value) && is_callable($value)) {
            $source = '
				<p>{{{ . }}}</p>
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
                
                $buffer .= $indent . '				<p>';
                $value = $this->resolveValue($context->last(), $context, $indent);
                $buffer .= $value;
                $buffer .= '</p>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section34f46cd6a57b61cf940981be70ac2f4e(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
        if (!is_string($value) && is_callable($value)) {
            $source = '
								<input type="text" name="{{ field.name }}" value="{{ field.value }}" class="{{ field.class }}" autocomplete="off" placeholder="{{ field.placeholder }}">
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
                
                $buffer .= $indent . '								<input type="text" name="';
                $value = $this->resolveValue($context->findDot('field.name'), $context, $indent);
                $buffer .= htmlspecialchars($value, 2, 'UTF-8');
                $buffer .= '" value="';
                $value = $this->resolveValue($context->findDot('field.value'), $context, $indent);
                $buffer .= htmlspecialchars($value, 2, 'UTF-8');
                $buffer .= '" class="';
                $value = $this->resolveValue($context->findDot('field.class'), $context, $indent);
                $buffer .= htmlspecialchars($value, 2, 'UTF-8');
                $buffer .= '" autocomplete="off" placeholder="';
                $value = $this->resolveValue($context->findDot('field.placeholder'), $context, $indent);
                $buffer .= htmlspecialchars($value, 2, 'UTF-8');
                $buffer .= '">
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section75fd013c4db39239dc6a5b967d929bc7(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
        if (!is_string($value) && is_callable($value)) {
            $source = '
								<input type="password" name="{{ name }}" value="{{ selected }}" class="{{ class }}" autocomplete="off" placeholder="{{ placeholder }}">
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
                
                $buffer .= $indent . '								<input type="password" name="';
                $value = $this->resolveValue($context->find('name'), $context, $indent);
                $buffer .= htmlspecialchars($value, 2, 'UTF-8');
                $buffer .= '" value="';
                $value = $this->resolveValue($context->find('selected'), $context, $indent);
                $buffer .= htmlspecialchars($value, 2, 'UTF-8');
                $buffer .= '" class="';
                $value = $this->resolveValue($context->find('class'), $context, $indent);
                $buffer .= htmlspecialchars($value, 2, 'UTF-8');
                $buffer .= '" autocomplete="off" placeholder="';
                $value = $this->resolveValue($context->find(' placeholder'), $context, $indent);
                $buffer .= htmlspecialchars($value, 2, 'UTF-8');
                $buffer .= '">
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionC877874b20aed109ed5be9bdc0ef9c49(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
        if (!is_string($value) && is_callable($value)) {
            $source = 'selected="selected"';
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
                
                $buffer .= 'selected="selected"';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section376d4fe79e6ae3926155e8ec548d2e76(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
        if (!is_string($value) && is_callable($value)) {
            $source = '
										<option value="{{ option.value }}" {{# option.selected }}selected="selected"{{/ option.selected }}>{{ option.label }}</option>
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
                
                $buffer .= $indent . '										<option value="';
                $value = $this->resolveValue($context->findDot('option.value'), $context, $indent);
                $buffer .= htmlspecialchars($value, 2, 'UTF-8');
                $buffer .= '" ';
                // 'option.selected' section
                $value = $context->findDot('option.selected');
                $buffer .= $this->sectionC877874b20aed109ed5be9bdc0ef9c49($context, $indent, $value);
                $buffer .= '>';
                $value = $this->resolveValue($context->findDot('option.label'), $context, $indent);
                $buffer .= htmlspecialchars($value, 2, 'UTF-8');
                $buffer .= '</option>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionD901be0bc62bbb2be45c27871459e2af(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
        if (!is_string($value) && is_callable($value)) {
            $source = '
								<select name="{{ field.name }}" class="{{ field.class }}">
									{{# field.options }}
										<option value="{{ option.value }}" {{# option.selected }}selected="selected"{{/ option.selected }}>{{ option.label }}</option>
									{{/ field.options }}
								</select>
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
                
                $buffer .= $indent . '								<select name="';
                $value = $this->resolveValue($context->findDot('field.name'), $context, $indent);
                $buffer .= htmlspecialchars($value, 2, 'UTF-8');
                $buffer .= '" class="';
                $value = $this->resolveValue($context->findDot('field.class'), $context, $indent);
                $buffer .= htmlspecialchars($value, 2, 'UTF-8');
                $buffer .= '">
';
                // 'field.options' section
                $value = $context->findDot('field.options');
                $buffer .= $this->section376d4fe79e6ae3926155e8ec548d2e76($context, $indent, $value);
                $buffer .= $indent . '								</select>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionE6c044fe8710d3502dd5cb9686c32f3f(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
        if (!is_string($value) && is_callable($value)) {
            $source = 'checked="checked"';
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
                
                $buffer .= 'checked="checked"';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section029f7a57e9a14cb8d6f8b82c34a0ab21(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
        if (!is_string($value) && is_callable($value)) {
            $source = '
									<p>
										<label>
											<input type="radio" name="{{ field.name }}" value="{{ value }}" {{# selected }}checked="checked"{{/ selected }} class="{{ class }}"> {{{ label }}}
										</label>
									</p>
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
                
                $buffer .= $indent . '									<p>
';
                $buffer .= $indent . '										<label>
';
                $buffer .= $indent . '											<input type="radio" name="';
                $value = $this->resolveValue($context->findDot('field.name'), $context, $indent);
                $buffer .= htmlspecialchars($value, 2, 'UTF-8');
                $buffer .= '" value="';
                $value = $this->resolveValue($context->find('value'), $context, $indent);
                $buffer .= htmlspecialchars($value, 2, 'UTF-8');
                $buffer .= '" ';
                // 'selected' section
                $value = $context->find('selected');
                $buffer .= $this->sectionE6c044fe8710d3502dd5cb9686c32f3f($context, $indent, $value);
                $buffer .= ' class="';
                $value = $this->resolveValue($context->find(' class'), $context, $indent);
                $buffer .= htmlspecialchars($value, 2, 'UTF-8');
                $buffer .= '"> ';
                $value = $this->resolveValue($context->find('label'), $context, $indent);
                $buffer .= $value;
                $buffer .= '
';
                $buffer .= $indent . '										</label>
';
                $buffer .= $indent . '									</p>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionD11865e1e6e9be012506f56fbce1e52f(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
        if (!is_string($value) && is_callable($value)) {
            $source = '
								{{# field.options }}
									<p>
										<label>
											<input type="radio" name="{{ field.name }}" value="{{ value }}" {{# selected }}checked="checked"{{/ selected }} class="{{ class }}"> {{{ label }}}
										</label>
									</p>
								{{/ field.options }}
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
                
                // 'field.options' section
                $value = $context->findDot('field.options');
                $buffer .= $this->section029f7a57e9a14cb8d6f8b82c34a0ab21($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionB0391a9efc2252f51dba50e43c15e56b(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
        if (!is_string($value) && is_callable($value)) {
            $source = '
								<p>
									<input type="hidden" name="{{ field.name }}" value="0">
									<label>
										<input type="checkbox" name="{{ field.name }}" value="1" {{# field.checked }}checked="checked"{{/ field.checked }} class="{{ class }}"> {{{ field.label }}}
									</label>
								</p>
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
                
                $buffer .= $indent . '								<p>
';
                $buffer .= $indent . '									<input type="hidden" name="';
                $value = $this->resolveValue($context->findDot('field.name'), $context, $indent);
                $buffer .= htmlspecialchars($value, 2, 'UTF-8');
                $buffer .= '" value="0">
';
                $buffer .= $indent . '									<label>
';
                $buffer .= $indent . '										<input type="checkbox" name="';
                $value = $this->resolveValue($context->findDot('field.name'), $context, $indent);
                $buffer .= htmlspecialchars($value, 2, 'UTF-8');
                $buffer .= '" value="1" ';
                // 'field.checked' section
                $value = $context->findDot('field.checked');
                $buffer .= $this->sectionE6c044fe8710d3502dd5cb9686c32f3f($context, $indent, $value);
                $buffer .= ' class="';
                $value = $this->resolveValue($context->find('class'), $context, $indent);
                $buffer .= htmlspecialchars($value, 2, 'UTF-8');
                $buffer .= '"> ';
                $value = $this->resolveValue($context->findDot('field.label'), $context, $indent);
                $buffer .= $value;
                $buffer .= '
';
                $buffer .= $indent . '									</label>
';
                $buffer .= $indent . '								</p>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionEb70b9d1c4d62c905c03f3b22c35388b(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
        if (!is_string($value) && is_callable($value)) {
            $source = '
										<span class="description">{{{ . }}}</span><br>
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
                
                $buffer .= $indent . '										<span class="description">';
                $value = $this->resolveValue($context->findDot(' .'), $context, $indent);
                $buffer .= $value;
                $buffer .= '</span><br>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section9f0e84544298fe6aaf9ea982410ebcc5(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
        if (!is_string($value) && is_callable($value)) {
            $source = '
										<label>
											<input type="hidden" name="{{ option.name }}" value="0">
											<input type="checkbox" name="{{ option.name }}" value="1" {{# option.checked }}checked="checked"{{/ option.checked }} class="{{ option.class }}"> {{{ option.label }}}
										</label><br>
										{{# option.description }}
										<span class="description">{{{ . }}}</span><br>
										{{/ option.description }}
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
                
                $buffer .= $indent . '										<label>
';
                $buffer .= $indent . '											<input type="hidden" name="';
                $value = $this->resolveValue($context->findDot('option.name'), $context, $indent);
                $buffer .= htmlspecialchars($value, 2, 'UTF-8');
                $buffer .= '" value="0">
';
                $buffer .= $indent . '											<input type="checkbox" name="';
                $value = $this->resolveValue($context->findDot('option.name'), $context, $indent);
                $buffer .= htmlspecialchars($value, 2, 'UTF-8');
                $buffer .= '" value="1" ';
                // 'option.checked' section
                $value = $context->findDot('option.checked');
                $buffer .= $this->sectionE6c044fe8710d3502dd5cb9686c32f3f($context, $indent, $value);
                $buffer .= ' class="';
                $value = $this->resolveValue($context->findDot('option.class'), $context, $indent);
                $buffer .= htmlspecialchars($value, 2, 'UTF-8');
                $buffer .= '"> ';
                $value = $this->resolveValue($context->findDot('option.label'), $context, $indent);
                $buffer .= $value;
                $buffer .= '
';
                $buffer .= $indent . '										</label><br>
';
                // 'option.description' section
                $value = $context->findDot('option.description');
                $buffer .= $this->sectionEb70b9d1c4d62c905c03f3b22c35388b($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section8c1773655ec981e1a54f520151419487(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
        if (!is_string($value) && is_callable($value)) {
            $source = '
								<fieldset>
									<legend class="screen-reader-text">
										<span>{{ title }}</span>
									</legend>

									{{# field.options }}
										<label>
											<input type="hidden" name="{{ option.name }}" value="0">
											<input type="checkbox" name="{{ option.name }}" value="1" {{# option.checked }}checked="checked"{{/ option.checked }} class="{{ option.class }}"> {{{ option.label }}}
										</label><br>
										{{# option.description }}
										<span class="description">{{{ . }}}</span><br>
										{{/ option.description }}
									{{/ field.options }}
								</fieldset>
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
                
                $buffer .= $indent . '								<fieldset>
';
                $buffer .= $indent . '									<legend class="screen-reader-text">
';
                $buffer .= $indent . '										<span>';
                $value = $this->resolveValue($context->find('title'), $context, $indent);
                $buffer .= htmlspecialchars($value, 2, 'UTF-8');
                $buffer .= '</span>
';
                $buffer .= $indent . '									</legend>
';
                $buffer .= $indent . '
';
                // 'field.options' section
                $value = $context->findDot('field.options');
                $buffer .= $this->section9f0e84544298fe6aaf9ea982410ebcc5($context, $indent, $value);
                $buffer .= $indent . '								</fieldset>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section7f100f40c26f275335f9db8f9d6dbdf8(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
        if (!is_string($value) && is_callable($value)) {
            $source = '
											<th class="column-{{ column.name }}" style="text-align: center;">{{{ column.title }}}</th>
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
                
                $buffer .= $indent . '											<th class="column-';
                $value = $this->resolveValue($context->findDot('column.name'), $context, $indent);
                $buffer .= htmlspecialchars($value, 2, 'UTF-8');
                $buffer .= '" style="text-align: center;">';
                $value = $this->resolveValue($context->findDot('column.title'), $context, $indent);
                $buffer .= $value;
                $buffer .= '</th>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section43f2e619601287a484cc451d3f70a4c1(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
        if (!is_string($value) && is_callable($value)) {
            $source = 'id="{{ . }}"';
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
                
                $buffer .= 'id="';
                $value = $this->resolveValue($context->last(), $context, $indent);
                $buffer .= htmlspecialchars($value, 2, 'UTF-8');
                $buffer .= '"';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionB798c5c911c7d0fa83778ddf58d5ba51(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
        if (!is_string($value) && is_callable($value)) {
            $source = '
											{{> admin/settings/table-row }}
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
                
                if ($partial = $this->mustache->loadPartial('admin/settings/table-row')) {
                    $buffer .= $partial->renderInternal($context, $indent . '											');
                }
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section87ab49dc967b3a1ddf792e4345d47b13(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
        if (!is_string($value) && is_callable($value)) {
            $source = '
								<p><a href="#" class="button {{ table.addRowButtonClass }}">Add row</a></p>
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
                
                $buffer .= $indent . '								<p><a href="#" class="button ';
                $value = $this->resolveValue($context->findDot('table.addRowButtonClass'), $context, $indent);
                $buffer .= htmlspecialchars($value, 2, 'UTF-8');
                $buffer .= '">Add row</a></p>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionD47865c1930ebcdcba8e22dcbe4336ab(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
        if (!is_string($value) && is_callable($value)) {
            $source = '
								<table class="wp-list-table widefat fixed striped posts {{ class }}">
									<thead>
										<tr>
											{{# table.columns }}
											<th class="column-{{ column.name }}" style="text-align: center;">{{{ column.title }}}</th>
											{{/ table.columns }}
										</tr>
									</thead>
									<tbody {{# table.id }}id="{{ . }}"{{/ table.id }}>
										{{# table.rows }}
											{{> admin/settings/table-row }}
										{{/ table.rows }}
									</tbody>
								</table>

								{{# table.addRowButton }}
								<p><a href="#" class="button {{ table.addRowButtonClass }}">Add row</a></p>
								{{/ table.addRowButton }}
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
                
                $buffer .= $indent . '								<table class="wp-list-table widefat fixed striped posts ';
                $value = $this->resolveValue($context->find('class'), $context, $indent);
                $buffer .= htmlspecialchars($value, 2, 'UTF-8');
                $buffer .= '">
';
                $buffer .= $indent . '									<thead>
';
                $buffer .= $indent . '										<tr>
';
                // 'table.columns' section
                $value = $context->findDot('table.columns');
                $buffer .= $this->section7f100f40c26f275335f9db8f9d6dbdf8($context, $indent, $value);
                $buffer .= $indent . '										</tr>
';
                $buffer .= $indent . '									</thead>
';
                $buffer .= $indent . '									<tbody ';
                // 'table.id' section
                $value = $context->findDot('table.id');
                $buffer .= $this->section43f2e619601287a484cc451d3f70a4c1($context, $indent, $value);
                $buffer .= '>
';
                // 'table.rows' section
                $value = $context->findDot('table.rows');
                $buffer .= $this->sectionB798c5c911c7d0fa83778ddf58d5ba51($context, $indent, $value);
                $buffer .= $indent . '									</tbody>
';
                $buffer .= $indent . '								</table>
';
                $buffer .= $indent . '
';
                // 'table.addRowButton' section
                $value = $context->findDot('table.addRowButton');
                $buffer .= $this->section87ab49dc967b3a1ddf792e4345d47b13($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section3b799db863d954282f9e20a893eac292(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
        if (!is_string($value) && is_callable($value)) {
            $source = '
								{{{ . }}}
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
                
                $buffer .= $indent . '								';
                $value = $this->resolveValue($context->last(), $context, $indent);
                $buffer .= $value;
                $buffer .= '
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section23255244884f1b459c05e02c63e3cc2a(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
        if (!is_string($value) && is_callable($value)) {
            $source = 'data-{{ key }}="{{ value }}" ';
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
                
                $buffer .= 'data-';
                $value = $this->resolveValue($context->find('key'), $context, $indent);
                $buffer .= htmlspecialchars($value, 2, 'UTF-8');
                $buffer .= '="';
                $value = $this->resolveValue($context->find('value'), $context, $indent);
                $buffer .= htmlspecialchars($value, 2, 'UTF-8');
                $buffer .= '" ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionB4df1ff59428e19a384e1e7224e29acf(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
        if (!is_string($value) && is_callable($value)) {
            $source = '
							<a href="#" class="button button-primary button-hero pacsoft-admin-action" {{# field.data }}data-{{ key }}="{{ value }}" {{/ field.data }}style="text-align: center; width: 240px">{{ field.button.text }}</a> <span class="spinner pacsoft-spinner hero"></span>
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
                
                $buffer .= $indent . '							<a href="#" class="button button-primary button-hero pacsoft-admin-action" ';
                // 'field.data' section
                $value = $context->findDot('field.data');
                $buffer .= $this->section23255244884f1b459c05e02c63e3cc2a($context, $indent, $value);
                $buffer .= 'style="text-align: center; width: 240px">';
                $value = $this->resolveValue($context->findDot('field.button.text'), $context, $indent);
                $buffer .= htmlspecialchars($value, 2, 'UTF-8');
                $buffer .= '</a> <span class="spinner pacsoft-spinner hero"></span>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section5ad74eb64b5bd91cc7da79e6433e64e3(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
        if (!is_string($value) && is_callable($value)) {
            $source = '
							{{{ . }}}
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
                
                $buffer .= $indent . '							';
                $value = $this->resolveValue($context->last(), $context, $indent);
                $buffer .= $value;
                $buffer .= '
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section9b3c93bf5679956bc04e500bdfb01d3d(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
        if (!is_string($value) && is_callable($value)) {
            $source = '
								<p class="description">{{{ . }}}</p>
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
                
                $buffer .= $indent . '								<p class="description">';
                $value = $this->resolveValue($context->last(), $context, $indent);
                $buffer .= $value;
                $buffer .= '</p>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section4eac74908e815dce085efeceb22c3d3f(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
        if (!is_string($value) && is_callable($value)) {
            $source = '
					<tr>
						<th scope="row">{{ field.title }}</th>
						<td>
							{{# field.text }}
								<input type="text" name="{{ field.name }}" value="{{ field.value }}" class="{{ field.class }}" autocomplete="off" placeholder="{{ field.placeholder }}">
							{{/ field.text }}

							{{# field.password }}
								<input type="password" name="{{ name }}" value="{{ selected }}" class="{{ class }}" autocomplete="off" placeholder="{{ placeholder }}">
							{{/ field.password }}

							{{# field.dropdown }}
								<select name="{{ field.name }}" class="{{ field.class }}">
									{{# field.options }}
										<option value="{{ option.value }}" {{# option.selected }}selected="selected"{{/ option.selected }}>{{ option.label }}</option>
									{{/ field.options }}
								</select>
							{{/ field.dropdown }}

							{{# field.radio }}
								{{# field.options }}
									<p>
										<label>
											<input type="radio" name="{{ field.name }}" value="{{ value }}" {{# selected }}checked="checked"{{/ selected }} class="{{ class }}"> {{{ label }}}
										</label>
									</p>
								{{/ field.options }}
							{{/ field.radio }}

							{{# field.checkbox }}
								<p>
									<input type="hidden" name="{{ field.name }}" value="0">
									<label>
										<input type="checkbox" name="{{ field.name }}" value="1" {{# field.checked }}checked="checked"{{/ field.checked }} class="{{ class }}"> {{{ field.label }}}
									</label>
								</p>
							{{/ field.checkbox }}

							{{# field.checkboxes }}
								<fieldset>
									<legend class="screen-reader-text">
										<span>{{ title }}</span>
									</legend>

									{{# field.options }}
										<label>
											<input type="hidden" name="{{ option.name }}" value="0">
											<input type="checkbox" name="{{ option.name }}" value="1" {{# option.checked }}checked="checked"{{/ option.checked }} class="{{ option.class }}"> {{{ option.label }}}
										</label><br>
										{{# option.description }}
										<span class="description">{{{ . }}}</span><br>
										{{/ option.description }}
									{{/ field.options }}
								</fieldset>
							{{/ field.checkboxes }}

							{{# field.table }}
								<table class="wp-list-table widefat fixed striped posts {{ class }}">
									<thead>
										<tr>
											{{# table.columns }}
											<th class="column-{{ column.name }}" style="text-align: center;">{{{ column.title }}}</th>
											{{/ table.columns }}
										</tr>
									</thead>
									<tbody {{# table.id }}id="{{ . }}"{{/ table.id }}>
										{{# table.rows }}
											{{> admin/settings/table-row }}
										{{/ table.rows }}
									</tbody>
								</table>

								{{# table.addRowButton }}
								<p><a href="#" class="button {{ table.addRowButtonClass }}">Add row</a></p>
								{{/ table.addRowButton }}
							{{/ field.table }}

							{{# field.html }}
								{{{ . }}}
							{{/ field.html }}

							{{# field.button }}
							<a href="#" class="button button-primary button-hero pacsoft-admin-action" {{# field.data }}data-{{ key }}="{{ value }}" {{/ field.data }}style="text-align: center; width: 240px">{{ field.button.text }}</a> <span class="spinner pacsoft-spinner hero"></span>
							{{/ field.button }}

							{{# field.after }}
							{{{ . }}}
							{{/ field.after }}

							{{# field.description }}
								<p class="description">{{{ . }}}</p>
							{{/ field.description }}
						</td>
					</tr>
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
                
                $buffer .= $indent . '					<tr>
';
                $buffer .= $indent . '						<th scope="row">';
                $value = $this->resolveValue($context->findDot('field.title'), $context, $indent);
                $buffer .= htmlspecialchars($value, 2, 'UTF-8');
                $buffer .= '</th>
';
                $buffer .= $indent . '						<td>
';
                // 'field.text' section
                $value = $context->findDot('field.text');
                $buffer .= $this->section34f46cd6a57b61cf940981be70ac2f4e($context, $indent, $value);
                $buffer .= $indent . '
';
                // 'field.password' section
                $value = $context->findDot('field.password');
                $buffer .= $this->section75fd013c4db39239dc6a5b967d929bc7($context, $indent, $value);
                $buffer .= $indent . '
';
                // 'field.dropdown' section
                $value = $context->findDot('field.dropdown');
                $buffer .= $this->sectionD901be0bc62bbb2be45c27871459e2af($context, $indent, $value);
                $buffer .= $indent . '
';
                // 'field.radio' section
                $value = $context->findDot('field.radio');
                $buffer .= $this->sectionD11865e1e6e9be012506f56fbce1e52f($context, $indent, $value);
                $buffer .= $indent . '
';
                // 'field.checkbox' section
                $value = $context->findDot('field.checkbox');
                $buffer .= $this->sectionB0391a9efc2252f51dba50e43c15e56b($context, $indent, $value);
                $buffer .= $indent . '
';
                // 'field.checkboxes' section
                $value = $context->findDot('field.checkboxes');
                $buffer .= $this->section8c1773655ec981e1a54f520151419487($context, $indent, $value);
                $buffer .= $indent . '
';
                // 'field.table' section
                $value = $context->findDot('field.table');
                $buffer .= $this->sectionD47865c1930ebcdcba8e22dcbe4336ab($context, $indent, $value);
                $buffer .= $indent . '
';
                // 'field.html' section
                $value = $context->findDot('field.html');
                $buffer .= $this->section3b799db863d954282f9e20a893eac292($context, $indent, $value);
                $buffer .= $indent . '
';
                // 'field.button' section
                $value = $context->findDot('field.button');
                $buffer .= $this->sectionB4df1ff59428e19a384e1e7224e29acf($context, $indent, $value);
                $buffer .= $indent . '
';
                // 'field.after' section
                $value = $context->findDot('field.after');
                $buffer .= $this->section5ad74eb64b5bd91cc7da79e6433e64e3($context, $indent, $value);
                $buffer .= $indent . '
';
                // 'field.description' section
                $value = $context->findDot('field.description');
                $buffer .= $this->section9b3c93bf5679956bc04e500bdfb01d3d($context, $indent, $value);
                $buffer .= $indent . '						</td>
';
                $buffer .= $indent . '					</tr>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section875ac618cdf9219d7880ff19b0e18897(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
        if (!is_string($value) && is_callable($value)) {
            $source = '
			{{# section.title }}
				<h2 class="title">{{ . }}</h2>
			{{/ section.title }}

			{{# section.description }}
				<p>{{{ . }}}</p>
			{{/ section.description }}

			<table class="form-table">
				<tbody>
					{{# section.fields }}
					<tr>
						<th scope="row">{{ field.title }}</th>
						<td>
							{{# field.text }}
								<input type="text" name="{{ field.name }}" value="{{ field.value }}" class="{{ field.class }}" autocomplete="off" placeholder="{{ field.placeholder }}">
							{{/ field.text }}

							{{# field.password }}
								<input type="password" name="{{ name }}" value="{{ selected }}" class="{{ class }}" autocomplete="off" placeholder="{{ placeholder }}">
							{{/ field.password }}

							{{# field.dropdown }}
								<select name="{{ field.name }}" class="{{ field.class }}">
									{{# field.options }}
										<option value="{{ option.value }}" {{# option.selected }}selected="selected"{{/ option.selected }}>{{ option.label }}</option>
									{{/ field.options }}
								</select>
							{{/ field.dropdown }}

							{{# field.radio }}
								{{# field.options }}
									<p>
										<label>
											<input type="radio" name="{{ field.name }}" value="{{ value }}" {{# selected }}checked="checked"{{/ selected }} class="{{ class }}"> {{{ label }}}
										</label>
									</p>
								{{/ field.options }}
							{{/ field.radio }}

							{{# field.checkbox }}
								<p>
									<input type="hidden" name="{{ field.name }}" value="0">
									<label>
										<input type="checkbox" name="{{ field.name }}" value="1" {{# field.checked }}checked="checked"{{/ field.checked }} class="{{ class }}"> {{{ field.label }}}
									</label>
								</p>
							{{/ field.checkbox }}

							{{# field.checkboxes }}
								<fieldset>
									<legend class="screen-reader-text">
										<span>{{ title }}</span>
									</legend>

									{{# field.options }}
										<label>
											<input type="hidden" name="{{ option.name }}" value="0">
											<input type="checkbox" name="{{ option.name }}" value="1" {{# option.checked }}checked="checked"{{/ option.checked }} class="{{ option.class }}"> {{{ option.label }}}
										</label><br>
										{{# option.description }}
										<span class="description">{{{ . }}}</span><br>
										{{/ option.description }}
									{{/ field.options }}
								</fieldset>
							{{/ field.checkboxes }}

							{{# field.table }}
								<table class="wp-list-table widefat fixed striped posts {{ class }}">
									<thead>
										<tr>
											{{# table.columns }}
											<th class="column-{{ column.name }}" style="text-align: center;">{{{ column.title }}}</th>
											{{/ table.columns }}
										</tr>
									</thead>
									<tbody {{# table.id }}id="{{ . }}"{{/ table.id }}>
										{{# table.rows }}
											{{> admin/settings/table-row }}
										{{/ table.rows }}
									</tbody>
								</table>

								{{# table.addRowButton }}
								<p><a href="#" class="button {{ table.addRowButtonClass }}">Add row</a></p>
								{{/ table.addRowButton }}
							{{/ field.table }}

							{{# field.html }}
								{{{ . }}}
							{{/ field.html }}

							{{# field.button }}
							<a href="#" class="button button-primary button-hero pacsoft-admin-action" {{# field.data }}data-{{ key }}="{{ value }}" {{/ field.data }}style="text-align: center; width: 240px">{{ field.button.text }}</a> <span class="spinner pacsoft-spinner hero"></span>
							{{/ field.button }}

							{{# field.after }}
							{{{ . }}}
							{{/ field.after }}

							{{# field.description }}
								<p class="description">{{{ . }}}</p>
							{{/ field.description }}
						</td>
					</tr>
					{{/ section.fields }}
				</tbody>
			</table>
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
                
                // 'section.title' section
                $value = $context->findDot('section.title');
                $buffer .= $this->sectionBe470298515d99f4233ee908265e5ec7($context, $indent, $value);
                $buffer .= $indent . '
';
                // 'section.description' section
                $value = $context->findDot('section.description');
                $buffer .= $this->section255b20b123ef65708f6c6ce98e49aaf1($context, $indent, $value);
                $buffer .= $indent . '
';
                $buffer .= $indent . '			<table class="form-table">
';
                $buffer .= $indent . '				<tbody>
';
                // 'section.fields' section
                $value = $context->findDot('section.fields');
                $buffer .= $this->section4eac74908e815dce085efeceb22c3d3f($context, $indent, $value);
                $buffer .= $indent . '				</tbody>
';
                $buffer .= $indent . '			</table>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionD570f4ad28487d62cb367542fec6bbc1(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
        if (!is_string($value) && is_callable($value)) {
            $source = 'Save changes';
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
                
                $buffer .= 'Save changes';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionF3d7518a05edec8b8bf99dce18c29959(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
        if (!is_string($value) && is_callable($value)) {
            $source = '
		<p class="submit">
			<button class="button-primary">{{# i18n }}Save changes{{/ i18n }}</button>
		</p>
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
                
                $buffer .= $indent . '		<p class="submit">
';
                $buffer .= $indent . '			<button class="button-primary">';
                // 'i18n' section
                $value = $context->find('i18n');
                $buffer .= $this->sectionD570f4ad28487d62cb367542fec6bbc1($context, $indent, $value);
                $buffer .= '</button>
';
                $buffer .= $indent . '		</p>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

}
