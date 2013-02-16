# Request Dispatching
Request Dispatching is a process of determination of the requested [lmbService](./lmb_service.md) and requested [Action](./action.md).

There is the list of classes that play important roles in Request Dispatching process:

* [lmbRequestDispatcher](./lmb_request_dispatcher.md) that accepts ServiceRequestExtractor and lmbActionRequestExtractor in constructor (see [lmbRequestExtractor](./lmb_request_extractor.md)).
* [lmbDispatchedRequest](./lmb_dispatched_request.md) that is created by [lmbRequestDispatcher](./lmb_request_dispatcher.md).
* [lmbRequestDispatchingFilter](./lmb_request_dispatching_filter.md) that starts the whole process.

## UML charts
### Static structure
### Sequence diagram
