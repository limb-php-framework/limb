# lmbRequestDispatchingFilter
lmbRequestDispatchingFilter implements lmbInterceptingFilter interface and performs [RequestDispatching](./request_dispatching.md) using some concrete [lmbRequestDispatcher](./lmb_request_dispatcher.md).

[lmbRequestDispatchingFilter](./lmb_request_dispatching_filter.md) has a factory method to create lmbRequestDispatcher object, as well as factory methods to create ServiceRequestExtractor and lmbActionRequestExtractor.

    class lmbRequestDispatchingFilter implements lmbInterceptingFilter
    {
      function run($filter_chain)
      {
        $toolkit = lmbToolkit :: instance();
        $request = $toolkit->getRequest();
 
        $dispatcher = $this->_createRequestDispatcher();
        if(!$dispatched_request = $dispatcher->dispatch($request))
          return;
 
        $toolkit->setDispatchedRequest($dispatched_request);
 
        $filter_chain->next();
      }
 
      protected function _createRequestDispatcher()
      {
        $service_extractor = $this->_createServiceExtractor();
        $action_extractor = $this->_createActionExtractor();
        include_once(LPKG_CORE_DIR . '/src/request/lmbRequestDispatcher.class.php');
        return new lmbRequestDispatcher($service_extractor, $action_extractor);
      } 
 
      protected function _createServiceExtractor() {...}
 
      protected function _createActionExtractor() {...}
      [...]
    }

You can create your own child lmbRequestDispatchingFilter for your application and pass any special [RequestExctrators](./lmb_request_extractor.md) into [lmbRequestDispatcher](./lmb_request_dispatcher.md).

lmbRequestDispatchingFilter puts [lmbDispatchedRequest](./lmb_dispatched_request.md) into [lmbToolkit](./lmb_toolkit.md) in order to make it available for other components of an application.
