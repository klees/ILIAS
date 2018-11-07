# How to Process Input Securely in ILIAS?

The inspection and processing of user input is of crucial importance for the
security of a software system. Every input to the system may become a possible
attack vector and compromise security qualities of the software when carelessly
rocessed. Data propagating into the system thus must be inspected and, in case
of doubt, be discarded from further processing as early as possible.

This document attempts to show the current state of the art of input processing
in ILIAS and outline a way to improve that processing by proposing changes
regarding code as well as processes. The document was created on request of
and under involvement of the Technical Board of the ILIAS-Society.

We begin by explaining the core considerations that were taken into account
when designing measures to improve the security of input processing in ILIAS.
We go on by explaining recently implemented libraries to improve the input
processing and showcase it at the forms in the UI-Framework.

We then evaluate requirements from components of ILIAS that currently do not
implement systematical approaches to security when processing input. From there
we derive which enhancements and extensions to ILIAS are required to span the
components currently not included in the systematical approach to input security
and how these can be implemented technically as well as socially.

## Core Considerations

### Primitive Obsession

A core problem when handling input is the question whether input was already
inspected from a security perspective or if a given function or section of code
needs to perform that step on a given input.

Consider this snippet of pseudo-code, that someone might have written at some
point in the development of ILIAS.

```php
class ilSomeGUI {
	/**
	 * @var ilSomeService
	 */
	protected $some_service;

	public function executeCommand() {
		$input_param = $_GET["myInputParam"];
		if(!$this->checkInputParam($input_param)) {
			throw \Exception("Alert! Someone tried to temper with my input!");
		}

		$this->some_service->doWork($input_param);
	}

	protected function checkInputParam($param) {
		// some checking stuff;
		return $check_result;
	}
}

class ilSomeService {
	public function doWork($param) {
		// somehow use $param
	}
}
```

The GUI-class retreives and checks the input provided by the user via query-
params in `$_GET`. It complains when the input doesn't match some criteria. It
the hands the data off to some service that does the actual work. When the GUI
and the service where created, this design is perfectly secure regarding the
input parameter. A practical example might be some service that needs a filename
as input parameter via get an then does some operation on the file, e.g.
delivering the file to the user.

Later on (where later might actually be years later) someone (the same developer
as well as a completely different person) might add a new feature to ILIAS,
reusing the same service and adds the following (pseudo-)code to the system:

```php
class ilSomeOtherGUI {
	/**
	 * @var ilSomeService
	 */
	protected $some_service;

	public function executeCommand() {
		$input_param = $_GET["myInputParam"];
		$this->some_service->doWork($input_param);
	}
}
```

Expecting, that the service itself takes care of valid input, the other developer
just calls the service with some input provided by the user and thus (possibly)
opens a hole in the security defenses of the software.

Keep in mind, that the situation might look a lot more complicated than this example.
Maybe a first GUI-class checks the parameter provided by the user, passes it to a
subsequent class, which then calls the service. Maybe the service contains convoluted
code that does not clearly communicate if input is inspected or not.

There are different measures to treat this problem. Documentation of the service
may be required, that outlines if and how input is checked. But then the new task
of taking care of the documentation is introduced and the documentation can not
be checked by automatic procedures and thus must be read and understood by every
developer using the service. We might introduce some guidelines that say where
and how user input needs to be validated. But a guideline is similar to documentation,
it needs to be understood and followed and can only be verified automatically in
rare cases. The checks might be pushed down to the lowest layer of the application.
But this might mean that information required for the checks are missing at the
point where the checks need to take place.

These solutions all miss a crucial problem contained in the code above. A general
duty when programming is to give meaning to anonymous sequences of bits and bytes
contained in our computers memories. This does not end at primitive datatypes
like `string` and `integer`, PHP offers more tools to attach meaning and sense
to data that at the same time serve as a documentation for developers.

Consider the GUI and service were implemented like this to begin with:

```php
class ilSomeGUI {
	/**
	 * @var ilSomeService
	 */
	protected $some_service;

	public function executeCommand() {
		$input_param = new ParamType($_GET["myInputParam"]);
		$this->some_service->doWork($input_param);
	}
}

final class ParamType {
	/**
	 * @var string
	 */
	protected $value;

	public function __construct(string $param) {
		if (!this->checkInputParam($param)) {
			throw new \InvalidArgumentException
				("$param is not allowed when constructing ParamType.");
		}
		$this->value = $param;
	}

	protected function checkInputParam(string $param) : bool {
		// some checking stuff;
		return $check_result;
	}
}

class ilSomeService {
	public function doWork(ParamType $param) {
		// somehow use $param
	}
}
```

The fact, that the parameter to the service needs to be validated is communicated,
and also enforced, by the fact that the `doWork`-method takes a typed parameter.
The user can only possess such a value when he has a string that passes the check in
the constructor. In fact, the existence of a value of type ParamType proves that
the check was performed (at least in a setting where we regard "sane" usages of PHP
only and disregard odd usages of ReflectionClass, Serialization, ...). The property
that only these values are passed to `doWork` will be enforced by the runtime by
means of the type hint.

This approach won't fit to all scenarios in which checks need to be performed on
input, but will improve scenarios where the values that are passed between different
components are in fact more than simple strings, integers, arrays or floats.
Introducing wrappers around primitive data types will also improve other properties
of the system.

The phenomenon that primitive datatypes are used instead of semantically richer
structures is known as the antipattern "primitive obsession".

### API-Design

The native PHP-API to access values provided by the user via GET and POST is
extremely simple and handy:

```php
$foo = $_GET["foo"];
$bar = $_POST["bar"];
```

Note, that we don't even have to declare that `$_GET` and `$_POST` are globals.
Various tutorials and answers on stack overflow promote this direct usage of
the superglobals, often without adressing dangers in this approach. And of course
this approach is as dangerous as it is handy, since values are easily retrieved
at any location in the code but not checked for any property when retrieving
them.

ILIAS currently attacks this problems by treating `$_GET` with the static method
`ilInitialisation::recusivelyRemoveUnsafeCharacters` in the initialisation procedure.
This method uses `strip_tags` and removes some characters considered to be unsafe
for some reason from all values in `$_GET`. `$_POST` is not treated generally.
This procedure might prevent the most obvious attacks that attempt to introduce
malicious values via `$_GET`, but surely cannot treat every such vector as it
cannot possibly know about the circumstances in which these values will be used.

However, every future API to retrieve values from `$_GET` and `$_POST` will have
to compete with the simple superglobal API. It is indeed possible to deprecate
the direct usage of `$_GET` and `$_POST` via Dicto, but this won't help when
developers complain about some approach being hard to use or understand. It is
of course hard to compete with the superglobals regarding simplicity. Thus every
new API must provide benefits in other areas. It is unlikely that this can
only be found in security, though, as security seldomly is a core concern when
writing software.

The introduction of PSR-7 with its interfaces to HTTP-messages provides a
promising impression where these benefits might be. This API provides easy to
read and to remember methods on the message objects that allow to read
information from the request. The HTTP-messages are immutable and suggest to
be passed into the services that consume them due to their value appearance,
instead of beeing summoned from the void via a global. This has implications for
the general architecture of the application as well as for the its testability.

ILIAS implements PSR-7 since 5.3, at least in some parts of the application.
This suggests, that this implementation needs to be the base for secure input
processing, at least regarding the usage of `$_GET`, `$_POST` and `$_COOKIE`.

We thus need to consider carefully what the benefits of a future API are and
how we document and communicate them.

### Reality of ILIAS Development

### Feedback when Rejecting Input

### Policy vs. Structure

## State of the Art: Core Libraries

During the implementation of Form Inputs in the UI-Framework three libraries where
created that tackle various problems when processing input via forms in ILIAS. The
functionality was created in libraries to be used in other scenarios as well and thus
offers a base to design secure input processing for other components as well.

### Data

### Validation

### Transformation

## Showcase: Input via Forms in the UI-Framework

## Evaluation

### Widen Concept to GET-Requests via the UI-Framework

### Requirements of XML-Imports

### Requirements of SOAP

### Other Input Mechanisms

## Outlook

### Improvements of Existing Libraries

### New Libraries and Services

### Implementation
