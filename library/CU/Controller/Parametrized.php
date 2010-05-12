<?php
class CU_Controller_Parametrized extends Zend_Controller_Action {
	public function dispatch($action) {
		// Notify helpers of action preDispatch state
		$this->_helper->notifyPreDispatch();

		$this->preDispatch();
		if ($this->getRequest()->isDispatched()) {
		    if (null === $this->_classMethods) {
				$this->_classMethods = get_class_methods($this);
		    }

		    // preDispatch() didn't change the action, so we can continue
		    if ($this->getInvokeArg('useCaseSensitiveActions') || in_array($action, $this->_classMethods)) {
				if ($this->getInvokeArg('useCaseSensitiveActions')) {
				    trigger_error('Using case sensitive actions without word separators is deprecated; please do not rely on this "feature"');
				}

				$this->_callAction($action);
			} else {
				$this->__call($action, array());
		    }
		    $this->postDispatch();
		}

		// whats actually important here is that this action controller is
		// shutting down, regardless of dispatching; notify the helpers of this
		// state
		$this->_helper->notifyPostDispatch();
	}

	protected function _callAction($action) {
		//$mtd = new ReflectionMethod($this, $action);
		//$args = $mtd->getParameters();

		$reflection = Zend_Server_Reflection::reflectClass($this);
		$methods = $reflection->getMethods();
		
		$mtd = null;
		foreach($methods as $m) {
			if($m->getName() == $action)
				$mtd = $m;
		}

		if(!$mtd)
			throw new RuntimeException('Method "' . $action . '" not found');

		$protos = $mtd->getPrototypes();
		$args = $protos[0]->getParameters();
		
		$parameters = array();
		$basicTypes = array('int','float','string','bool');
		foreach($args as $arg) {		
			$name = $arg->getName();
			$param = $this->getRequest()->getParam($name, null);
			$type = $arg->getType();

			if($arg->isOptional() && $param === null) {
				$param = $arg->getDefaultValue();
			}
			elseif($param === null) {
				throw new RuntimeException("Parameter '$name' does not exist");
			}

			if(in_array($type, $basicTypes)) {				
				settype($param, $type);				
			}
			elseif(class_exists($arg->getType()) && get_class($param) != $type) {
				
			}

			$parameters[] = $param;
		}

		call_user_func_array(array($this, $action), $parameters);
	}
}