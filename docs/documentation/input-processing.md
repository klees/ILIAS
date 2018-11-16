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
when designing measures to improve the security of input processing in ILIAS
and issues that some might find interesting when talking about input processing
but that were not considered here. We go on by explaining recently implemented
libraries to improve the input processing and showcase it at the forms in the
UI-Framework.

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

We thus need to consider carefully how a future API looks from a consumers
perspective, what the benefits of the API are and how we document and communicate
them.

### Reality of ILIAS Development

There are certain circumstances in the development in ILIAS that need to be
recognized when thinking about a systematic approach to secure input processing
in ILIAS.

First and foremost, ILIAS is a software under 20 years of continous development.
This on the one hand means, that a systematic approach needs to somehow address
different historical layers of the software, either by integrating into them as
seamlessly as possible, by showing clear pathes to migrate these layers to new
approaches or by deprecating these old layers with intelligible arguments. On the
other hand there are habbits and approaches that are ingrained in processes and
procedures of the community that won't change immediately but might require a
long term engagement to be effectively changed.

During the development, security was historically seldomly addressed as a seperate
requirement or concern, neither in conceptual phases nor in the coding itself.
There currently aren't any tools or processes that systematically attempt to raise
security of ILIAS, besides the recently improved possibility to hand in security
reports to the community. Other approaches that are common in environments that
frequently have higher security requirements as an LMS, like pen-tests, systematic
training and instruction of developers, risk analysis, automated or manual code
reviews, are not implemented in the general developement process of ILIAS. This
on the one hand means that there certainly are low hangy fruits to be picked
regarding security. On the other hand it again shows, that technical approaches
most certainly won't be enought to raise the general level of security. Even if
considering social implications, like we attempt to do in this paper, the
improvement of the security of input processing won't be enough in general and
will have a hard stand if not backed by other means. There are, however, measures
in the scope of input processing, like proper communication and good API-design,
that might help to form new habbits regarding security in the long run.

We thus try to show approaches besides technical measures for the scope of this
paper, but also ask the project leadership of ILIAS to keep thinking about and
implementing more hollistic approaches to security.


### Feedback when Rejecting Input

The circumstances in which the system receives input have a huge variety regarding
their sources. There are forms that are operated by users, that need to be informed
regarding the success or failure of the desired input. There are forms of input
that are very technical in their nature, like reading data from the database or
XML-import files, where machines are talking to other machines along a more or
less strictly defined interface. There are forms of input, that seem to be machine-
to-machine like the former, but where we in fact don't really know if and how
humans that need feedback are involved, e.g. the SOAP-Interface or JSON-over-HTTP-
interfaces.

The requirements regarding the mere filtering of the data that reach the system
are very similar in all these cases: We need to make sure that only data maching
certain criteria in shape and content may pass the boundary of the system and be
subject to further processing. Data should be scrutinized as closely and early
as possible. The requirements regarding feedback to the other side of the
communication are, however, widely different.

A human sitting in front of a form might just input improper data and every nice
system will try to give her feedback in the most helpful and detailed form as
possible. Reading some data from the database seems to require no feedback to a
user at all, as every missformed data indicates some deeper problem that most
certainly cannot be solved at the interface to the database itself. A malformed
XML-file in an import might hint at incompatibel versions or objects regarding the
import, as well as at some attempt to tamper with an import file and use it as a
vector to degrade security of the system. A user importing the file might require
the feedback "incompatible version", but less likely seems to be interested in
the information which exact field of which datastructure didn't match expectations.
A detailed response to a missformed SOAP-request might help a desperate developer
of a webservice-interface, but also inform a malicious hacker regarding new
approaches to degrade security.

Thus, the question how, where and which feedback is given as a reponse to a
malformed input is of interest when designing an API to secure input. It must be
possible for the consumers of the API to give detailed feedback to human users,
as well as discarding and hiding that feedback completely when the nature of
the interface to the system makes this appropriate. To be able to detect tempering
we suggest to address the logging of failed input attempts in the future. As this
paper tries to lay the ground for securing the input processing (and not the
detection of attempts to temper with the system), the logging-topic is considered
to be out of scope for the rest of the paper.


### Structure vs. Policy

The cause for requirements to data that should cross the systems boundary can
be divided into two categories that might look similar at first sight but show
significant difference on a closer look.

The first cause for constraints on input are derived from requirements on the
structure of the input, that is which types of data the input needs to contain
in which position and shape. A multiselect input, e.g. might require a `list
of strings` as input, while a SOAP-call to copy an ILIAS object might require
a dictionary containing an integer at the key `source` and one at `target`.
If these requirements are not met the application often will stop at some point
and generate some form or more or less informative exception or error message.
If e.g. the "target" is missing in the example of the SOAP call, the operation
cannot be completed in a meaningful way.

It is not enough to expect the application to fail at some point in case the
input is not structured correctly. On the one hand, the input might propagate
deeply into the system, cause subsequent errors or make it hard to debug the
root cause of an exception or error. In the SOAP-call, for example, a missing
"target"-parameter might be interpreted as `null`, be written as `0` into the
database before the actual error happens and later on cause all kind of havoc
when read from the database again. On the other hand data that  propagates into
the system, creates unexpected effects and possibly generates helpful output
for an attacker means that the available surface for every attack is unnecessarily
large.

Structural requirements thus need to be checked as early as possible and deeper
layers in the system need to put requirements on their consumers that data is
in fact shaped as expected. This of course has a deep connection to the primitive
obsession antipattern explained earlier, as the requirements on the shape need
to be documented and enforced properly.

The second cause for constraints roots not in the shape of the data but in their
very content and the specific circumstances in which the data is processed. Data
that is shaped in the form of a date might be invalid under the policy that a
users birthday most possibly ain't a future date. The same date would be a
perfectly valid date for an appointment. The integer shaped target for the SOAP
copy operation might be invalid under the policy that only writeable categories
are a valid target for a certain user. This might be true or false depending on
the user that performs the operation.

Other than the shape of the data a policy seems to require more feedback to
the agent that attempts to input the data to the system. While, e.g. the shape
of the date might be guaranteed by the input field that the user used to enter
his birthday, he will need feedback when the date is out of some expected range.
Also requirements from policies are often more volatile than requirements from
shape. The answer to "is this category writeable" might be answered differently
from one second to the other when some permission was changed. This also shows,
that policies often have authorities that judge and enforce them, as the RBAC-
system does for the permissions. Consequently, a policy on some data most possibly
cannot be enforced directly at the boundary of the system but will already
require some processing of the data that checks the policy.

This makes the picture when and how policies can be enforced to secure input
processing a lot fuzzier than the this is the case for structural requirements.
It will be cumbersome or even impossible to document policy requirements in the
PHP type system via classes. It also will be a lot harder to find a framework for
enforcing policies on data that fits all cases, since policies mostly will arise
at the business rules of the application of a framework, thus being responsibility
of the user.

Policies still are indispensable regarding the security of the system. If e.g.
permission policies can't be enforced this will render the RBAC-system useless
and hence knock out an essential security feature of the application. We thus
will try to look how policy enforcing systems may hook into the general input
processing, but we will not be able to exhaustively examine all requirements
from said systems in this paper. We request the maintainers of said systems to
understand the role of their systems in this regard and work towards sensible
solutions to secure input processing regarding the nature of the policies their
systems want to enforce.


### Declarative vs. Imperative

When enforcing constraints on the inputs to a software system, it is not enough,
that the desired constraints are, in fact, enforced. It is also important that
other people (e.g. developers, reviewers, even the developer herself at a later
point in time) can understand and scrutinize the constraints in place. This allow
to check, if constraints are sufficient and up to date, as well as to understand
which data is allowed to passed the boundary of the system and which data is
discarded.

Code written in an imperative style focusses on *how* a problem is solved, while
declarative code focusses on *what* the developer wants. Typically, well written
declarative code is easier to understand as according imperative code, as it
allows to hide intricate details in some implementation, while the writer of the
declaration can focus on what he wants. Think about the difference between CSS
and a CSS rendering engine for an extreme example of that observation.

Using declarative approaches can lead to an (embedded) domain specific language
((E)DSL) that allows to express solutions in a narrow domain of problems with a
specific set of language constructs. The language for Dicto is an example for such
a DSL. DSLs allow for a concise and readable formulation of a desired solution
that shows the information essential to the problem with little boilerplate.

To express the constraints on inputs to the system, a declarative approach or
EDSL is the right choice, since it allows the developer to focus on the task
of choosing his constraints on the input without being bothered by the question
how a check may be conducted. For readers of her code, a well crafted set of
tools to express constraints will simplify to understand which data should be
discarded and why. The imperative part, how the checks are performed, then can
moved to a location common for all components and be put under extra scrutiny.
This will free developers as well as reviewers of the question if constraints
actually work as desired.


## Non-Issues

### Performance

Checking inputs early and thorough will always require more computational
ressources then letting the data pass unscrutinized. However, ILIAS is not a
system that needs to process huge amounts of input in a time critical environment.
For that reason, performance will be considered a non-issue throughout this paper.

If at some point in the future the validation of input will become crucial for
the performance of the system, we will have some strategies to work on performance
of the validation by (e.g.):

* using external programmes to validate input, e.g. for huge blobs of data like
  movies
* giving names to complex constraints and programme them directly instead of
  composing them from smaller parts
* compiling constraints into more efficient PHP-code that e.g. uses references
  to pass data instead of copy it or uses other methods to improve PHP performance


### Escaping

"Escaping" is the procedure to prepare some data to be outputted in a certain context.
It is a measure to allow another system to correctly interpret the data in the way
our system intends it to be interpreted. This has security implications in some
contexts, since incorrect interpretation of some user provided data will lead to
a degraded security in some subsystem. Widely known vectors that use missing or
incorrect escaping are SQL-injection and Cross-Side-Scripting. The famously and
widely used `ilDB::quote`-method is an example for such a procedure that defends
the database against injections of SQL.

Escaping thus is a question of the context in which data is outputted from the
system and thus on the exact other end of the data processing the system performs.
When data is inputted to the system it certainly in general ain't possible to
determine the context to which the data will be outputted later on. The correct
means of escaping thus cannot be determined at that point as well. Escaping thus
is a problem that needs to be tackled at the various output interfaces of the
system.


### Sanitizing

"Sanitizing" is the attempt to clean up the data provided by users and remove
unwanted parts of the input to derive acceptable input. On the one hand,
sanitizing input data can be understood to be an implementation of [Postels Law of
robustness](https://en.wikipedia.org/wiki/Robustness_principle). On the other hand
it might be understood as security measure to remove dangerous parts of some
input in order to prevent injection attacks or at least make them less likely.

As an attempt to make the system more robust, sanitizing input certainly is a valid
approach that can be understood as a step, or even the step, in the transformation
from primitive user input to richer internal data types. As such it does not require
extra attention.

As an attempt to prevent injection, sanitizing is a very weak measure. Similar
to escaping, it is not possible to know the output context for some data and
hence the required escaping at the moment the data is handed to the system as
input. This problem will get bigger once the system gets more interfaces that
actually output data.

Instead of removing data from input that is deemed unsecure in some context, it
is more advisable to reject said data, either with a detailed message to the user
or not. This on the one hand allows the user to act accordingly and modify the
data she send, while simply discarding parts of the data may leave the user in
the wrong impression that the input was actually accepted. On the other hand,
input containing data that could be used in an injection-vector could be a hint
that someone tries to tamper with the system. This attempt should be noticed
somehow and not silently be ignored by removing the injection code. Last but not
least, a sanitizing procedure might become an attack surface in its own right
when elaborate and complex enough.


## State of the Art: Core Libraries

During the implementation of Form Inputs in the UI-Framework three libraries where
created that tackle various problems when processing input via forms in ILIAS. The
functionality was created in libraries to be used in other scenarios as well and thus
offers a base to design secure input processing for other components. The libraries
also already reflect some of the core considerations outlined before. We show the
current state of the art in these libraries to give an impression of what is already
there and later on derive what needs to be added.

### Data

The [Data-library](../../src/Data) aims at providing standard datatypes that are
used throughout the system and thus do not belong to a certain component in ILIAS.
Currently it contains types for `Color`, `DataSize`, `Password` and `URI`. There
also is the `Result`-type which captures the possibility of error in a calculation
by containing either some other value or some error information.

The [Data-library](../../src/Data) thus will be an important tool to tackle the
primitive obsession. When, for example, dealing with passwords, the `Password`
type in conjunction with typehints will allow PHP to help us installing guards
against unintendedly publishing a password. The security gains that can be provided
by using the library will mostly be on the structure-side of the policy/structure-
scale that was presented in the core considerations.

The library will also be a part of a good API design. Its objects will allow IDEs
to help developers, the methods on the objects are easier to find and document
than keys in some array. The off-the-shelf types in the library can help developers
to save work.

A precondition for the success of the library will be, that it contains some
interesting types and that it is known to developers. Besides the commonly used
types captured in the library, there still will be a lot of datastructures that
belong to a certain component and not into a common library. For these types
similar strategies than these showcased in the [Data-library](../../src/Data)
will need to be deployed by the responsible maintainers.

These strategies are:

* Primitive data types should be used as little as possible. Instead semantically
richer datastructures should be used to put PHP's type system to a greater
effectiveness. This will help security as well as correctness and understandability.
* The richer datastructures should protect their structural integrity via a
"correctness by construction"-approach. This means, that structural constraints
should be enforced in the constructor and by all methods to change the datastructure,
be it setters or mutators. Structural integrity needs to be enforced by the
datatype to make invalid data unexpressable.


### Transformation

The [Transformation-library](../../src/Transformation) declares properties of
`Transformation`s which are understood as structural conversions between different
shapes and types of data that do not perform side effects. It further aims at
providing common `Transformations` to be reused throughout the system. The library
thus is the tool to transform primitive input data into semantically richer
structures in a declarative way.

Currently it only provides three prefactored transformations and one transformation
that allows a user to define a closure that performs the desired task. To become
part of a good API we certainly will need to add more transformations that can be
reused by consumers and showcase the benefits of a declarative approach to input
security.

Like the [Data-library](../../src/Data), the [Transformation-library](../../src/Transformation)
deals with structural constraints on the data and not policies. It will need to
be backed by semantically rich data types that protect their structural integrity
properly to become effective in securing input processing. If the transformations
only work on primitive datatypes, they will only amount to shuffeling array entries
back and forth without documenting the effort in the type of the created data.
It thus will be a tool to quickly and easily derive meaningful data from primitive
input but will also require additions to the [Data-library](../../src/Data) and the
components that use their own data structures.


### Validation

The [Validation-library](../../src/Validation) provides an abstraction for
`Constraints` on data, where a `Constraint` is a check in conjunction with a
builder for a human readable error-message. Like the [Transformation-library](../../src/Transformation)
the [Validation-library](../../src/Validation) also attempts to provide a set of
standard constraints and facilities to compose complex constraints from simpler
ones.

Currently the library provides 14 prefactored constraints and one constrain
that allows to create custom constraints via closures. Here we certainly
require additions to the currently available constraints as well as a better
internal structure that e.g. groups the constraints by the type they are acting
upon.

Other then the two aforementioned library, the [Validation-library](../../src/Validation)
mostly will deal with constraints derived from policies on the input data. It
thus will certainly need backup from those two libraries regarding the structures
it operates upon. Like the `Transformations` the `Constraints` offer the possibility
to declaratively define these constraints and thus make them understandable and
readable. To fully unfold this potential the general use of custom constraints
needs to be minimal.

This also offers a perspective how other policy enforcing components in the system
may come into play here: Like the [Validation-library](../../src/Validation) itself
they may offer sets of constraints regarding the policies they are enforcing.


## ilUtil::stripSlashes and ilInitialisation::recusivelyRemoveUnsafeCharacters

Currently ILIAS has two methods that are used to systematically secure input
processing: `ilUtil::stripSlashes` and `ilInitialisation::recusivelyRemoveUnsafeCharacters`.

`ilUtil::stripSlashes` is used in many places throughout ILIAS. Other than its
name suggests, it does not only attempt to strip slashes from some string but
also attempts to remove html-tags, where the user can define which tags will
remain in the input. PHP's standard `stripslashes` will only be called if the
ini-setting `magic_quotes_gpc` is defined, which is deprecated by PHP and thus
most likely won't be present. `stripslashes` thus most likely will not be called.

Although `ilUtil::stripSlashes` is used on input, it workings suggest that it is
actually a device that removes data according to some output context (html) which
means that it is a form of escaping. This also shows in the fact, that it does
not remove data that would be dangerous in other contexts, e.g. SQL for databases
or `"` for json. Also, data treated with `ilUtil::stripSlashes` won't work in
an attribute context of html, since `"` is kept.

The problem that `ilUtil::stripSlashes` currently seams to tackle mostly is that
in some locations users want to use some html-markup in their input, but the
system needs to ensure, that not all html-markup is used to protect from XSS.
We suggest to solve that problem by introducing a proper input field in the
UI-framework and maybe use markdown for the requirements that are currently
fulfilled via markup. On the other hand we propose to introduce a proper output
escaping at the various layers that perform output, similar to `ilDB::quote`.
Finally we propose to introduce datatypes that wrap strings and make their
content more explicit, e.g. `HTMLString` for strings that contain HTML-markup or
`AlphaNumericString` for strings that contain alphanumeric characters only.
The new datatypes will be crucial to allow new inputs and escaping to work
together, as they will tag the data on the way between input and output and will
allow proper decisions on how data needs to be trated when escaping it. A very
similar problem already emerged in the Mail-Service, where the service needs to
work with raw `string`s without really knowing if they need to be escaped for
HTML or not. We thus suggest to phase out the use of `ilUtil::stripSlashes`.

`ilInitialisation::recusivelyRemoveUnsafeCharacters` is called in the initialisation-
process of ILIAS to remove HTML-tags and some single characters that are deemed
unsafe from `$_GET`. Since the output context is not known the situation is similar
`ilUtil::stripSlashes`, still `ilInitialisation::recusivelyRemoveUnsafeCharacters`
seems to cover a broader range of output as it removed `"` and `'` as well. We
propose a similar approach than for `ilUtil::stripSlashes` by introducing proper
datatypes to capture the use of parameters in  `$_GET`. We suspect this uses to
be very narrow, mostly ids, alphanumerics and the control path. It should be
simple to device proper datatypes for these usecases. To phase out the use of
`ilInitialisation::recusivelyRemoveUnsafeCharacters` we will additionally have
to provide a proper method to get values from `$_GET` as outlined in [API-Design](#api-design).


## Showcase: Input via Forms in the UI-Framework

The libraries outline in the [State of the Art](#state-of-the-art) have been build
to implement input via forms in the UI-Framework. We thus want to use the form
input in the UI-Framework as a showcase for the libraries and explain their
cooperation with regards to the principles outlined in the [Core Considerations](#core-consideration).
On the other hand, the current state of the form inputs might already hint at
some potential for future improvements in the libraries an in general. The code
presented in the following was discussed in [this PR](https://github.com/ILIAS-eLearning/ILIAS/pull/1189)
and is now [part of the ILIAS-core](https://github.com/ILIAS-eLearning/ILIAS/blob/trunk/Modules/StudyProgramme/classes/class.ilObjStudyProgrammeSettingsGUI.php#L159).
Since we want to show case how input data can be secured here, we refer to the
explanation of the [Inputs in the UI-Framework](https://github.com/ILIAS-eLearning/ILIAS/tree/trunk/src/UI/Component/Inputi/README.md)
for further explanation regarding visual aspects of the form.

We first will have a look into [`ilObjStudyProgrammeSettingsGUI::update`](https://github.com/ILIAS-eLearning/ILIAS/blob/trunk/Modules/StudyProgramme/classes/class.ilObjStudyProgrammeSettingsGUI.php#L159) to get a general idea of the structure of the processing. The example
is shortened a little to highlight the essentials, while comments are added
for explanation:

```php
$form = $this

	// We first build the form, which contains the definition of the shape of the 
	// expected input, the constraints on that input and the procedure to transform
	// it into the required structure (along with the visuals).
	->buildForm($this->getObject(), $this->ctrl->getFormAction($this, "update"))

	// We then attach the actual source of that data, which is a PSR-7 `ServerRequestInterface`.
	// Note that this attaches values and (possibly) errors to the input fields in
	// the form, that directly allows the developer to again show it to the user.
	->withRequest($this->request);

// Finally we attempt to acquire the data from the form. Note, that this data will
// either be not available or already fit the structure and policies we defined
// in buildForm.
$content = $form->getData();

// If the input of the user did not fit the expected structure and policies, we won't
// have been able to retreive any data and hence can't process anything.
$update_possible = !is_null($content);
if ($update_possible) {
	// perform update process
} else {
	// print form with errors to the user
}
```

The essential part of the input processing is the definition of shape, constraints
and transformations of the input, which goes along with visual requirements when
definining forms. We thus have a look at the shortened and commented method
[`ilObjStudyProgrammeSettingsGUI::buildForm`](https://github.com/ILIAS-eLearning/ILIAS/blob/trunk/Modules/StudyProgramme/classes/class.ilObjStudyProgrammeSettingsGUI.php#L216):

```php
// We define some shortcuts for brevity in the definition later on.
$ff = $this->input_factory->field();
$tf = $this->trafo_factory;
$txt = function($id) { return $this->lng->txt($id); };

// We gather some options to be used in a select-field later on.
$sp_types = ilStudyProgrammeType::getAllTypesArray();

// We construct a form by using the factories of the UI-Framework defined
// previously.
return $this->input_factory->container()->form()->standard(
	// We need to tell where the input is posted to...
	$submit_action,
	// ... and which fields the form contains
	[
		// We assign (local!) names to these fields...
		self::PROP_TITLE =>
			// ...define the types of the fields...
			$ff->text($txt("title"))
				// ...the value that is shown initially...
				->withValue($prg->getTitle())
				// ...and (if so) whether input is required from the user.
				->withRequired(true),
		// We do this again for the remaining fields...
		self::PROP_TYPE =>
			$ff->select($txt("type"), $sp_types)
				->withValue($prg->getSubtypeId() == 0 ? "" : $prg->getSubtypeId())
				// ...and may also attach more transformations to the data in the
				// fields if our usecase requires us to. Here we need to wrap around
				// the fact, that an  non-selection in the select-field is represented
				// by an empty string in the inputs, while the Study Programme uses 0
				// to represent a Programme with no type.
				->withAdditionalTransformation($tf->custom(function($v) {
					if ($v == "") {
						return 0;
					}
					return $v;
				})),
		self::PROP_POINTS =>
			// The UI-Framework offers types of input that already carry some
			// constraints, like `numeric` that only allows for numeric values
			// in the users input.
			$ff->numeric($txt("prg_points"))
				->withValue((string)$prg->getPoints())
		)
	]
);
```
Note the key components in the construction of the input processing of forms in
the UI-framework:

* The API of the form is very small and only contains two interesting methods
with a clearly defined purpose. `withRequest` attaches user input to the form,
while `getData` allows to retrieve it. All the nitty-gritty details of how the
data is collected from `$_POST`, processed, checked, filled in the form etc. is
hidden in the definition of the form and these two methods.
* The definition of the form is declarative and the structure of the code can
be arranged in a way that closely resembles the structure of the form as it is
found on the screen. Besides the closure in `withAdditionalTransformation` no
statement code is used. This makes it easy to grasp what is going on here,
possibly even for people that don't know ILIAS or PHP in general very well.
* The syntax for the declaration of the form uses techniques well known for users
of the UI-Framework, like named factories, immutable objects and easy composition
of larger structures from smaller parts. The mechanismn to process the input
was created with care to fit these techniques  and maintain their properties. The
input processing is weaved naturally into the definition of the visuals.

This all amounts to an API-design that encourages the user to properly process
input received via forms. The correct approach is made easy, while incorrect
procedures are hard to implement. To stress this principle: also it might look as
if it could be possible to retreive data from `$_POST` by using `$_POST[self::PROP_TITLE]`,
this actually won't work. The name `self::PROP_TITLE` of the field only occurs
locally while the names of the field in the actual `$_POST` are set by the
abstraction. This enhances composability and disencourages incorrect procedures
at the same time.

The subject [Structure vs. Policy](#structure-vs-policy) needs to get some extra
attention since it is only present in this example very implicitely. The form
only declares a few constraints in a visible manner. First note, that `withRequired`
and `numeric` in fact are constraints. While `numeric` is a structural constraint
(only ints or floats are allowed), `withRequired` may be viewed as a policy, as
there won't be any technical problems with an empty string as title besides the
quite comprehensible expectation that a title at least contains one character.

The method `ILIAS\UI\Component\Input\Field::withAdditionalConstraint` can be used
to attach additional constraints over the ones that the input fields define by
default. If one would want, for example, a numeric field that may only contain
numbers larger than 0, one could attach a an according constraint to the field:

```php

$ff = $this->input_factory->field();
$cf = $this->constraint_factory;

$numeric_larger_than_zero =
	$ff->numeric($txt("prg_points"))
		->withAdditionalConstraint($cf->greaterThan(0));

``` 

Note that this defines a constraint on the input as well as the error message
that is shown when the input of the user didn't match the expected value. Since
there most probably is some user sitting in front of a screen showing the form,
it is nice of the system to provide him with some hint on his mistake to put
him into the position to fix it. Constraints can be added to singular fields
as well as to compositions of fields to express constraints that concern multiple
fields at the same time.

While constraints are more about policies then structure, the method
`ILIAS\UI\Component\Input\Field::withAdditionalConstraint` is used to derive
required structure from the data provided by the users. Similar to the
constraints, the input fields already bring transformations, but the user
may add additional ones on top of them. The `text` and `textarea` inputs, e.g.
currently `strip_tags` from the provided strings. Similar to `ilUtil::stripSlashes`
this is a weak measure to provide security, while the non-existing feedback about
the operation might lead users to think they actually entered html-tags when
in fact they didn't (see [Sanitizing](#sanitizing)). This transformation should
thus be removed once the according problems for `ilUtil::stripSlashes` are solved.

The advice to define datastructures that enforce their structural constraints
by construction (see [Data](#data)) also is not implemented in the given example,
as the data is retrieved from the form as an array. Two show how this might look
like, we might imaging a data structure that carries some basic data for every
`ilObject`:

```php
class ilObjectData {
	/**
	 * @var string
	 */
	protected $title;

	/**
	 * @var string
	 */
	protected $description;

	public function __construct(string $title, string $description) {
		// Enforce policy that titles need to contain at least one character.
		if (strlen($title) == 0) {
			throw new \InvalidArgumentException(
				"Title needs to have at least one character");
		}
		$this->title = $title;
		$this->description = $description;
	}
}
```

This could be used with the API for inputs as such:

```php
public function getObjectDataSection(string $title, string $description) {
	$ff = $this->input_factory->field();
	$tf = $this->trafo_factory;
	$cf = $this->constraint_factory;
	$txt = function($id) { return $this->lng->txt($id); };

	return $ff->section(
		[ $this
			->text($txt("title")
			->withValue($title)
			->withRequired(true)
		, $this
			->textarea($txt("description"))
			->withValue($description)
		],
		$txt("object_data"),
		""
	)->withAdditionalTransformation($tf->custom(function($vs) {
		return new ilObjectData($vs[0], $vs[1]);
	});
}
```

This approach will document the check on the "no empty title"-policy in the
datatype and bundle the two primitive strings together, like they were
actually given by the user. The API allows to handle a defined chunk of a
form with its visuals, constraints and transformations and reuse it in various
locations.

This example also shows potential for improvements in the libraries that are
used. The [Transformation-library](../../src/Transformation) currently doesn't
offer a premade solution to the common task of creating new objects and the
user needs to fall back to a `custom`-transformation. Although not very visible
in the example, the check on the "no empty title"-policy is duplicated, once
in the form (via `withRequired`) once in `ilObjectData::__construct`. It does
not seem to be possible to remove the check in this example (since `withRequired`
is not only a constraint but also adds a visual marker to that field). In general
it should be possible to check constraints in some classes constructor and hook
into the mechanisms of the [Validation-library](../../src/Validation) without
duplicating the check.

The processing of user input via forms in the UI-Framework by using the libraries
that where created thus implements the requirements outlines in the [Core
Considerations](#core-considerations) as such:

* It allows to tackle primitive obsession by considering the transformation of data
at the boundary of the system from the start and weaving it into the visual requirements
of forms.
* It offers an API that is declarative but still allows to introduce imperative
parts as required. The compositionality of the components on different levels
(fields of the form, constraints, transformations) allow to adopt to a huge
bandwith of requirements and as well as to construct reusable parts. This cannot
compete with the `$_GET`- and `$_POST`-APIs in simplicity, but still offers
compelling advantages with a pleasant surface.
* The new API can be introduced gradually and will work besides (but not with!)
the existing ilPropertyFormGUI. As the [PR for the Study Programme](https://github.com/ILIAS-eLearning/ILIAS/pull/1189)
shows, it is possible to use the new API with only minimal adjustments to rest of
the component.
* The API can express structural constraints as well as policy while being able
to give feedback to the user of the form.


## Evaluation

In the following we will evaluate parts of the system currently not subject to
a systematic approach to security in input processing. We will gather requirements
and assess, if and how the libraries written for the form input in the UI-Framework
can be put to use to implement them. In this process we will derive which extensions
are required for said libraries, as well as find realms that currently are not
covered by ILIAS libraries or services.


### Widen Concept to GET-Requests via the UI-Framework

### Requirements of XML-Imports

### Requirements of SOAP

### Other Input Mechanisms

## Outlook

### Improvements of Existing Libraries

#### Input UI-Framework

* Remove `strip_tags` in the `Text Field` and `Textarea Field` once wrappers around
the primitive string and proper output escaping is in place.
* Build inputs that allow for some formatting like bold, italic, enumerations, ...

#### Validation

* Allow datastructures that protect their own integrity to work hook into the
constraints via Exceptions.

#### Transformation

* Add transformation to build an object from some data.

### New Libraries and Services

### Implementation
