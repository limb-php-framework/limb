# lmbDispatchedRequest
lmbDispatchedRequest contains requested [lmbService](./lmb_service.md) and [Action](./action.md). lmbDispatchedRequest is created in [lmbRequestDispatcher](./lmb_request_dispatcher.md) and passed to [lmbToolkit](./lmb_toolkit.md) in [lmbRequestDispatchingFilter](./lmb_request_dispatching_filter.md) so that other classes can use lmbDispatchedRequest for their needs.

Some filters where lmbDispatchedRequest is used:

* [lmbCommandProcessingFilter](./lmb_command_processing_filter.md) — executes a command that matches the requested lmbService and Action.
* [ServiceActionExtraFilterChainFilter](./service_action_extra_filter_chain_filter.md) — creates an extra filter chain according to the requested lmbService and Action.
* lmbSimpleACLAccessFilter from SimpleACL package — checks if the user has access to perform requested Action.
