# How and when to use Dependency Injection in ILIAS?

As most sofware, ILIAS is a system of various components that interact with
and depend on each other in complex ways. A **dependency** is understood to
be some (part of) another component that a given piece of code requires to
fulfill its duty, but that does not belong to the responsibility of the given
piece of code. A GUI, for example, will (most likely) need to query some data
from another component to display it, but ultimately has the responsibility to 
present the data instead of getting it from, e.g., the database.

Moreover, some piece of code might not have the responsibility to perform a
certain task but it also should not care much how, where and when the required
task is performed, as long as the dependency acts according to a common protocol.
This **interface** between the component and its dependencies defines what the
dependency expects as input from the component, how it answers to the component
and how it behaves during the interaction. This interface is supposed to be as
small as possible. Every additional agreement between a component and a dependency
represents some overlap in responsibility and logic, that, when getting to big,
ties the components together strongly until they become indistinguishable.

Finally, **dependency injection** is the practice to let components explicitely
rely on their dependencies via an interface and only supply concrete implementations
of said interfaces at runtime. This approach to dependencies improves various
desireable properties of a software system, such as modularity and testability.

Traditionally, ILIAS mostly uses the Service Locator Pattern to resolve dependencies
between components, where PHP's `$GLOBALS` array was used as service locator
until we started to move our system to the `Container` from the `Pimple` library.
Currently, most of the system uses said "dependency injection container" (DIC)
as a service locator by pulling dependencies from the container as required.

We now want to start the next step on the journey to introduce dependency injection
in ILIAS and start to remove the DIC and other static forms of dependency resolution
from (most of) the codebase completely. This paper attempts to

* [summarize the status quo regarding dependencies](#where-do-we-start)
* [reinforce why we would want to make that move](#why-do-we-want-to-use-dependency-injection)
* [show which final state the system should move to regarding dependencies](#how-should-the-system-look-like-when-we-are-finished)
* [propose a plan how this move can be made incrementally](#how-can-we-move-to-the-final-state-incrementally)
* [try to anticipate common problems and hint at solutions for these](which-problems-will-we-face-and-how-can-we-overcome-them)



## Where do we start?

As a complex system build from various components, ILIAS already uses some
means to resolve these dependencies to concrete snippets of code. These should
be shown first to understand the starting position that we want to improve.

### Dependency Injection Container as Service Locator

Currently, most of ILIAS uses the dependency injection container `$DIC` as a
service locator. The service locator pattern via `Pimple`s `Container` is mostly
similar to using `global`s  from a consumers perspective. Typical consumer code
will look like this:

```php
class ilSomeGUI {
    public function someMethod() {
        global $DIC;
        $ilAccess = $DIC->access();
        // ...
        $ilAccess->checkAccess($obj_id, $usr_id, $perm);
        // ...
    }
}
```

The `$DIC` here mostly acts as a drop-in replacement for the `global`s array,
with the added benefit of providing type hinted methods to help IDEs for some
of the commonly used services. All services can be accessed via `ArrayAccess`
like this:

```php
class ilSomeGUI {
    public function someMethod() {
        global $DIC;
        $ilAccess = $DIC["ilAccess"];
        //...
    }
}
```

Some components already use the pattern that dependencies are only pulled from
the `$DIC` in the constructor and then stored in instance variables in the class:

```php

class ilSomeGUI {
    /**
     * @var \ilAccess
     */
    protected $ilAccess;

    public function __construct() {
        global $DIC;
        $this->ilAccess = $DIC->access();
    }

    public function someMethod() {
        // ...
        $this->ilAccess->checkAccess($obj_id, $usr_id, $perm);
        // ...
    }
}
```

This pattern already allows to inspect the dependencies of a class in a single
location if these are in fact all pulled from the `$DIC` in the constructor.
Some (small minority of) components even go one step further and expect dependencies
via constructor, where some are simply not constructible if dependencies are not
supplied and others use the `$DIC` as a fallback.

Although very similar looking consumer code can be written using globals, the usage
of the `Pimple\Container` already provides one benefit that is mostly invisible to
its consumers. Since it allows dependencies to be created lazily, it is indeed
possible to define how dependencies can be resolved but in fact delay the actual
creation until a service is really needed by a consumer. This advantage over
globals can be exploited once the remaining usage of globals are removed by
redesigning the initialisation of ILIAS.

### Inline Instantiation

ILIAS, as an OOP system, instantiates some, or sometimes even many, objects
during each and every request. While factories are sometimes used, instantiation
of objects via `new` in conjunction with a fixed class name is as least as common. 
Since every such instantiation uses a predefined class name that cannot be changed
dynamically during runtime, the resolution of the used class is a fixed dependency.

PHP also supports to instantiate classes with `new` and a dynamically determined
class name in a string variable, as e.g. `ilObjectFactory` does. This is certainly
more dynamic then using fixed class names, but the question if we could inject
dependencies than boils down to the question if we can in fact dynamically set
the used class name or rely on some convention or other means to statically set
class names to be used.

### Static Methods

Some components of ILIAS offer some or most of their functionality via static
interfaces. The poster child of this type of interface might be `ilUtil` but other
components are notorious here too. `ilObject`, e.g., plays various roles. For one,
it acts as a base class for all other `ilObject`s. It also is a go-to location
to retrieve some basic information about the objects (e.g. `_lookupType`) via
static methods and even serves as a facade to other services in the system, e.g.
via `_getAllReferences`.

A cursory look into the usage of static methods in the system seems to reveal
three basic use cases for static methods:

1. They are used when the underlying functionality seems or seemed to be unique or
   global in some sense, e.g. `ilUtil::stripSlashes`.
2. They are used when there is or was no available or obvious instance for an 
   instance methods, e.g. `ilUtil::zip`.
3. They are used, together with static variables, to provide a simple and handily
   available caching mechanism.

It should, however, be noted that static methods indeed are dependencies just
like globals, or maybe even worse. While globals can be initialized at runtime
and thus be replaced dynamically, static methods are referenced by name and one
needs to go to great lengths to replace them dynamically, as e.g. the mocking
framework `Mockery` shows. Static methods in some sense can also be considered
to be procedural programming in disguise, not harnessing the power of OOP.
Static methods thus need to be considered just like other forms of dependencies
when talking about dependency injection and even will need some special attention
to be replaced by more flexible means of dependency resolution.

### Singleton Classes

Singleton classes are classes that do not provide a public constructor, but
instead have a public and static method, often called `getInstance`, to retrieve
the one and only instance of said class. This instance is stored in a protected
static variable to make sure only one instance is ever created for each invocation
of the script. They thus can be considered a special case of static methods.

Similar to static methods, singleton classes seem to be mostly used for cases
where we either seem or seemed to have only one unique object for the targeted
functionality of the class, e.g. the session of the user. Singleton classes can
also be considered to be some caching mechanism that works internal to one
invocation of a script.



## Why do we want to use Dependency Injection?

The current ways to resolve dependencies in ILIAS certainly do their job in
resolving dependencies, since they obviously lead to a working and, in some sense,
modular system. Still there are well known advantages for a system that uses
dependency injection. To improve the understanding of these advantages and how
they could play out for ILIAS specifically, we want to reiterate some of them.

### Reduce Coupling Between Components

We might want to build a modular system for various reasons, but two advantages
of modular systems that come to mind quite quick are *composability* and *extensibility*.
Think of legos: individual components (bricks) can be combined via shared interfaces
(the studs and tubes) to an infinite variety of forms. A lego piece we bought can
be extended by other bricks and thus be turned into something completely different.
The coupling between the bricks is loose in the sense, that they do not depend
on each other and only need to agree on some very small interface to form a large
piece.

In ILIAS, on the other hand, we often can't break out pieces of the software, since
they are coupled together strongly. If one uses *inline instantiation* to create
an instance of some class by name in some other class, that other class strongly
dependends on the first class. We cannot simply replace it because we will need
to edit some code in the class to do so. If we use *static methods* to call some
functionality or `getInstance` from some *singleton class*, that usage can only
be replaced by similar means.

This often leads to a system architecture that exhibits the *highlander principle*.
There can only be one. One implementation for, e.g., logging. One implementation
for database connectivity. One implementation for a repository tree. When the use
cases of the software become more diverse or we have different factions in the
community, where e.g. one wants to experiment with some functionality and the other
opts for stability instead, we might discover that a highlander is not want we
want after all. If, for whatever reason, we discover that we somehow want or need
to accomodate more use cases or requirements, we are often tempted to make the
highlander more powerful, e.g. by adding options and configuration. This leads
to complex code when these options are implemented internally in classes via
control flow operations.

*Service locator* improves the situation a little, because we then can replace
dependencies on some instance by replacing it in the service locator. But what
we get in turn then is a dependency on the whole system. Via the service locator
every component can basically pull in all other services. We thus trade a weakened
coupling on an individual component by introdcing a coupling to all components.
The highlander principle also is only reduced slightly, because at runtime, we
still only have one implementation per dependency, although it might be easy to
use the one or the other dependency per process.

With *dependency injection* it is possible that each component declares which
other services it dependes on and that these dependencies are resolved at runtime,
possibly even differently per component. Instead of, e.g. using the same logger
per component, we could use a logger that writes to some file for basic components
of the system like reading an ini-file, a logger that writes to a logging service
for the components that we are interested in, and a logger that only stubs the
required interface but in fact does nothing for components we are not interested
in. Moreover, with a dependency injection container we can encode that wiring of
dependencies in defined places, allowing people to simply change the wiring to
fulfill special requirements, experiment with new implementations for dependencies
or mix parts of ILIAS with other systems.

### Make Core Easier to Understand and Test

Introducing dependency injection into the ILIAS core will basically involve two
activities. For a given class, we will need to pin down which exact requirements
this class has regarding its dependencies, most likely in form of an interface. 
We will then need to move the location where these dependencies are retrieved to
typehints in the constructor of the component.

In the first step, we will most likely discover, that the concrete implementations
that the components currently use are too broad in terms of their surface. This
can, e.g., be observed for the `ilLng`. By far the most uses of `ilLng` are via
the `txt` method. Still, every user of `ilLng` may also ask for user settings
or request usage information for topics or modules, which is another concern. We
might then introduce a smaller, more specific, interface and implement it for
the class.

When we then hint against that smaller interface in the constructor of the class,
future readers of the code will know what we intent to do with the dependency, and
of course also what we don't intend to do. This will make it easier to grasp what 
the purpose of a given class might be, because we can simply look at a constructor
to answer the question what it can do. Note, that with service locator the answer
to this question is just: this can do anything, including launching nuclear
missiles or even removing stuff from the database.

This has tremendous impact on how we can understand our software. When reviewing
code, we could easily spot, what is missing. Why doesn't this GUI-class request
`ilAccessHandler`? Did someone forget RBAC-checks? We could also understand, what
we don't need to consider. Has this class any impact on the security of our files?
No, because it does not request a filesystem dependency. Is this class doing to
much? Most certainly yes, if it pulls in a dozen dependencies.

From a technical perspective, this translates to classes that are easier to test.
Every dependency is a source of potential input and output. Huge dependencies
are huge sources thereof. When we understand tests as devices that model input
and test the resulting output, we can see that smaller and maybe less dependencies
simply means that there a less things that we need to test and model.

Finally, limiting the surface and amount of dependencies also means limiting the
possible ways in which a problem could be solved. Should I use `ilRbacReview` or
`ilAccessHandler`? Should I use some settings object or query the database directly?
In the best case, the dependencies of a class not only limit its possible
implementations, but rather make the implementation obvious, to write and to 
verify, automatically and manually.

To heave that treasure, however, it is not enough to just repeat the complete
surface of an implementation in an interface. Instead we need to chop existing
surfaces into multiple interfaces, or even have methods on a class that are
not implementations of an interface. `ilDBInterface` for instance, does just
that: repeat, what `ilDBPdo` offers as public interface. From this perspective
here we have gained nothing. Users of `ilDBInterface` could still drop tables,
although we expect them to just perform queries. Affairs would be more obvious
if we, e.g., had a `ilDBRead`, `ilDBWrite` and `ilDBManagement` interfaces, even
if they still would be implemented by the same class.

### More Talk About Interfaces

Interfaces are expression of certain boundaries that we deliberately chose to
introduce into our system. We do that because we learned at some point, that
having all code in one long file, without no structure what so ever, just won't
cut it, except for the most simple of problems. If we don't introduce procedures
we will hardly be able to reuse any functionality. If we don't use classes or
similar means to group functionality and state, we simply won't be able to
understand which part of data is modified where and why. If we don't put effort
into organizing our code, every project will quickly turn into a big ball of mud
or a plate of spaghetti. In fact, a lot of the actual work we are doing as coders
is thinking about and introducing these exact boundaries.

Using interfaces to hide concrete implementations behind them can be understood
as just another form of boundaries in our system that become handy at some point.
While procedures or classes could be viewed as means to group code together,
interfaces are means to tell code apart. When using an interface we express
that we don't care what's actually behind it, as long as it fullfills the contract,
and we also promise to not reach into concrete details of how this contract is
fullfilled.

This is a healthy practices, as it allows us to think about smaller problems and
move the focus from the "how" of an implementation to the "what". What is it,
that we actually need from a service doing access control? What is it that
other developers want to rely upon when using a filesystem? These intentional
divides in our systems allow us to forget about the whole when we need too,
and replace or duplicate parts we want to be different. From a higher perspective,
the divides will show what is important in our system and what is just a disposable
detail.

### Streamlined and Faster Initialisation

### Improve Extensibility and Customizability


## Why would we want to maintain the current state for dependency resulution?


## How should the system look like when we are finished?

### Internal and external dependency management

### Interfaces define interaction

### Components visibly declare their interfaces

### Composition is done in defined locations

## How can we move to the final state incrementally?

### Dicto Rule to remove `$DIC`

### Dicto Rule to deprecate `static`

## Which problems will we face and how can we overcome them?
