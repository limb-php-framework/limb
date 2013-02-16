# lmbRequestDispatcher
lmbRequestDispatcher is a class that performs the main part of [RequestDispatching](./request_dispatching.md). lmbRequestDispatcher uses [lmbRequestExtractors](./lmb_request_extractor.md) to determine current [lmbService](./lmb_service.md) and Action. lmbRequestDispatcher :: dispatch($request) method returns [lmbDispatchedRequest](./lmb_dispatched_request.md).

[lmbRequestDispatchingFilter](./lmb_request_dispatching_filter.md) is a common place where lmbRequestDispatcher is used.

There's a request dispatching schema on [RequestDispatching](./request_dispatching.md) page.
