# The key conceptions of Limb3 Controller
Controller handles application logic.

Here is the list of the key conceptions of Limb3 Controller:

* **Request** — contains request information. [lmbHttpRequest](../../../net/docs/en/net/lmb_http_request.md) contains POST, GET and FILES.
* [Response](../../../net/docs/en/net/lmb_http_response.md) — contains response information from application once Request is processed.
* [lmbService](./lmb_service.md) — is a group of actions representing some particular domain or functional area. For example, AdminNews lmbService can contain all actions to display, create, edit and delete news.
* [Action](./action.md) — is a functional unit of a Limb-based application. It's ok to think about Action as a page or as a page type. Action always belongs to a lmbService.
* [lmbRequestDispatcher](./lmb_request_dispatcher.md) — determines what exactly application should do. lmbRequestDispatcher maps Request to [lmbDispatchedRequest](./lmb_dispatched_request.md) which encapsulates a system request as a whole and contains specific lmbService and Action.
* [FiltersChain](./filters.md). Filters act as Front Controller for a Limb-based application. Filters perform misc system operations that are common for the majority of applications and usually have no relationship to domain logic of application. For example, it's common for intercepting filters to run and close http session, check user access rights, log request for statistics, etc.
* [lmbCommand](./lmb_command.md) — performs all application specific logic.
* [Validation](./validation.md) used in [lmbFormCommand](./lmb_form_command.md) to check data received from user. Limb uses WACT validation sub-system.
