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

We begin by explaining the core considerations that should be taken into account
when designing measures to improve the security of input processing in ILIAS.
We go on by explaining recently implemented libraries to improve the input
processing and showcase it at the forms in the UI-Framework.

We then evaluate requirements from components of ILIAS that currently do not
implement systematical approaches to security when processing input. From there
we derive which enhancements and extensions to ILIAS are required to span the
components currently not included in the systematical approach to input security
and how these can be implemented technically as well as socially.

## Core Considerations

### Embrace Reality of ILIAS Development

### Primitive Obsession

### API-Design as Central Issue

### Feedback when Rejecting Input

## State of the Art: Core Libraries

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
