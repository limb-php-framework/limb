# lmbCommandProcessingFilter
lmbCommandProcessingFilter executes a [lmbCommand](./lmb_command.md) according to the requested lmbService and Action.

    class lmbCommandProcessingFilter implements lmbInterceptingFilter
    {
      public function run($filter_chain)
      {
        $dispatched = lmbToolkit :: instance()->getDispatchedRequest();
        if(!is_object($dispatched))
          throw new lmbException('Request is not dispatched yet! lmbDispatchedRequest not found in lmbToolkit!');
 
        $command = $dispatched->getActionCommand();
        $command->perform();
 
        $filter_chain->next();
      }
    }

## UML Static structure
## lmbDispatchedRequest
[lmbDispatchedRequest](./lmb_dispatched_request.md) is stored in [lmbToolkit](./lmb_toolkit.md). [lmbDispatchedRequest](./lmb_dispatched_request.md) is created by [lmbRequestDispatcher](./lmb_request_dispatcher.md) in [lmbRequestDispatchingFilter](./lmb_request_dispatching_filter.md).
