# extbase-redirectforward-example

A TYPO3 example of handling redirects and forwards.

TYPO3 Extbase has an ActionController::processRequest()
handling that calls:

- execute initializeAction()
- execute initialize[TARGET]Action()
- Perform argument mapping
- execute [TARGET]Action()

The actual actions return ResponseInterface objects.
Only those can interact properly with a
RedirectResponse or a ForwardResponse.

The difference:

* Redirect performs an additional HTTP request
* Forward operates within the same HTTP request

The `initialize[TARGET]Action()` methods however
can not deal with forwarding to other methods,
because vital arguments and view mapping has
not yet taken place.

To deal with this you can:

* throw an immediate PropagateResponseException($response)
  exception. That ends the FULL HTTP Request
  and emits an error. Note that if your Extbase
  plugin is just one content element on a page,
  this will "kill" the full page flow and emit
  the message you have passed in $response.

* Let your initialize actions make the arguments
  valid again, so that your actual action can be
  called. Then the actual action handles this
  state and performs redirection or forwarding.
  You can for example enrich your request context
  with a specific attribute to flag this
  needed forwarding. This will allow you to just
  act on the Extbase plugin level, and not the
  whole page-level.

To showcase this, this extensions DummyController
makes use of several variants on how to handle it.

Enjoy. Let me know if this helped you in any way,
shape or form.

