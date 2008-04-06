#!/usr/bin/python

cvsRevision = "$Revision: 1.51 $"
cvsDate = "$Date: 2007-12-14 22:18:26 $"
VERSION = '%s %s' % ( cvsRevision.split()[1], cvsDate.split()[1])

NOTICE = '\
## This "starparser.py" IEML Expression Parser\n\
## converts IEML "Star Language" expressions to XML.\n\
## Copyright 2007 Pierre Levy.  Version %s.\n\
\n\
## This software is licensed under the Apache\n\
## License, Version 2.0 (the "License"); you may\n\
## not use this file except in conformance with the\n\
## License.  Copies of the License are available\n\
## at http://www.apache.org/licenses/LICENSE-2.0\n\
\n\
## NOTICE: Unless required by applicable law or\n\
## agreed to in writing, software distributed\n\
## under the License is distributed on an "AS\n\
## IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS\n\
## OF ANY KIND, either express or implied.  See\n\
## the License for the specific language\n\
## governing permissions and limitations under\n\
## the License.\n\
\n\
## Copies of the source code and the license are\n\
## available at http://www.ieml.org\n\
\n\
### Written by Steven R. Newcomb, Coolheads Consulting\n\
###            Michel R. Biezunski, Infoloom\n\
### First version delivered 2007-11-27.\n\
' % ( VERSION)

### The dtd for parsing the output of this program can be seen by
### using the -outputDtd option.  This will put the DTD directly into
### the output file.

# This parser resolves the syntax down to a level of syntactic atoms,
# and it can report the result as tokens (using the -tokens argument)
# and as XML (using the -raw argument).  *Then* it does any filling
# with I's or E's that the ~ or other layer marks have specified, and
# any copying of source to destination and translator that has been
# specified by the ! layer mark.  The output of this second phase is
# the normal output of the parser.

# This parser does not evaluate expressions in such a way as to yield
# sets.  Tools for this and other purposes are expected to follow.

# In an IEML expression that is passed to the parser, the leading star
# and the trailing pair of stars can be either be present or omitted.

# In the paragraph below, star_expression[ 0] is the string '*', and
#                         star_expression[ 1] is the string '**'.

# When the leading and trailing stars are *present*, the string to be
# parsed can begin with 0 or more whitespace characters, followed by
# star_expression[ 0], followed by optional characters that are not
# star_expression[ 1], followed by star_expression[ 1], followed by
# any characters at all.  The IEML expression that will be parsed is
# between star_expression[ 0] and star_expression[ 1], and everything
# else is ignored.

# If the leading and trailing stars are *absent*, it is assumed that
# the entire string is what would normally appear between
# star_expression[ 0] and star_expression[ 1].  It is assumed that
# BOTH star_expression[ 0] and star_expression[ 1] have been omitted.

# In any case, stars can appear in comments and instantiators, and
# they will be preserved.

#  Stars absent:
#    O:M:/$I like stars **$/[I * like stars **].M:O:.-

#  Stars present:
#    *O:M:/$I like stars **$/[I * like stars **].M:O:.-**

dtdString = '\
<!--\n\
The following %setET; and %setAtrs; parameter entities have been\n\
expanded everywhere they were formerly invoked, in order to\n\
comply with the XML restriction that parameter entities\n\
are not processed when occurring in the internal subset.\n\
-->\n\
<!--\n\
\n\
<!ENTITY % setET " I | F | E | M | O | U | A | S | B | T | wo |\n\
wa | wu | we | y | o | e | u | a | i | j | g | h | c | p | x | s\n\
| b | t | k | m | n | d | f | l | emptyEvent | emptyRelation |\n\
emptyIdea | emptyPhrase | emptySeme | completeEvent |\n\
completeRelation | completeIdea | completePhrase | completeSeme\n\
| group | union | intersection | difference | genOp |\n\
undeterminedSubsetOf | diagonal " >\n\
\n\
-->\n\
\n\
<!--\n\
<!ENTITY % setAtrs "\n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase | seme\n\
            ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
">-->\n\
\n\
<!--\n\
    commentsAndInstantiators : Any comments and/or instantiators\n\
                  associated with this set.  These are\n\
                  associated with the set that appears at the\n\
                  level that is indicated by the following layer\n\
                  mark.  For example: in *M:$comment$.** the\n\
                  comment is associated with the set of events\n\
                  terminated by the dot.  Similarly, in\n\
                  *M:[instantiator].** the instantiator is\n\
                  associated with the set of events terminated\n\
                  by the dot.  A commentsAndInstantiators\n\
                  attribute can have as its value any number of\n\
                  instantiators and comments, in the order in\n\
                  which they appeared (or should appear) in the\n\
                  star expression.  This is why their delimiters\n\
                  are preserved.\n\
\n\
          layer : The layer of all of the members of the set.\n\
\n\
           role : The role that this set plays in the containing\n\
                  category expression (layer n+1).\n\
\n\
       fillWith : "I" to fill with I (the completion operator\n\
                  was used).\n\
\n\
                  "E" to fill with E (the layer mark truncated\n\
                  the expression of a generative operation\n\
                  immediately after the source or destination.\n\
\n\
                  "S" to copy source to destination (and\n\
                  translator, too, if the layer number > 3) (the\n\
                  role player duplication operator was used).\n\
\n\
          first : A decimal number representing the number of\n\
                  characters in the entire IEML expression that\n\
                  appear before the first character of the IEML\n\
                  construct represented by this particular XML\n\
                  element.  If the IEML expression begins with\n\
                  this construct, the value of the "first"\n\
                  attribute is 0.\n\
\n\
           last : A decimal number representing the number of\n\
                  characters in the entire IEML expression that\n\
                  appear before the last character of the IEML\n\
                  construct represented by this particular XML\n\
                  element.\n\
\n\
-->\n\
\n\
<!-- A whole IEML expression.  The document element type. -->\n\
<!-- IEML stands for Information Economy Meta Language. -->\n\
\n\
<!ELEMENT ieml ( ( I | F | E | M | O | U | A | S | B | T | wo |\n\
                   wa | wu | we | y | o | e | u | a | i | j | g\n\
                   | h | c | p | x | s | b | t | k | m | n | d |\n\
                   f | l | emptyEvent | emptyRelation |\n\
                   emptyIdea | emptyPhrase | emptySeme |\n\
                   completeEvent | completeRelation |\n\
                   completeIdea | completePhrase | completeSeme\n\
                   | group | union | intersection | difference |\n\
                   genOp | undeterminedSubsetOf | diagonal ),\n\
\n\
                 ( ( I | F | E | M | O | U | A | S | B | T | wo\n\
                     | wa | wu | we | y | o | e | u | a | i | j\n\
                     | g | h | c | p | x | s | b | t | k | m | n\n\
                     | d | f | l | emptyEvent | emptyRelation |\n\
                     emptyIdea | emptyPhrase | emptySeme |\n\
                     completeEvent | completeRelation |\n\
                     completeIdea | completePhrase |\n\
                     completeSeme | group | union | intersection\n\
                     | difference | genOp | undeterminedSubsetOf\n\
                     | diagonal ),\n\
\n\
                   ( I | F | E | M | O | U | A | S | B | T | wo\n\
                     | wa | wu | we | y | o | e | u | a | i | j\n\
                     | g | h | c | p | x | s | b | t | k | m | n\n\
                     | d | f | l | emptyEvent | emptyRelation |\n\
                     emptyIdea | emptyPhrase | emptySeme |\n\
                     completeEvent | completeRelation |\n\
                     completeIdea | completePhrase |\n\
                     completeSeme | group | union | intersection\n\
                     | difference | genOp | undeterminedSubsetOf\n\
                     | diagonal )?)? )>\n\
\n\
<!ATTLIST ieml\n\
    expressionString CDATA #IMPLIED\n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase \n\
            | seme ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
>\n\
\n\
<!-- The "Information" primitive category -->\n\
<!ELEMENT I EMPTY>\n\
<!ATTLIST I\n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase \n\
            | seme ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
>\n\
\n\
<!-- The "Full" primitive category -->\n\
<!ELEMENT F EMPTY>\n\
<!ATTLIST F\n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase \n\
            | seme ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
>\n\
\n\
<!-- The "Empty" (placeholder) primitive category. -->\n\
<!ELEMENT E EMPTY>\n\
<!ATTLIST E\n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase \n\
            | seme ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
>\n\
\n\
<!-- The "Nouns" primitive category -->\n\
<!ELEMENT M EMPTY>\n\
<!ATTLIST M\n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase \n\
            | seme ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
>\n\
\n\
<!-- The "Verbs" primitive category -->\n\
<!ELEMENT O EMPTY>\n\
<!ATTLIST O\n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase \n\
            | seme ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
>\n\
\n\
<!-- The "Virtual" primitive category -->\n\
<!ELEMENT U EMPTY>\n\
<!ATTLIST U\n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase \n\
            | seme ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
>\n\
\n\
<!-- The "Actual" primitive category -->\n\
<!ELEMENT A EMPTY>\n\
<!ATTLIST A\n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase \n\
            | seme ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
>\n\
\n\
<!-- The "Sign" primitive category -->\n\
<!ELEMENT S EMPTY>\n\
<!ATTLIST S\n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase \n\
            | seme ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
>\n\
\n\
<!-- The "Being" primitive category -->\n\
<!ELEMENT B EMPTY>\n\
<!ATTLIST B\n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase \n\
            | seme ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
>\n\
\n\
<!-- The "Thing" primitive category -->\n\
<!ELEMENT T EMPTY>\n\
<!ATTLIST T\n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase \n\
            | seme ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
>\n\
\n\
\n\
<!-- The "wo" event category -->\n\
<!ELEMENT wo EMPTY>\n\
<!ATTLIST wo\n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase \n\
            | seme ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
>\n\
\n\
<!-- The "wa" event category -->\n\
<!ELEMENT wa EMPTY>\n\
<!ATTLIST wa\n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase \n\
            | seme ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
>\n\
\n\
<!-- The "wu" event category -->\n\
<!ELEMENT wu EMPTY>\n\
<!ATTLIST wu\n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase \n\
            | seme ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
>\n\
\n\
<!-- The "we" event category -->\n\
<!ELEMENT we EMPTY>\n\
<!ATTLIST we\n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase \n\
            | seme ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
>\n\
\n\
<!-- The "y" event category -->\n\
<!ELEMENT y EMPTY>\n\
<!ATTLIST y\n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase \n\
            | seme ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
>\n\
\n\
<!-- The "o" event category -->\n\
<!ELEMENT o EMPTY>\n\
<!ATTLIST o\n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase \n\
            | seme ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
>\n\
\n\
<!-- The "e" event category -->\n\
<!ELEMENT e EMPTY>\n\
<!ATTLIST e\n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase \n\
            | seme ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
>\n\
\n\
<!-- The "u" event category -->\n\
<!ELEMENT u EMPTY>\n\
<!ATTLIST u\n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase \n\
            | seme ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
>\n\
\n\
<!-- The "a" event category -->\n\
<!ELEMENT a EMPTY>\n\
<!ATTLIST a\n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase \n\
            | seme ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
>\n\
\n\
<!-- The "i" event category -->\n\
<!ELEMENT i EMPTY>\n\
<!ATTLIST i\n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase \n\
            | seme ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
>\n\
\n\
<!-- The "j" event category -->\n\
<!ELEMENT j EMPTY>\n\
<!ATTLIST j\n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase \n\
            | seme ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
>\n\
\n\
<!-- The "g" event category -->\n\
<!ELEMENT g EMPTY>\n\
<!ATTLIST g\n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase \n\
            | seme ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
>\n\
\n\
<!-- The "h" event category -->\n\
<!ELEMENT h EMPTY>\n\
<!ATTLIST h\n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase \n\
            | seme ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
>\n\
\n\
<!-- The "c" event category -->\n\
<!ELEMENT c EMPTY>\n\
<!ATTLIST c\n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase \n\
            | seme ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
>\n\
\n\
<!-- The "p" event category -->\n\
<!ELEMENT p EMPTY>\n\
<!ATTLIST p\n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase \n\
            | seme ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
>\n\
\n\
<!-- The "x" event category -->\n\
<!ELEMENT x EMPTY>\n\
<!ATTLIST x\n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase \n\
            | seme ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
>\n\
\n\
<!-- The "s" event category -->\n\
<!ELEMENT s EMPTY>\n\
<!ATTLIST s\n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase \n\
            | seme ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
>\n\
\n\
<!-- The "b" event category -->\n\
<!ELEMENT b EMPTY>\n\
<!ATTLIST b\n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase \n\
            | seme ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
>\n\
\n\
<!-- The "t" event category -->\n\
<!ELEMENT t EMPTY>\n\
<!ATTLIST t\n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase \n\
            | seme ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
>\n\
\n\
<!-- The "k" event category -->\n\
<!ELEMENT k EMPTY>\n\
<!ATTLIST k\n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase \n\
            | seme ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
\n\
>\n\
\n\
<!-- The "m" event category -->\n\
<!ELEMENT m EMPTY>\n\
<!ATTLIST m\n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase \n\
            | seme ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
>\n\
\n\
<!-- The "n" event category -->\n\
<!ELEMENT n EMPTY>\n\
<!ATTLIST n\n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase \n\
            | seme ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
>\n\
\n\
<!-- The "d" event category -->\n\
<!ELEMENT d EMPTY>\n\
<!ATTLIST d\n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase \n\
            | seme ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
>\n\
\n\
<!-- The "f" event category -->\n\
<!ELEMENT f EMPTY>\n\
<!ATTLIST f\n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase \n\
            | seme ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
>\n\
\n\
<!-- The "l" event category -->\n\
<!ELEMENT l EMPTY>\n\
<!ATTLIST l\n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase \n\
            | seme ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
>\n\
\n\
<!-- The "emptyEvent" placeholder category. -->\n\
<!ELEMENT emptyEvent EMPTY>\n\
<!ATTLIST emptyEvent\n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase \n\
            | seme ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
>\n\
\n\
<!-- The "emptyRelation" placeholder category. -->\n\
<!ELEMENT emptyRelation EMPTY>\n\
<!ATTLIST emptyRelation\n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase \n\
            | seme ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
>\n\
\n\
<!-- The "emptyIdea" placeholder category. -->\n\
<!ELEMENT emptyIdea EMPTY>\n\
<!ATTLIST emptyIdea\n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase \n\
            | seme ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
>\n\
\n\
<!-- The "emptyPhrase" placeholder category. -->\n\
<!ELEMENT emptyPhrase EMPTY>\n\
<!ATTLIST emptyPhrase\n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase \n\
            | seme ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
>\n\
\n\
<!-- The "emptySeme" placeholder category. -->\n\
<!ELEMENT emptySeme EMPTY>\n\
<!ATTLIST emptySeme\n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase \n\
            | seme ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
\n\
>\n\
\n\
\n\
<!-- The "completeEvent" (all possibilities) category. -->\n\
<!ELEMENT completeEvent EMPTY>\n\
<!ATTLIST completeEvent\n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase \n\
            | seme ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
>\n\
\n\
<!-- The "completeRelation" (all possibilities) category. -->\n\
<!ELEMENT completeRelation EMPTY>\n\
<!ATTLIST completeRelation\n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase \n\
            | seme ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
>\n\
\n\
<!-- The "completeIdea" (all possibilities) category. -->\n\
<!ELEMENT completeIdea EMPTY>\n\
<!ATTLIST completeIdea\n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase \n\
            | seme ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
>\n\
\n\
<!-- The "completePhrase" (all possibilities) category. -->\n\
<!ELEMENT completePhrase EMPTY>\n\
<!ATTLIST completePhrase\n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase \n\
            | seme ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
>\n\
\n\
<!-- The "completeSeme" (all possibilities) category. -->\n\
<!ELEMENT completeSeme EMPTY>\n\
<!ATTLIST completeSeme\n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase \n\
            | seme ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
>\n\
\n\
<!-- Container for a non-generative set operation, expressed\n\
     as a parenthesis in STAR-IEML. -->\n\
<!ELEMENT group ( I | F | E | M | O | U | A | S | B | T | wo |\n\
                  wa | wu | we | y | o | e | u | a | i | j | g |\n\
                  h | c | p | x | s | b | t | k | m | n | d | f\n\
                  | l | emptyEvent | emptyRelation | emptyIdea |\n\
                  emptyPhrase | emptySeme | completeEvent |\n\
                  completeRelation | completeIdea |\n\
                  completePhrase | completeSeme | group | union\n\
                  | intersection | difference | genOp |\n\
                  undeterminedSubsetOf | diagonal )>\n\
\n\
<!ATTLIST group\n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase \n\
            | seme ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
>\n\
\n\
\n\
<!-- "union" non-generative set operation. -->\n\
<!ELEMENT union ( ( setOp | union | difference |\n\
                    intersection),\n\
                  ( setOp | union | difference |\n\
                    intersection))>\n\
<!ATTLIST union\n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase \n\
            | seme ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
>\n\
\n\
<!-- "intersection" non-generative set operation. -->\n\
<!ELEMENT intersection ( ( setOp | union | difference |\n\
                           intersection),\n\
                         ( setOp | union | difference |\n\
                           intersection))>\n\
<!ATTLIST intersection\n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase \n\
            | seme ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
>\n\
\n\
<!-- "difference" non-generative set operation. -->\n\
<!ELEMENT difference ( ( setOp | union | difference |\n\
                         intersection),\n\
                       ( setOp | union | difference |\n\
                         intersection))>\n\
<!ATTLIST difference\n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase \n\
            | seme ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
\n\
>\n\
\n\
<!-- an operand in a non-generative set operation -->\n\
<!ELEMENT setOp ( I | F | E | M | O | U | A | S | B | T | wo |\n\
                  wa | wu | we | y | o | e | u | a | i | j | g\n\
                  | h | c | p | x | s | b | t | k | m | n | d\n\
                  | f | l | emptyEvent | emptyRelation |\n\
                  emptyIdea | emptyPhrase | emptySeme |\n\
                  completeEvent | completeRelation |\n\
                  completeIdea | completePhrase | completeSeme\n\
                  | group | union | intersection | difference\n\
                  | genOp | undeterminedSubsetOf | diagonal )>\n\
\n\
<!ATTLIST setOp\n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase \n\
            | seme ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
>\n\
\n\
<!-- an operand in a generative set operation -->\n\
<!ELEMENT genOp ( ( I | F | E | M | O | U | A | S | B | T | wo |\n\
                    wa | wu | we | y | o | e | u | a | i | j | g\n\
                    | h | c | p | x | s | b | t | k | m | n | d\n\
                    | f | l | emptyEvent | emptyRelation |\n\
                    emptyIdea | emptyPhrase | emptySeme |\n\
                    completeEvent | completeRelation |\n\
                    completeIdea | completePhrase | completeSeme\n\
                    | group | union | intersection | difference\n\
                    | genOp | undeterminedSubsetOf | diagonal ),\n\
\n\
                 ( ( I | F | E | M | O | U | A | S | B | T | wo\n\
                     | wa | wu | we | y | o | e | u | a | i | j\n\
                     | g | h | c | p | x | s | b | t | k | m | n\n\
                     | d | f | l | emptyEvent | emptyRelation |\n\
                     emptyIdea | emptyPhrase | emptySeme |\n\
                     completeEvent | completeRelation |\n\
                     completeIdea | completePhrase |\n\
                     completeSeme | group | union | intersection\n\
                     | difference | genOp | undeterminedSubsetOf\n\
                     | diagonal ),\n\
\n\
                 ( I | F | E | M | O | U | A | S | B | T | wo |\n\
                   wa | wu | we | y | o | e | u | a | i | j | g\n\
                   | h | c | p | x | s | b | t | k | m | n | d |\n\
                   f | l | emptyEvent | emptyRelation |\n\
                   emptyIdea | emptyPhrase | emptySeme |\n\
                   completeEvent | completeRelation |\n\
                   completeIdea | completePhrase | completeSeme\n\
                   | group | union | intersection | difference |\n\
                   genOp | undeterminedSubsetOf | diagonal )?)?\n\
                   )>\n\
\n\
<!ATTLIST genOp\n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase \n\
            | seme ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
>\n\
\n\
\n\
<!-- Container for an expression of a set that is known to be a\n\
     superset of the intended set.  In STAR-IEML, the superset\n\
     appears within curly braces {}.  -->\n\
<!ELEMENT undeterminedSubsetOf ( I | F | E | M | O | U | A | S |\n\
                                 B | T | wo | wa | wu | we | y |\n\
                                 o | e | u | a | i | j | g | h |\n\
                                 c | p | x | s | b | t | k | m |\n\
                                 n | d | f | l | emptyEvent |\n\
                                 emptyRelation | emptyIdea |\n\
                                 emptyPhrase | emptySeme |\n\
                                 completeEvent |\n\
                                 completeRelation | completeIdea\n\
                                 | completePhrase | completeSeme\n\
                                 | group | union | intersection\n\
                                 | difference | genOp |\n\
                                 undeterminedSubsetOf | diagonal\n\
                                 )>\n\
\n\
<!ATTLIST undeterminedSubsetOf\n\
    parameterIdentifier CDATA #IMPLIED  \n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase \n\
            | seme ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
>\n\
<!-- \n\
\n\
    parameterIdentifier : If present, this is the identifying\n\
                          number of the undetermined subset in\n\
                          the expression.\n\
             E.g.: For <I:29>, this is "29".\n\
                   For <I>, there is no "parameterIdentifier" \n\
                   attribute.\n\
-->\n\
\n\
<!-- Container for an expression of a generative operand that is\n\
     intended to be the set which is the intersection of the set\n\
     expressed by the operand and the set(s) which is/are\n\
     expressed by at least one other generative operand at the\n\
     same layer in the same IEML expression.  In STAR-IEML, the\n\
     operand appears within angle brackets <>.  -->\n\
<!ELEMENT diagonal ( I | F | E | M | O | U | A | S | B | T | wo\n\
                     | wa | wu | we | y | o | e | u | a | i | j\n\
                     | g | h | c | p | x | s | b | t | k | m | n\n\
                     | d | f | l | emptyEvent | emptyRelation |\n\
                     emptyIdea | emptyPhrase | emptySeme |\n\
                     completeEvent | completeRelation |\n\
                     completeIdea | completePhrase |\n\
                     completeSeme | group | union | intersection\n\
                     | difference | genOp | undeterminedSubsetOf\n\
                     | diagonal )>\n\
\n\
<!ATTLIST diagonal\n\
    parameterIdentifier CDATA #IMPLIED  \n\
    commentsAndInstantiators CDATA #IMPLIED\n\
    layer ( primitive | event | relation | idea | phrase \n\
            | seme ) #IMPLIED\n\
    role ( source | destination | translator) #IMPLIED\n\
    fillWith ( I | E | S ) #IMPLIED\n\
    first CDATA #REQUIRED\n\
    last CDATA #REQUIRED\n\
>\n\
<!-- \n\
    parameterIdentifier : If present, this is the identifying\n\
                          number of the diagonal operand (of a\n\
                          generative set operation) in the\n\
                          expression.\n\
             E.g.: For {I:29}, this is "29".\n\
                   For {I:}, there is no "parameterIdentifier" \n\
                   attribute.\n\
-->\n\
'
dtdString = '\
<!-- XML-based IEML Syntax Document Type Definition (DTD)\n\
     version %s -->\n%s' % (
    VERSION,
    dtdString,
)




import os, re, sys, string, types, subprocess
from xml.etree import cElementTree as ET


star_I = 'I'
star_F = 'F'
star_E = 'E'
star_M = 'M'
star_O = 'O'
star_U = 'U'
star_A = 'A'
star_S = 'S'
star_B = 'B'
star_T = 'T'

star_wo = 'wo'
star_wa = 'wa'
star_wu = 'wu'
star_we = 'we'
star_y = 'y'
star_o = 'o'
star_e = 'e'
star_u = 'u'
star_a = 'a'
star_i = 'i'
star_j = 'j'
star_g = 'g'
star_h = 'h'
star_c = 'c'
star_p = 'p'
star_x = 'x'
star_s = 's'
star_b = 'b'
star_t = 't'
star_k = 'k'
star_m = 'm'
star_n = 'n'
star_d = 'd'
star_f = 'f'
star_l = 'l'

primitiveAndEventTokenTypesToXMLGis = {
    'star_I': 'I',
    'star_F': 'F',
    'star_E': 'E',
    'star_M': 'M',
    'star_O': 'O',
    'star_U': 'U',
    'star_A': 'A',
    'star_S': 'S',
    'star_B': 'B',
    'star_T': 'T',

    'star_wo': 'wo',
    'star_wa': 'wa',
    'star_wu': 'wu',
    'star_we': 'we',
    'star_y': 'y',
    'star_o': 'o',
    'star_e': 'e',
    'star_u': 'u',
    'star_a': 'a',
    'star_i': 'i',
    'star_j': 'j',
    'star_g': 'g',
    'star_h': 'h',
    'star_c': 'c',
    'star_p': 'p',
    'star_x': 'x',
    'star_s': 's',
    'star_b': 'b',
    'star_t': 't',
    'star_k': 'k',
    'star_m': 'm',
    'star_n': 'n',
    'star_d': 'd',
    'star_f': 'f',
    'star_l': 'l',


}
xmlGisToPrimitiveAndEventTokenTypes = {
    'I': 'star_I',
    'F': 'star_F',
    'E': 'star_E',
    'M': 'star_M',
    'O': 'star_O',
    'U': 'star_U',
    'A': 'star_A',
    'S': 'star_S',
    'B': 'star_B',
    'T': 'star_T',

    'wo': 'star_wo',
    'wa': 'star_wa',
    'wu': 'star_wu',
    'we': 'star_we',
    'y': 'star_y',
    'o': 'star_o',
    'e': 'star_e',
    'u': 'star_u',
    'a': 'star_a',
    'i': 'star_i',
    'j': 'star_j',
    'g': 'star_g',
    'h': 'star_h',
    'c': 'star_c',
    'p': 'star_p',
    'x': 'star_x',
    's': 'star_s',
    'b': 'star_b',
    't': 'star_t',
    'k': 'star_k',
    'm': 'star_m',
    'n': 'star_n',
    'd': 'star_d',
    'f': 'star_f',
    'l': 'star_l',
}


wMap = {
    star_wo[ 1]: 'star_wo',
    star_wa[ 1]: 'star_wa',
    star_wu[ 1]: 'star_wu',
    star_we[ 1]: 'star_we',
}

eventSymbolTokenTypeNames = [
    'star_wo',
    'star_wa',
    'star_wu',
    'star_we',
    'star_y',
    'star_o',
    'star_e',
    'star_u',
    'star_a',
    'star_i',
    'star_j',
    'star_g',
    'star_h',
    'star_c',
    'star_p',
    'star_x',
    'star_s',
    'star_b',
    'star_t',
    'star_k',
    'star_m',
    'star_n',
    'star_d',
    'star_f',
    'star_l',
]

star_eventSymbols = [
    star_wo,
    star_wa,
    star_wu,
    star_we,
    star_y,
    star_o,
    star_e,
    star_u,
    star_a,
    star_i,
    star_j,
    star_g,
    star_h,
    star_c,
    star_p,
    star_x,
    star_s,
    star_b,
    star_t,
    star_k,
    star_m,
    star_n,
    star_d,
    star_f,
    star_l,
]


star_expression = [ '*', '**']

star_whitespace = [ ' ', '\t', '\n', ]

star_group = [ '(', ')']

star_union = '|'
star_intersection = '&'
star_difference = '^'
setOperationMap = {
    star_union: 'union',
    star_intersection: 'intersection',
    star_difference: 'difference',
}


star_undeterminedSubsetOf = [ '<', '>']
star_comment = [ '/$', '$/']
star_instantiator = [ '[', ']']
star_diagonal = [ '{', '}']

## star_generators = ';'  ## these are not part of the star language
## star_generateds = '%'
## star_stack = '#'

star_tildeLayerMark = '~'
star_bangLayerMark = '!'
star_primitiveLayerMark = ":"
star_eventLayerMark = '.'
star_relationLayerMark = '-'
star_ideaLayerMark = "'"
star_phraseLayerMark = ','
star_semeLayerMark = '_'

star_layerMarks = [
    star_primitiveLayerMark,
    star_eventLayerMark,
    star_relationLayerMark,
    star_ideaLayerMark,
    star_phraseLayerMark,
    star_semeLayerMark,
]
layerNameMap = {
         star_tildeLayerMark: 'star_tildeLayerMark',
          star_bangLayerMark: 'star_bangLayerMark',
     star_primitiveLayerMark: 'star_primitiveLayerMark',
         star_eventLayerMark: 'star_eventLayerMark',
      star_relationLayerMark: 'star_relationLayerMark',
          star_ideaLayerMark: 'star_ideaLayerMark',
        star_phraseLayerMark: 'star_phraseLayerMark',
          star_semeLayerMark: 'star_semeLayerMark',
}
layerNamesToLayerMarks = {
    'primitive': star_primitiveLayerMark,
    'event': star_eventLayerMark,
    'relation': star_relationLayerMark,
    'idea': star_ideaLayerMark,
    'phrase': star_phraseLayerMark,
    'seme': star_semeLayerMark,
}

layerMarkTokenTypes = {
                       'star_bangLayerMark': -1,
                      'star_tildeLayerMark': -2,
                  'star_primitiveLayerMark': 1,
                      'star_eventLayerMark': 2,
                   'star_relationLayerMark': 3,
                       'star_ideaLayerMark': 4,
                     'star_phraseLayerMark': 5,
                       'star_semeLayerMark': 6,
}
layerMarkTokenTypesB = {
    'star_primitiveLayerMark': 1,
        'star_eventLayerMark': 2,
     'star_relationLayerMark': 3,
         'star_ideaLayerMark': 4,
       'star_phraseLayerMark': 5,
         'star_semeLayerMark': 6,
}
roleNumberToRoleName = {
    1: 'source',
    2: 'destination',
    3: 'translator',
}

star_primitiveSymbols = [
    star_I,
    star_F,
    star_E,
    star_M,
    star_O,
    star_U,
    star_A,
    star_S,
    star_B,
    star_T,
]

star_primitiveAndEventSymbols = star_primitiveSymbols[ :]
star_primitiveAndEventSymbols.extend( star_eventSymbols)

star_primitiveTokenTypeNames = [
    'star_I',
    'star_F',
    'star_E',
    'star_M',
    'star_O',
    'star_U',
    'star_A',
    'star_S',
    'star_B',
    'star_T',
]
star_primitiveAndEventTokenTypeNames = star_primitiveTokenTypeNames[ :]
star_primitiveAndEventTokenTypeNames.extend( eventSymbolTokenTypeNames)

singleCharacterMap = {
    star_primitiveLayerMark: 'star_primitiveLayerMark',
    star_eventLayerMark: 'star_eventLayerMark',
    star_relationLayerMark: 'star_relationLayerMark',
    star_ideaLayerMark: 'star_ideaLayerMark',
    star_phraseLayerMark: 'star_phraseLayerMark',
    star_semeLayerMark: 'star_semeLayerMark',
    star_I: 'star_I',
    star_F: 'star_F',
    star_E: 'star_E',
    star_M: 'star_M',
    star_O: 'star_O',
    star_U: 'star_U',
    star_A: 'star_A',
    star_S: 'star_S',
    star_B: 'star_B',
    star_T: 'star_T',
    star_y: 'star_y',
    star_o: 'star_o',
    star_e: 'star_e',
    star_u: 'star_u',
    star_a: 'star_a',
    star_i: 'star_i',
    star_j: 'star_j',
    star_g: 'star_g',
    star_h: 'star_h',
    star_c: 'star_c',
    star_p: 'star_p',
    star_x: 'star_x',
    star_s: 'star_s',
    star_b: 'star_b',
    star_t: 'star_t',
    star_k: 'star_k',
    star_m: 'star_m',
    star_n: 'star_n',
    star_d: 'star_d',
    star_f: 'star_f',
    star_l: 'star_l',
}

operandContainerTokenTypes = {
               'iemlExpression': None,
             'nonterminalGroup': None,
                'terminalGroup': None,
'nonterminalUndeterminedSubset': None,
   'terminalUndeterminedSubset': None,
          'nonterminalDiagonal': None,
             'terminalDiagonal': None,
                   'setOperand': None,
                        'union': None,
                   'difference': None,
                 'intersection': None,
}

operandContainerTokenTypesToGis = {
             'nonterminalGroup': 'group',
                'terminalGroup': 'group',
'nonterminalUndeterminedSubset': 'undeterminedSubsetOf',
   'terminalUndeterminedSubset': 'undeterminedSubsetOf',
          'nonterminalDiagonal': 'diagonal',
             'terminalDiagonal': 'diagonal',
                   'setOperand': 'setOp',  ## set operand
                        'union': 'union',
                   'difference': 'difference',
                 'intersection': 'intersection',
                        'genOp': 'genOp',  ## generative operand
}

groupTokenTypeNames = {
             'nonterminalGroup': None,
                'terminalGroup': None,
'nonterminalUndeterminedSubset': None,
   'terminalUndeterminedSubset': None,
          'nonterminalDiagonal': None,
             'terminalDiagonal': None,
}
setOperationTokenTypeNames = {
                        'union': None,
                   'difference': None,
                 'intersection': None,
}

iAndEElementTypeNames = {
    'I': {
        'primitive': 'I',
            'event': 'completeEvent',
         'relation': 'completeRelation',
             'idea': 'completeIdea',
           'phrase': 'completePhrase',
             'seme': 'completeSeme',
    },
    'E': {
        'primitive': 'E',
            'event': 'emptyEvent',
         'relation': 'emptyRelation',
             'idea': 'emptyIdea',
           'phrase': 'emptyPhrase',
             'seme': 'emptySeme',
    },
}

layerNumberToLayerName = {
    1: 'primitive',
    2: 'event',
    3: 'relation',
    4: 'idea',
    5: 'phrase',
    6: 'seme',
}
layerNameToLayerNumber = {
    'primitive': 1,
        'event': 2,
     'relation': 3,
         'idea': 4,
       'phrase': 5,
         'seme': 6,
}    

class token:
    def __init__( self, tokenType, zubTokens, thisString = None, **kwargs):
        global iemlExpressionString

        self.parentToken = None
        self.tokenType = tokenType
        self.zubTokenLists = [ zubTokens]

        dontShow = False
        self.first = None
        self.last = None

        self.inherentLayer = None

        for kwargKey in kwargs:
            if kwargKey == 'first':
                self.first = kwargs[ kwargKey]
                if not kwargs.has_key( 'last'):
                    errMsg( 'last keyword argument not specified', 'traceback')
                    sys.exit( 1)
            elif kwargKey == 'last':
                self.last = kwargs[ kwargKey]
                if not kwargs.has_key( 'first'):
                    errMsg( 'first keyword argument not specified', 'traceback')
                    sys.exit( 1)
            elif kwargKey == 'inherentLayer':
                self.inherentLayer = kwargs[ 'inherentLayer']
            elif kwargKey == 'comInsts':
                self.comInsts = kwargs[ 'comInsts']
            elif kwargKey == 'dontShow':
                dontShow = True
            elif kwargKey == 'fillWith':
                self.fillWith = kwargs[ kwargKey]
            elif kwargKey == 'layerMarkPosition':
                self.layerMarkPosition = kwargs[ kwargKey]
            elif kwargKey == 'isSdtAtLayer':
                self.isSdtAtLayer = kwargs[ kwargKey]
            else:
                errMsg( 'no such keyword argument: "%s"' % ( kwargKey), 'traceback')
                sys.exit( 1)

        if self.first == None:
            self.first = self.zubTokenLists[ 0][ 0].first
            self.last = self.zubTokenLists[ 0][ -1].last

        for zubToken in self.zubTokenLists[ 0]:
            zubToken.parentToken = self

        if thisString != None:
            self.string = thisString
        else:
            thisString = ''
            for zubToken in self.zubTokenLists[ 0]:
                thisString = '%s%s' % ( thisString, zubToken.string)
            self.string = thisString

if len( star_expression[ 1]) > 1:
    iemlStartRE = re.compile( '(^\s*?)(%s)([^%s].*$)' % (
        re.escape( star_expression[ 0]),
        re.escape( star_expression[ 1][ 1]),
    ))
else:
    iemlStartRE = re.compile( '(^\s*?)(%s)(.*$)' % (
        re.escape( star_expression[ 0]),
    ))

iemlStartAndEndRE = re.compile( '(^\s*?)(%s)(.*?)(%s)(.*$)' % (
    re.escape( star_expression[ 0]),
    re.escape( star_expression[ 1])
))

iemlExpressionString = ''

iemlSymbolTokenList = []

XMLINDENT = 2

debug = False

#######################################################
#######################################################
#messaging.py
#this is a module used for messaging.  It allows multiple classes
#to handle various types of messages.  It should work on all python
#versions >= 1.5.2
# written by Christian Bird
# downloaded and adapted by SRN from
# http://aspn.activestate.com/ASPN/Cookbook/Python/Recipe/144838 on
# 20040323

####################################
cvsRevision = "$Revision: 1.51 $" ##
####################################

import sys, string, exceptions, traceback, types, os

#this flag determines whether debug output is sent to debug handlers themselves
debug = True

def sendDebugMessages(debugging):
    global debug
    debug = debugging

class MessagingException(exceptions.Exception):
    """an exception class for any errors that may occur in 
    a messaging function"""
    def __init__(self, args=None):
        self.args = args

class FakeException(exceptions.Exception):
    """an exception that is thrown and then caught
    to get a reference to the current execution frame"""
    pass        
        
        
class MessageHandler:
    """All message handlers should inherit this class.  Each method will be 
    passed a string when the executing program passes calls a messaging function"""
    def handleStdMsg(self, msg):
        """do something with a standard message from the program"""
        pass
    def handleErrMsg(self, msg):
        """do something with an error message.  This will already include the
        class, method, and line of the call"""
        pass
    def handleDbgMsg(self, msg):
        """do something with a debug message.  This will already include the
        class, method, and line of the call"""
        pass

class defaultMessageHandler(MessageHandler):
    """This is a default message handler.  It simply spits all strings to
    standard out"""
    def handleStdMsg(self, msg):
        if msg.endswith( '\n'):
            sys.stdout.write( msg)
        else:
            sys.stdout.write( msg + "\n")
    def handleErrMsg(self, msg):
        if msg.endswith( '\n'):
            sys.stderr.write( msg)
        else:
            sys.stderr.write( msg + "\n")
        sys.stderr.write(msg + "\n")
    def handleDbgMsg(self, msg):
        if msg.endswith( '\n'):
            sys.stdout.write( msg)
        else:
            sys.stdout.write( msg + "\n")

#this keeps track of the handlers
_messageHandlers = []

#call this with the handler to register it for receiving messages
def registerMessageHandler(handler):
    """we're not going to check for inheritance, but we should check to make
    sure that it has the correct methods"""
    for methodName in ["handleStdMsg", "handleErrMsg", "handleDbgMsg"]:
        try:
            getattr(handler, methodName)
        except:            
            raise MessagingException, "The class " + handler.__class__.__name__ + " is missing a " + methodName + " method"
    _messageHandlers.append(handler)
    
    
def getCallString(level):
    #this gets us the frame of the caller and will work
    #in python versions 1.5.2 and greater (there are better
    #ways starting in 2.1
    try:
        raise FakeException("this is fake")
    except Exception, e:
        #get the current execution frame
        f = sys.exc_info()[2].tb_frame
    #go back as many call-frames as was specified
    while level >= 0:        
        f = f.f_back
        level = level-1
    #if there is a self variable in the caller's local namespace then
    #we'll make the assumption that the caller is a class method
    obj = f.f_locals.get("self", None)
    functionName = f.f_code.co_name
    fileName = f.f_code.co_filename
    if obj:
        callStr = fileName+' : '+obj.__class__.__name__+"::"+functionName+"() (line "+str(f.f_lineno)+")"
    else:
        callStr = fileName+':'+functionName+"() (line "+str(f.f_lineno)+")"        
    return callStr        
    
#send this message to all handlers of std messages
def stdMsg(*args):
    newArgs = []
    for arg in args:
        if isinstance( arg, types.StringType):
            argToAppend = arg
        elif isinstance( arg, types.UnicodeType):
            argToAppend = arg.encode( 'utf-8')
        else:
            argToAppend = repr( arg).encode( 'utf-8')
        newArgs.append( argToAppend)
    args = newArgs
    stdStr = string.join(map(str, args), " ")
    for handler in _messageHandlers:
        handler.handleStdMsg(stdStr)

#send this message to all handlers of error messages
def errMsg(*args):
    newArgs = []
    do_traceback = False
    for arg in args:
        if isinstance( arg, types.StringType):
            if arg.lower() == 'traceback':
                do_traceback = True
            else:
                newArgs.append( arg)
        elif isinstance( arg, types.UnicodeType):
            if arg.lower() == 'traceback':
                do_traceback = True
            else:
                newArgs.append( arg.encode( 'utf-8'))
        else:
            newArgs.append( repr( arg).encode( 'utf-8'))
    args = newArgs
    errStr = "ERROR "+os.path.split( sys.argv[ 0])[ 1]+":"+getCallString(1)+" : "+string.join(map(str, args), " ")
    if do_traceback:
        errStr = errStr+''.join( traceback.format_list( traceback.extract_stack()))
    for handler in _messageHandlers:
        handler.handleDbgMsg(errStr)

#send this message to all handlers of debug messages
def dbgMsg(*args):
    if not debug:
        return
    newArgs = []
    do_traceback = False
    for arg in args:
        if isinstance( arg, types.StringType):
            if arg.lower() == 'traceback':
                do_traceback = True
            else:
                newArgs.append( arg)
        elif isinstance( arg, types.UnicodeType):
            if arg.lower() == 'traceback':
                do_traceback = True
            else:
                newArgs.append( arg.encode( 'utf-8'))
        else:
            newArgs.append( repr( arg).encode( 'utf-8'))
    args = newArgs
    errStr = "DEBUG "+os.path.split( sys.argv[ 0])[ 1]+":"+getCallString(1)+" : "+string.join(map(str, args), " ")
    if do_traceback:
        errStr = errStr+''.join( traceback.format_list( traceback.extract_stack()))
    for handler in _messageHandlers:
        handler.handleDbgMsg(errStr)


registerMessageHandler(defaultMessageHandler())
#end of messaging.py
#######################################################
#######################################################

nameSpaceReg = re.compile( '{.*}(.*)' )

INDENT = 2

docTypeSystemStr = '(^<!DOCTYPE\s+ieml\s+SYSTEM\s+")([^"]+)(">$)'
docTypeSystemRE = re.compile( docTypeSystemStr)

docTypePublicStr = '(^<!DOCTYPE\s+ieml\s+PUBLIC\s+")([^"]+)(">$)'
docTypePublicRE = re.compile( docTypePublicStr)

#######################################################
def buildNewTree(oldTreeRoot, newTreeRoot):
    global highestLayer
    
    highestLayer = oldTreeRoot.attrib[ 'layer']
    firstChild = oldTreeRoot.getchildren()[ 0]
    if firstChild.attrib.has_key( 'fillWith'):
        if firstChild.attrib[ 'fillWith'] != 'E':
            userError( 'The within a group or whole expression, the rightmost layermark cannot be ~ nor !%s' % (
                showPlaceInString(
                    iemlExpressionString,
                    firstChild.attrib[ 'last'],
                    firstChild.attrib[ 'last'],
##                     firstChild.attrib[ 'layerMarkPosition'],
##                     firstChild.attrib[ 'layerMarkPosition'],
                ),
            ))
            sys.exit( 1)

    if oldTreeRoot.text:
        newTreeRoot.text = oldTreeRoot.text
    if oldTreeRoot.tail:
        newTreeRoot.tail = oldTreeRoot.tail

    _buildNewTree( oldTreeRoot, newTreeRoot)

#######################################################
def _buildNewTree( oldParent, newParent):

    global highestLayer

    oldChildren = oldParent.getchildren()
    childCounter = 0
    while childCounter < len( oldChildren):
        child = oldChildren[ childCounter]
###
##         print 'child.tag == "%s", attrib="%s"' % ( child.tag, child.attrib)
##         pdb.set_trace()
###
        newAttrib = child.attrib.copy()
        try:
            del newAttrib[ 'fillWith']
        except KeyError:
            pass
        if child.tag in xmlGisToPrimitiveAndEventTokenTypes:
            try:
                del newAttrib[ 'layer']
            except KeyError:
                pass

        newSubelement = ET.SubElement( newParent, child.tag, newAttrib)
        if child.text:
            newSubelement.text = child.text
        if child.tail:
            newSubelement.tail = child.tail
        _buildNewTree( child, newSubelement)
        if childCounter == len( oldChildren) - 1:  ## this is the last child
            if not child.attrib.has_key( 'fillWith'): break
            if oldParent.tag in [ 'setOp', 'group', 'undeterminedSubsetOf', 'diagonal'] and child.attrib[ 'fillWith'] != 'E':
                if child.attrib[ 'fillWith'] == 'S':
                    actualLayerMark = star_bangLayerMark
                else:  ## must be I
                    actualLayerMark = star_tildeLayerMark
                betterLayerMark = layerNamesToLayerMarks[ child.attrib[ 'layer']]
                userError( '\
The content of the following %s:%s\n\
uses a %s as the rightmost layer mark\n\
for the set expressed by the %s.\n\
Within any such set, the rightmost layer mark must be one of\n%s\n\
.  You could replace the %s with a %s\n\
Is that what you meant?\n' % (
                    oldParent.tag,
                    showPlaceInString(
                        iemlExpressionString,
                        child.attrib[ 'first'],
                        child.attrib[ 'last'],
                    ),
                    actualLayerMark,
                    oldParent.tag,
                    ' %s %s %s %s %s or %s  ' % (
                        star_primitiveLayerMark,
                        star_eventLayerMark,
                        star_relationLayerMark,
                        star_ideaLayerMark,
                        star_phraseLayerMark,
                        star_semeLayerMark,
                    ),
                    actualLayerMark,
                    betterLayerMark,
                ))
                sys.exit( 1)
            if not child.attrib.has_key( 'role'): break
            if newParent.attrib[ 'layer'] == 'primitive':
                break
            if child.attrib[ 'layer'] == highestLayer:
                break
            if not oldParent.attrib.has_key( 'fillWith'): break
            newLayer = layerNumberToLayerName [layerNameToLayerNumber[ newParent.attrib[ 'layer']] - 1]

            if child.attrib[ 'fillWith'] == 'E' or child.attrib[ 'fillWith'] == 'I':
                for roleNumber in range( childCounter + 2, 4):
                    if newLayer in [ 'primitive', 'event'] and roleNumber == 3:
                        iOrEElementTypeName = iAndEElementTypeNames[ 'E'][ newLayer]
                    else:
                        iOrEElementTypeName = iAndEElementTypeNames[ child.attrib[ 'fillWith']][ newLayer]
                    newSubelement = ET.SubElement(
                        newParent,
                        'genOp',
                        {
                            'role': roleNumberToRoleName[ roleNumber],
                            'layer': newLayer,
                            'first': child.attrib[ 'first'],
                            'last': child.attrib[ 'last'],
                        },
                    )
                    newSubSubelement = ET.SubElement(
                        newSubelement,
                        iOrEElementTypeName,
                        {
                            'first': child.attrib[ 'first'],
                            'last': child.attrib[ 'last'],
                        },
                    )
            else:    ## child.attrib[ 'fillWith'] == 'S':
                if len( oldChildren) > 1:
                    userError( 'If you want to copy the source, you can\'t already have a destination.%s' % (
                        showPlaceInString( iemlExpressionString,
                                           child.attrib[ 'first'],
                                           child.attrib[ 'last'],
                                         ),
                    ))
                    sys.exit( 1)
                
                for roleNumber in range( childCounter + 2, 4):
                    newAttrib[ 'role'] = roleNumberToRoleName[ roleNumber]
                    if newLayer in [ 'primitive', 'event'] and roleNumber == 3:
                        iOrEElementTypeName = iAndEElementTypeNames[ 'E'][ newLayer]
                        newSubelement = ET.SubElement(
                            newParent,
                            'genOp',
                            {
                                'role': roleNumberToRoleName[ roleNumber],
                                'layer': newLayer,
                                'first': child.attrib[ 'first'],
                                'last': child.attrib[ 'last'],
                            },
                        )
                        newSubSubelement = ET.SubElement(
                            newSubelement,
                            iOrEElementTypeName,
                            {
                                'first': child.attrib[ 'first'],
                                'last': child.attrib[ 'last'],
                            },
                        )
                        
                    else:
                        newSubelement = ET.SubElement( newParent, child.tag, newAttrib)
                        if child.text:
                            newSubelement.text = child.text
                        if child.tail:
                            newSubelement.tail = child.tail
                        _buildNewTree( child, newSubelement)


        childCounter += 1

#######################################################
def printElem( element):
    sys.stdout.write( prettyXml( element))

#######################################################
def printElemList( elementList):
    for element in elementList:
        printElem( element)

#######################################################
def prettyXml( child):
    global prettyXMLMsg, tagClose

    prettyXMLMsg = ''
    tagClose = ''
    _prettyXml( child, 0)

    if len( tagClose) > 0:
        prettyXMLMsg = '%s%s\n' % (
            prettyXMLMsg,
            tagClose,
        )

    return prettyXMLMsg

#######################################################
attOrder = {
            'expressionString': 1,
                       'layer': 2,
                        'role': 3,
          'parameterIdentifier': 4,
    'commentsAndInstantiators': 5,
                    'fillWith': 6,
                       'first': 7,
                        'last': 8,
##            'layerMarkPosition': 9,
}
def iemlAttrSort( a, b):
    z = attOrder[ a] - attOrder[ b]
    if z > 0:
        return 1
    elif z < 0:
        return -1
    else:
        return 0


#######################################################
def attrString( indentLength, attrDict):
    attrString = ''
    attrNames = attrDict.keys()
    attrNames.sort( iemlAttrSort)
    indentStr = ' '
    if len( attrNames) > 0:
        for attrName in attrNames:
            attrString = '%s%s%s="%s"\n' % ( attrString, indentStr, attrName, attrDict[ attrName])
            indentStr = ' '*indentLength
    else:
        attrString = '\n'
    return attrString


#######################################################
def _prettyXml(child, indent = 0):
    global prettyXMLMsg, tagClose

    pcdata = ''
    if child.text:
        pcdata = child.text

    tail = ''
    if child.tail:
        tail = child.tail

    # starttag stuff here

    if tagClose == '':
        tagCloseStr = ''
    else:
        if not prettyXMLMsg.endswith( '\n'):
            prettyXMLMsg = '%s\n' % ( prettyXMLMsg)
        tagCloseStr = '%s%s' % (
            ' '*indent*INDENT,
            tagClose
        )
        tagClose = ''

    prettyXMLMsg = '%s%s<%s' % (
        prettyXMLMsg,
        tagCloseStr,
        child.tag,
    )
    indx = prettyXMLMsg.rfind( '\n')
    if indx == -1:
        lastLineLength = len( prettyXMLMsg) + 1
    else:
        lastLineLength = ( len( prettyXMLMsg) - indx)
    prettyXMLMsg = '%s%s' % (
        prettyXMLMsg,
        attrString( lastLineLength, child.attrib),
    )
    if len( child.getchildren()) == 0 and pcdata == '':
        tagClose = '/>'
        endTagNeeded = False
    else:
        tagClose = '>'
        endTagNeeded = True

    if len( pcdata) > 0:
        if len( tagClose) > 0:
            prettyXMLMsg = '%s%s%s%s' % (
                prettyXMLMsg,
                ' '*indent*INDENT,
                tagClose,
                pcdata,
            )
            tagClose = ''
        else:
            prettyXMLMsg = '%s%s' % (
                prettyXMLMsg,
                pcdata,
            )

    # internal elements here
    for thisChild in child.getchildren():
        _prettyXml( thisChild, indent + 1)

    if len( tail) > 0:
        if len( tagClose) > 0:
            if not prettyXMLMsg.endswith( '\n'):
                prettyXMLMsg = '%s\n' % ( prettyXMLMsg)
            prettyXMLMsg = '%s%s%s' % (
                prettyXMLMsg,
                ' '*indent*INDENT,
                tagClose,
            )
            tagClose = ''
        prettyXMLMsg = '%s%s' % (
            prettyXMLMsg,
            tail
        )

    # endtag stuff here
    if endTagNeeded:
        if not prettyXMLMsg.endswith( '\n'):
            prettyXMLMsg = '%s\n' % ( prettyXMLMsg)
        prettyXMLMsg = '%s%s%s' % (
            prettyXMLMsg,
            ' '*indent*INDENT,
            tagClose,
        )
        tagClose = ''

        prettyXMLMsg = '%s</%s' % ( 
            prettyXMLMsg,
            child.tag,
        )
        tagClose = '>'


#######################################################
def showPlaceInString( s, first, last, indent=0, INDENT=2, **kwargs):

    if not isinstance( first, types.IntType):
        first = int( first)

    if not isinstance( last, types.IntType):
        last = int( last)

    if kwargs.has_key( 'appendToSecondLine'):
        appendToSecondLineString = kwargs[ 'appendToSecondLine']
    else:
        appendToSecondLineString = ''
    controlString = '\n%%s"%%s"\n%%s %%-%d.%ds  %%s' % ( len( s), len( s))

    if last != None:
        caretString = '%s%s' % ( ' '*first, '^'*( ( last - first) + 1),)
    else:
        caretString = ' ' * len( s)

    return controlString % (
        ' '*INDENT*indent,
        s.replace( '\n', ' ').replace( '\t', ' ').replace( '\r', ' '),
        ' '*INDENT*indent,
        caretString,
        appendToSecondLineString,
    )



#######################################################
def usage( errorMsg, exitStatus):

    if errorMsg:
        print '\nError: %s\n' % ( errorMsg)

    print """
Usage: %s [options] [<IEML expression>]

Options:
         -i  <input file path>  Default: (IEML expression is 
                provided as an argument). Use '-' for stdin.

         -o  <output path for cooked (final) XML output> 
                Default: stdout  ('-')

    -tokens  <output file for token output> Default: (none). For
                debugging.  Use '-' for stdout.

       -raw  <output file for raw parsed XML output> Default: (none)
                For debugging.  This output includes "fillWith"
                attributes; they have not yet been acted upon.  (And
                there are other differences with the "cooked" form.)
                Use '-' for stdout.

-xmlSocPath  <path to XML declaration for nsgmls parsing>
                Default: no parsing by nsgmls will be done.

 -dtdSystem  <path to show in the DOCTYPE declaration (SYSTEM)>
                Default: no DOCTYPE declaration will appear in the XML
                output, and unless the -outputDtd or -dtdPublic is
                used, the XML will not be checked by nsgmls.

 -dtdPublic  <path to show in the DOCTYPE declaration (PUBLIC)>
                Default: no DOCTYPE declaration will appear in the XML
                output, and unless the -outputDtd or -dtdSystem is
                used, the XML will not be checked by nsgmls.

 -outputDtd  Include the DTD in the XML that is output.

   -dtdOnly  Output the dtd only, and quit.

         -h  (Show this help information.)

     -about  (Show the license notice, etc.)
""" % ( os.path.split( sys.argv[ 0])[ 1])

    if errorMsg:
        print '\nError: %s\n' % ( errorMsg)

    sys.exit( exitStatus)

#######################################################
def dispatch( thisToken):
    global dispatchDict

    for function in dispatchDict[ thisToken.tokenType]:
        targetTokenList = []
        function( thisToken.zubTokenLists[ -1], targetTokenList, thisToken)
        if len( targetTokenList) > 0:
            for targetToken in targetTokenList:
                setattr( targetToken, 'parentToken', thisToken)
            thisToken.zubTokenLists.append( targetTokenList)
            
#######################################################
def main():
    global iemlExpressionString, iemlExpressionToken, inputFilePath, outputFilePath, tokensOutputFilePath, rawXmlOutputFilePath, xmlSocPath, dtdSystem, dtdPublic, tokensFO, outputDtd, VERSION

    iemlExpressionString = None

    inputFilePath = None
    outputFilePath = None
    tokensOutputFilePath = None
    rawXmlOutputFilePath = None
    
    xmlSocPath = None

    dtdSystem = None
    dtdPublic = None

    outputDtd = False

    argCounter = 1
    while argCounter < len( sys.argv):
        arg = sys.argv[ argCounter]
        
        if arg == '-i':
            argCounter += 1
            inputFilePath = sys.argv[ argCounter]
        elif arg == '-o':
            argCounter += 1
            outputFilePath = sys.argv[ argCounter]
        elif arg == '-tokens':
            argCounter += 1
            tokensOutputFilePath = sys.argv[ argCounter]
        elif arg == '-raw':
            argCounter += 1
            rawXmlOutputFilePath = sys.argv[ argCounter]
        elif arg == '-xmlSocPath':
            argCounter += 1
            xmlSocPath = sys.argv[ argCounter]
        elif arg == '-dtdSystem':
            argCounter += 1
            dtdSystem = sys.argv[ argCounter]
        elif arg == '-dtdPublic':
            argCounter += 1
            dtdPublic = sys.argv[ argCounter]
        elif arg == '-dtdOnly':
            sys.stdout.write( dtdString)
            sys.exit( 0)
        elif arg == '-dtdOnlyForHtml':
            sys.stdout.write( dtdString.replace( ' ', '&#160;').replace( '<', '&lt;').replace( '>', '&gt;').replace( '\n', '<br/>\n'))
            sys.exit( 0)
        elif arg == '-outputDtd':
            outputDtd = True
        elif arg == '-about':
            print NOTICE
            sys.exit( 0)
        elif arg.startswith( '-h'):
            usage( '', 0)
        else:
            if iemlExpressionString == None:
                iemlExpressionString = arg
            else:
                usage( 'Multiple IEML expression strings were specified:\n(1) "%s"\n(2) "%s"\n' % ( iemlExpressionString, arg), 1)

        argCounter += 1    

    if inputFilePath == None and iemlExpressionString == None:
        usage( 'Neither an input file nor an IEML expression were specified.', 1)
    elif inputFilePath != None:
        if inputFilePath == '-':
            iemlExpressionString = sys.stdin.read()
        else:
            inputFO = file( inputFilePath, 'r')
            iemlExpressionString = inputFO.read()
            inputFO.close()

    if outputFilePath == None:
        outputFO = sys.stdout
    elif outputFilePath == '-':
        outputFO = sys.stdout
    else:
        outputFO = file( outputFilePath, 'w')

    if tokensOutputFilePath == None:
        tokensFO = None
    elif tokensOutputFilePath == '-':
        tokensFO = sys.stdout
    else:
        tokensFO = file( tokensOutputFilePath, 'w')

    if rawXmlOutputFilePath == None:
        rawFO = None
    elif rawXmlOutputFilePath == '-':
        rawFO = sys.stdout
    else:
        rawFO = file( rawXmlOutputFilePath, 'w')

    if xmlSocPath != None:
        if not os.path.exists( xmlSocPath):
            usage( 'No such catalog file: "%s"' % ( xmlSocPath), 1)

    if dtdSystem != None:
        if not os.path.exists( dtdSystem):
            usage( 'no such dtd file: "%s"' % ( dtdSystem, 1))
        
    if dtdSystem != None and dtdPublic != None:
        usage( 'Both a SYSTEM and a PUBLIC dtd were specified.  Can\'t have both!', 1)

    if outputDtd and ( dtdSystem != None or dtdPublic != None):
        usage( 'You asked to output the DTD as part of the XML file, and at the same time you said that the DTD is in a file.  You can\'t have it both ways!', 1)

    iemlExpressionToken = parse( iemlExpressionString)  ## 1st stage of parsing

    rawXMLString = xmlifyToken( iemlExpressionToken, 0)
    if rawFO != None:
        rawFO.write( rawXMLString)
        if rawFO != sys.stdout:
            rawFO.close()
        else:
            rawFO.flush()

    if xmlSocPath != None and ( dtdSystem != None or dtdPublic != None or outputDtd):
        if rawFO != None and rawFO != sys.stdout:  ## there is a file containing the raw XML
            argVector = [
                    'nsgmls',
                    '-c',
                    xmlSocPath,
                    '-s',
                    rawXmlOutputFilePath,
            ]

            sys.stderr.write( 'Checking the raw XML output via %s.\n' % ( ' '.join( argVector)))

            subProc = subprocess.Popen(
                argVector,
            )
            subProc.wait()
            if subProc.returncode != 0:
                errMsg( 'raw xml output failed to pass parsing test')
                sys.exit( 1)
        else:  ## there is no file containing the raw XML; use a pipe instead
            argVector = [
                    'nsgmls',
                    '-c',
                    xmlSocPath,
                    '-s',
                    '-',
            ]

            sys.stderr.write( 'Checking the raw XML output via %s.\n' % ( ' '.join( argVector)))

            subProc = subprocess.Popen(
                argVector,
                stdin=subprocess.PIPE,
            )
            subProc.communicate( input=rawXMLString)
            if subProc.returncode != 0:
                errMsg( 'raw xml output failed to pass parsing test')
                sys.exit( 1)


    oldTreeRoot = ET.fromstring( rawXMLString)
    
    newRootElement = ET.Element(oldTreeRoot.tag)
    newAttrib = oldTreeRoot.attrib.copy()
    try:
        del newAttrib[ 'fillWith']
    except KeyError:
        pass
    try:
        del newAttrib[ 'layer']
    except KeyError:
        pass
    newRootElement.attrib = newAttrib
    newTree = ET.ElementTree( newRootElement)
    newTreeRoot = newTree.getroot()

    buildNewTree( oldTreeRoot, newTreeRoot)

    outputFO.write( '<?xml version="1.0"?>\n')
    if docTypeDecl != None:
        outputFO.write( docTypeDecl)
    
    finalXMLOutputString = prettyXml( newTree.getroot())
    outputFO.writelines( finalXMLOutputString)
    outputFO.flush()

    if xmlSocPath != None and ( dtdSystem != None or dtdPublic != None or outputDtd):
        if outputFO != None and outputFO != sys.stdout:  ## there is a file containing the raw XML
            argVector = [
                    'nsgmls',
                    '-c',
                    xmlSocPath,
                    '-s',
                    outputFilePath,
            ]

            sys.stderr.write( 'Checking the final XML output via %s.\n' % ( ' '.join( argVector)))

            subProc = subprocess.Popen(
                argVector,
            )
            subProc.wait()
            if subProc.returncode != 0:
                errMsg( 'final xml output failed to pass parsing test')
                sys.exit( 1)
        else:  ## there is no file containing the final XML; use a pipe instead
            argVector = [
                    'nsgmls',
                    '-c',
                    xmlSocPath,
                    '-s',
                    '-',
            ]

            sys.stderr.write( 'Checking the final XML output via %s.\n' % ( ' '.join( argVector)))

            subProc = subprocess.Popen(
                argVector,
                stdin=subprocess.PIPE,
            )
            subProc.communicate( input=rawXMLString)
            if subProc.returncode != 0:
                errMsg( 'final xml output failed to pass parsing test')
                sys.exit( 1)

#######################################################
def xmlifyToken( thisToken, indent):
    global tagClose, dtdSystem, dtdPublic, docTypeDecl, outputDtd, dtdString

    msg = ''

    msg = '%s%s\n' % ( msg, '<?xml version="1.0"?>')

    docTypeDecl = ''
    if dtdSystem != None:
        docTypeDecl = '<!DOCTYPE ieml SYSTEM "%s">' % ( dtdSystem)
        msg = '%s%s\n' % ( msg, docTypeDecl)
    elif dtdPublic != None:
        docTypeDecl = '<!DOCTYPE ieml PUBLIC "%s">' % ( dtdPublic)
        msg = '%s%s\n' % ( msg, docTypeDecl)
    elif outputDtd:
        docTypeDecl = '<!DOCTYPE ieml [\n%s\n]>' % ( dtdString)
    msg = '%s%s' % ( msg, docTypeDecl)

    msg = '%s%s\n' % ( msg, '<ieml expressionString="%s"%s' % (
        xmlEscape( iemlExpressionString),
        attributeString( thisToken)
    ))
    tagClose = '>'
    
    for zubToken in thisToken.zubTokenLists[ -1]:
        msg = '%s%s' % ( msg, _xmlifyToken( zubToken, indent + 1))

    msg = '%s%s%s\n' % ( msg, tagClose, '</ieml>')

    return msg


#######################################################
def xmlEscape( str):
    return str.replace( '&', '&amp;').replace( '<', '&lt;').replace( '>', '&gt;')

#######################################################
def _xmlifyToken( thisToken, indent):
    global tagClose

    msg = ''

    omitEndTag = False

    if thisToken.tokenType in operandContainerTokenTypesToGis:
        gi = operandContainerTokenTypesToGis[ thisToken.tokenType]
        
        msg = '%s%s%s\n' % ( ' '*indent*XMLINDENT,
                             msg,
                             '%s<%s%s' % ( tagClose, gi, attributeString( thisToken)))
        tagClose = '>'

    elif thisToken.tokenType in primitiveAndEventTokenTypesToXMLGis:
        gi = primitiveAndEventTokenTypesToXMLGis[ thisToken.tokenType]
        msg = '%s%s%s\n' % ( ' '*indent*XMLINDENT,
                             msg,
                             '%s<%s%s' % ( tagClose, gi, attributeString( thisToken)))
        omitEndTag = True
        tagClose = '/>'

    elif thisToken.tokenType in [ 'char']:
        omitEndTag = True

    elif thisToken.tokenType in layerMarkTokenTypes:
        omitEndTag = True

    else:
        errMsg( 'unhandled token type: %s' % ( thisToken.tokenType))
        sys.exit( 1)

    ## recursion here
    for otherToken in thisToken.zubTokenLists[ -1]:
        msg = '%s%s' % ( msg, _xmlifyToken( otherToken, indent + 1))
    ## back from recursive call here

    if not omitEndTag:
        msg = '%s%s%s\n' % ( msg, ' '*indent*XMLINDENT, '%s</%s' % ( tagClose, gi))
        tagClose = '>'

    return msg


#######################################################
def attributeString( thisToken):

    roleStr = ''
    if hasattr( thisToken, 'roleNumber'):
        try:
            roleStr = ' role="%s"' % ( roleNumberToRoleName[ thisToken.roleNumber])
        except KeyError:
            userError( 'There are only three roles, but this appears to play a fourth role!%s' % (
                showPlaceInString(
                    iemlExpressionString,
                    thisToken.first,
                    thisToken.last,
                ),
            ))
            sys.exit( 1)
            
    fillWithStr = ''
    if hasattr( thisToken, 'fillWith'):
        fillWithStr = ' fillWith="%s"' % ( thisToken.fillWith)

    layerAttrStr = ''
    if hasattr( thisToken, 'isSdtAtLayer'):
        layerAttrStr = ' layer="%s"' % ( layerNumberToLayerName[ thisToken.isSdtAtLayer])
    elif hasattr( thisToken, 'inherentLayer'):
        if thisToken.inherentLayer == None:
            userError( 'This is not interpretable in this context.%s' % (
                showPlaceInString(
                    iemlExpressionString,
                    thisToken.first,
                    thisToken.last,
                ),
            ))
        layerAttrStr = ' layer="%s"' % ( layerNumberToLayerName[ thisToken.inherentLayer])

    parameterIdentifierAttrStr = ''
    if hasattr( thisToken, 'parameterIdentifier'):
        parameterIdentifierAttrStr = ' parameterIdentifier="%s"' % ( thisToken.parameterIdentifier)

    commentAttrStr = ''
    if hasattr( thisToken, 'comInsts'):
        if len( thisToken.comInsts) > 0:
            comInstStr = ''
            for comInst in thisToken.comInsts:
                comInstStr = '%s%s' % ( comInstStr, comInst.string)
            commentAttrStr=' commentsAndInstantiators="%s"' % ( comInstStr)

    firstStr = ''
    if hasattr( thisToken, 'first'):
        firstStr = ' first="%s"' % ( thisToken.first)
        
    lastStr = ''
    if hasattr( thisToken, 'last'):
        lastStr = ' last="%s"' % ( thisToken.last)
        
    fwmlStr = ''
##     if hasattr( thisToken, 'layerMarkPosition'):
##         fwmlStr = ' layerMarkPosition="%s"' % ( thisToken.layerMarkPosition)

    return '%s%s%s%s%s%s%s' % (
##     return '%s%s%s%s%s%s%s%s' % (
        roleStr,
        fillWithStr,
        layerAttrStr,
        parameterIdentifierAttrStr,
        commentAttrStr,
        firstStr,
        lastStr,
##         fwmlStr,
    )

#######################################################
def parse( iemlExp):
    global iemlExpressionString, iemlExpressionToken, tokensFO
           
    preliminaryTokenList = []

    iemlExpressionString = iemlExp

    ## Find the last double star in the string
    expressionIsExplicitlyDelimited = False
    iemlExpressionBeforeAndAfterTheLastStarExpressionTerminator = \
        iemlExpressionString.rsplit( star_expression[ 1], 1)
    if len( iemlExpressionBeforeAndAfterTheLastStarExpressionTerminator) == 2:
        ## There is a double star.  Now we'll look at the string that precedes
        ## the double star, to see whether it has a single star optionally
        ## preceding by whitespace:
        iemlStartMO = iemlStartRE.match( iemlExpressionBeforeAndAfterTheLastStarExpressionTerminator[ 0])
        if iemlStartMO:
            ## Parser will now assume that is an explicitly delimited IEML expression.
            expressionIsExplicitlyDelimited = True
            if len( iemlStartMO.group( 1)):
                group1LastChar = iemlStartMO.end( 1) - 1
            else:
                group1LastChar = None
            
            if len( iemlStartMO.group( 1)) > 0:
                preliminaryTokenList.append(
                    token(
                        'leadingWhiteSpace',
                        makeListOfCharTokensFromString( iemlStartMO.group( 1), [], startPosition=0),
                        iemlStartMO.group( 1),
                        first=iemlStartMO.start( 1),
                        last=group1LastChar,
                    )
                )
            preliminaryTokenList.append(
                token(
                    'star_expression[ 0]',
                    makeListOfCharTokensFromString( iemlStartMO.group( 2), [], startPosition=iemlStartMO.start( 2)),
                    star_expression[ 0],
                    first=iemlStartMO.start( 2),
                    last=iemlStartMO.end( 2) - 1,
                )
            )

            preliminaryTokenList.append(
                token(
                    'iemlExpression',
                    makeListOfCharTokensFromString( iemlStartMO.group( 3), [], startPosition=iemlStartMO.start( 3)),
                    iemlStartMO.group( 3),
                    first=iemlStartMO.start( 3),
                    last=iemlStartMO.end( 3) - 1,
                )
            )
            iemlExpressionToken = preliminaryTokenList[ -1]
            iemlExpressionTokenNumber = len( preliminaryTokenList) - 1

            preliminaryTokenList.append(
                token(
                    'star_expression[ 1]',
                    makeListOfCharTokensFromString( star_expression[ 1], [], startPosition=iemlStartMO.end( 3)),
                    star_expression[ 1],
                    first=iemlStartMO.end( 3),
                    last=iemlStartMO.end( 3) + ( len( star_expression[ 1]) -1),
                )
            )
            if len( iemlExpressionBeforeAndAfterTheLastStarExpressionTerminator[ 1]) > 0:
                preliminaryTokenList.append(
                    token(
                        'trailingGarbage',
                        makeListOfCharTokensFromString(
                            iemlExpressionBeforeAndAfterTheLastStarExpressionTerminator[ 1],
                            [],
                            startPosition = iemlStartMO.end( 3) + len( star_expression[ 1]),
                        ),
                        iemlExpressionBeforeAndAfterTheLastStarExpressionTerminator[ 1],
                        first=iemlStartMO.end( 3) + len( star_expression[ 1]),
                        last=iemlStartMO.end( 3) + len( star_expression[ 1]) + \
                                len( iemlExpressionBeforeAndAfterTheLastStarExpressionTerminator[ 1]),
                    )
                )

    if not expressionIsExplicitlyDelimited:
        preliminaryTokenList.append(     ## this is the only token in the list
            token(
                'iemlExpression',
                makeListOfCharTokensFromString( iemlExpressionString, []),
                iemlExpressionString,
                first=0,
                last=len( iemlExpressionString) - 1,
            )
        )
        iemlExpressionToken = preliminaryTokenList[ -1]
        iemlExpressionTokenNumber = len( preliminaryTokenList) - 1

    dispatch( iemlExpressionToken)

    for zubToken in iemlExpressionToken.zubTokenLists[ -1]:
        if zubToken.tokenType == 'char':
            userError( 'Unprocessable character.%s' % (
                showPlaceInString(
                    iemlExpressionString,
                    zubToken.first,
                    zubToken.last,
                ),
            ))
            sys.exit( 1)

    characterizeGenOps( iemlExpressionToken)

    preliminaryTokenList[ iemlExpressionTokenNumber] = iemlExpressionToken

    if tokensFO != None:
        msg = ''
        msg = '%s<!--\niemlExpression = "%s"\n\n' % ( msg, iemlExpressionString)
        for myToken in preliminaryTokenList:
            msg = '%s%s' % ( msg, showToken( myToken, 0, suppressChar=True))

        msg = '%s\n-->\n' % ( msg)
        tokensFO.write( msg)
        if tokensFO != sys.stdout:
            tokensFO.close()
        else:
            tokensFO.flush()

    if len( iemlExpressionToken.zubTokenLists[ -1]) != 1:
        errMsg( 'The number of genOps in the ieml expression != 1')
        sys.exit( 1)

    return iemlExpressionToken

#######################################################
## This function is for debugging only.
def printTokenList( thisTokenList, indent = 0, levelsToShow = 9999999):

    msg = showTokenList( thisTokenList, indent, levelsToShow)
    sys.stdout.write( msg)

    return

#######################################################
## This function is for debugging only.
def printToken( thisToken, indent = 0, levelsToShow = 9999999):

    msg = showToken( thisToken, indent, levelsToShow)
    sys.stdout.write( msg)

    return

#######################################################
## This function is for debugging only.
def showTokenList( tokenList, indent = 0, levelsToShow = 999999):
    msg = ''
    for thisToken in tokenList:
        msg = '%s%s' % ( msg, showToken( thisToken, indent, levelsToShow))
    return msg

#######################################################
def showToken( thisToken, indent = 0, levelsToShow = 999999, **kwargs):

    if levelsToShow == 0: return ''

    INDENT = 3
    
    suppressChar = False
    for kwarg in kwargs:
        if kwarg == 'suppressChar':
            suppressChar = True
        else:
            errMsg( 'Unrecognized keyword arg "%s"' % ( kwarg))
            sys.exit( 1)

    if thisToken.tokenType == 'char' and suppressChar: return ''
        

    msg = ''
    msg = '%s%s\n' % (
        msg,
        showPlaceInString( iemlExpressionString,
                           thisToken.first,
                           thisToken.last,
                           indent, INDENT,
        ),
    )

    msg = '%s%s%-17.17s: %s\n' % (
        msg,
        ' '*indent*INDENT,
        'type',
        thisToken.tokenType,
    )

    try:
        msg = '%s%s%-17.17s: %s\n' % (
            msg,
            ' '*indent*INDENT,
            'parent type',
            thisToken.parentToken.tokenType,
        )
    except AttributeError:
        pass

    msg = '%s%s%-17.17s: %s\n' % ( msg, ' '*indent*INDENT, 'string', thisToken.string)

    if ( hasattr( thisToken, 'parameterIdentifier')):
        msg = '%s%s%-17.17s: %s\n' % ( msg, ' '*indent*INDENT, 'parameterIdentifier', thisToken.parameterIdentifier)
    if ( hasattr( thisToken, 'inherentLayer')):
        msg = '%s%s%-17.17s: %s\n' % ( msg, ' '*indent*INDENT, 'inherentLayer', thisToken.inherentLayer)
    if ( hasattr( thisToken, 'fillWith')):
        msg = '%s%s%-17.17s: %s\n' % ( msg, ' '*indent*INDENT, 'fillWith', thisToken.fillWith)
    if ( hasattr( thisToken, 'isSdtAtLayer')):
        msg = '%s%s%-17.17s: %s\n' % ( msg, ' '*indent*INDENT, 'isSdtAtLayer', thisToken.isSdtAtLayer)
    if ( hasattr( thisToken, 'roleNumber')):
        msg = '%s%s%-17.17s: %s\n' % ( msg, ' '*indent*INDENT, 'roleNumber', thisToken.roleNumber)

    if ( hasattr( thisToken, 'comInsts')):
        if len( thisToken.comInsts) > 0:
            msg = '%s%s%-17.17s: ' % ( msg, ' '*indent*INDENT, 'comInsts')
            for comInstToken in thisToken.comInsts:
                msg = '%s%s' % ( msg,
                                 showPlaceInString( iemlExpressionString,
                                                    comInstToken.first,
                                                    comInstToken.last,
                                                    indent+3, INDENT,
                                                    appendToSecondLine=comInstToken.tokenType,
                                 ),
                )
            msg = '%s\n' % ( msg)
    if ( hasattr( thisToken, 'instantiators')):
        if len( thisToken.instantiators) > 0:
            msg = '%s%s%-17.17s: ' % ( msg, ' '*indent*INDENT, 'instantiators')
            for instantiatorToken in thisToken.instantiators:
                msg = '%s%s' % ( msg,
                                 showPlaceInString( iemlExpressionString,
                                                    instantiatorToken.first,
                                                    instantiatorToken.last,
                                                    indent+3, INDENT,
                                 ),
                )
            msg = '%s\n' % ( msg)
    
    if len( thisToken.zubTokenLists) > 0:
        if len( thisToken.zubTokenLists[ -1]) > 0:
            for otherToken in thisToken.zubTokenLists[ -1]:
                msg = '%s%s' % ( msg, showToken( otherToken, indent + 1, levelsToShow - 1, suppressChar=suppressChar))
        elif len( thisToken.zubTokenLists) > 1:
            if len( thisToken.zubTokenLists[ -2]) > 0:
                for otherToken in thisToken.zubTokenLists[ -2]:
                    msg = '%s%s' % ( msg, showToken( otherToken, indent + 1, levelsToShow - 1, suppressChar=suppressChar))
    return msg

#######################################################
def characterizeGenOps( thisToken):

    for currentLayer in range( 1,7):
        _characterizeGenOps( thisToken, currentLayer)


            
#######################################################
def _characterizeGenOps( thisToken, currentLayer):

    for zubToken in thisToken.zubTokenLists[ -1]:         ## first, go deep.  Do bottom recursion levels first.
        if zubToken.tokenType in operandContainerTokenTypes and not hasattr( zubToken, 'isSdtAtLayer'):
            _characterizeGenOps( zubToken, currentLayer)

    ## first look for tokens with inherent layer, i.e. symbols for primitives and events
    if currentLayer in [ 1, 2]:  
        newTokenList = []
        zubTokenCounter1 = 0
        while zubTokenCounter1 < len( thisToken.zubTokenLists[ -1]):

            ## We look at all the tokens in the latest (i.e. [-1])
            ## list of zubTokens of thisToken.  If we find one that is
            ## inherently at the currentLayer, then we look for a
            ## layerMark after it (plus any intervening !, comments,
            ## or instantiators), and wrap everything (from the
            ## inherently-layered token to the layermark, inclusive)
            ## as an genOp token.

            ## Inherent layer characteristics:

            ## The primitive tokens U A S B T E F I can only be
            ## followed by one of [ ':', '~', '!'].  Anything else
            ## would be an error.

            ## The event tokens wo t  etc., can only be followed by
            ## one of [ '.', '~', '!']  Anything else would be an
            ## error.

            zubToken1 = thisToken.zubTokenLists[ -1][ zubTokenCounter1]
            if zubToken1.inherentLayer != currentLayer:
                newTokenList.append( zubToken1)
            else:  ##  zubToken1.inherentLayer == currentLayer:
                   ## now look forward to the layer marker
                tmpTokenList = [ zubToken1]

                zubTokenCounter2 = zubTokenCounter1 + 1
                comInsts = []
                foundLayerMark = False

                while zubTokenCounter2 < len( thisToken.zubTokenLists[ -1]):
                    zubToken2 = thisToken.zubTokenLists[ -1][ zubTokenCounter2]
                    if zubToken2.tokenType in layerMarkTokenTypes:
                        if layerMarkTokenTypes[ zubToken2.tokenType] == -2:  ## tilde
                            fillWith = 'I'
                        elif layerMarkTokenTypes[ zubToken2.tokenType] == currentLayer:
                            fillWith = 'E'
                        elif layerMarkTokenTypes[ zubToken2.tokenType] == -1:  ## bang
                            fillWith = 'S'
                        else:
                            userError( 'This layer mark %s is out of sequence.  A layer mark at layer %d was expected.%s' % (
                                zubToken2.tokenType,
                                currentLayer,
                                showPlaceInString(
                                    iemlExpressionString,
                                    zubToken2.first,
                                    zubToken2.last,
                                ),
                            ))
                            sys.exit( 1)

                        foundLayerMark = True
                        tmpTokenList.append( zubToken2)
                        newTokenList.append( token(
                            'genOp',
                            tmpTokenList,
                            isSdtAtLayer=currentLayer,
                            comInsts=comInsts,
                            fillWith=fillWith,
                            layerMarkPosition=zubToken2.first,
                        ))
                        tmpTokenList = []
                        comInsts = []
                        zubTokenCounter1 = zubTokenCounter2
                                ## this will be incremented at the
                                ## bottom of the outer loop, as usual,
                                ## so that we'll be in position for
                                ## the next token.
                        break
                    elif zubToken2.tokenType in [ 'comment', 'instantiator']:
                        comInsts.append( zubToken2)
                    else:
                        userError( 'Layer mark for layer %d is missing before here.%s' % (
                            currentLayer,
                            showPlaceInString(
                                iemlExpressionString,
                                zubToken2.first,
                                zubToken2.first,
                            ),
                        ))
                        sys.exit( 1)
                    zubTokenCounter2 += 1
                if not foundLayerMark:
                    userError( 'Layer mark for layer %d is missing here.%s' % (
                        currentLayer,
                        showPlaceInString(
                            iemlExpressionString,
                            zubToken1.first,
                            thisToken.zubTokenLists[ -1][ -1].last,
                        ),
                    ))
                    sys.exit( 1)

            zubTokenCounter1 += 1

        if len( newTokenList) > 0:
            
            allAreSdts = True
            allAreAtLayer = None
            for newToken in newTokenList:
                if not hasattr( newToken, 'isSdtAtLayer'):
                    allAreSdts = False
                else:
                    if allAreAtLayer == None:
                        allAreAtLayer = newToken.isSdtAtLayer
                    else:
                        if allAreAtLayer != newToken.isSdtAtLayer:
                            allAreAtLayer = False

            if allAreSdts and (( thisToken.tokenType not in setOperationTokenTypeNames and len( newTokenList) > 1) or \
                               ( thisToken.tokenType     in setOperationTokenTypeNames and len( newTokenList) > 2)) :
                userError( 'Too many operands in this %s.  There should be only one.%s' % (
                    thisToken.tokenType,
                    showPlaceInString(
                        iemlExpressionString,
                        thisToken.first,
                        thisToken.last,
                    ),
                ))
                sys.exit()

            if allAreSdts and allAreAtLayer:
                thisToken.isSdtAtLayer = allAreAtLayer
            thisToken.zubTokenLists.append( newTokenList)

    ## Now we look through the same list again (not the same if
    ## modified by the above code, of course), to see what can be
    ## identified as belonging to the current layer.  This is just
    ## like the above code, except now we're working at all levels and
    ## on all remaining tokens, instead of IEML tokens (primitive and
    ## event tokens) that are inherently at some layer.
    newTokenList = []
    zubTokenCounter1 = 0
    while zubTokenCounter1 < len( thisToken.zubTokenLists[ -1]):
        zubToken1 = thisToken.zubTokenLists[ -1][ zubTokenCounter1]
        if not hasattr( zubToken1, 'isSdtAtLayer'):
            newTokenList.append( zubToken1)
        else: ##   hasattr( zubToken1, 'isSdtAtLayer'):
            if zubToken1.isSdtAtLayer != currentLayer - 1:
                newTokenList.append( zubToken1)
            else:   ## zubToken1.isSdtAtLayer == currentLayer - 1:
                tmpTokenList = [ zubToken1]
                zubTokenCounter2 = zubTokenCounter1 + 1
                comInsts = []
                foundLayerMark = False
                while zubTokenCounter2 < len( thisToken.zubTokenLists[ -1]):
                    zubToken2 = thisToken.zubTokenLists[ -1][ zubTokenCounter2]
                    if zubToken2.tokenType in layerMarkTokenTypes:
                        if layerMarkTokenTypes[ zubToken2.tokenType] == -2:  ## tilde
                            fillWith = 'I'
                        elif layerMarkTokenTypes[ zubToken2.tokenType] == currentLayer:
                            fillWith = 'E'
                        elif layerMarkTokenTypes[ zubToken2.tokenType] == -1:  ## bang
                            fillWith = 'S'
                        elif layerMarkTokenTypes[ zubToken2.tokenType] < currentLayer:
                            ## this layer mark should already have been processed by now
                            userError( 'This layer mark should not be here.%s' % (
                                showPlaceInString(
                                    iemlExpressionString,
                                    zubToken2.first,
                                    zubToken2.last,
                                ),
                            ))
                            sys.exit( 1)
                        else:
                            userError( 'This layer mark %s is out of sequence.  A layer mark at layer %d was expected.%s' % (
                                zubToken2.tokenType,
                                currentLayer,
                                showPlaceInString(
                                    iemlExpressionString,
                                    zubToken2.first,
                                    zubToken2.last,
                                ),
                            ))
                            sys.exit( 1)

                        roleNumber = 1
                        for tmpToken in tmpTokenList:
                            tmpToken.roleNumber = roleNumber
                            roleNumber += 1

                        foundLayerMark = True
                        tmpTokenList.append( zubToken2)
                        newTokenList.append( token(
                            'genOp',
                            tmpTokenList,
                            isSdtAtLayer=currentLayer,
                            comInsts=comInsts,
                            fillWith=fillWith,
                            layerMarkPosition=zubToken2.first,
                        ))
                        tmpTokenList = []
                        comInsts = []
                        zubTokenCounter1 = zubTokenCounter2
                            ## zubTokenCounter1 will be incremented at
                            ## the bottom of the outer loop, as usual,
                            ## as well
                        break

                    elif zubToken2.tokenType in [ 'comment', 'instantiator']:
                        if not subsequentLayerMarkerExists( zubTokenCounter2, thisToken.zubTokenLists[ -1]):
                            userError( 'This %s is not followed by a layer mark.%s' % (
                                zubToken2.tokenType,
                                showPlaceInString(
                                    iemlExpressionString,
                                    zubToken2.first,
                                    zubToken2.last,
                                ),
                            ))
                            sys.exit( 1)
                        comInsts.append( zubToken2)
                    else:
                        tmpTokenList.append( zubToken2)
                    zubTokenCounter2 += 1


                if len( tmpTokenList) > 0:
                    newTokenList.extend( tmpTokenList)

        zubTokenCounter1 += 1
 
    allTokensHaveIsSdtAtLayerAttribute = True
    layerThatTheyAreAllAt = None
    for newToken in newTokenList:
        if not hasattr( newToken, 'isSdtAtLayer'):
            allTokensHaveIsSdtAtLayerAttribute = False
            break
        else:
            if layerThatTheyAreAllAt == None:
                layerThatTheyAreAllAt = newToken.isSdtAtLayer
            else:
                if layerThatTheyAreAllAt != newToken.isSdtAtLayer:
                    userError( 'Unexpected layer change from %d to %d here.%s' % (
                        layerThatTheyAreAllAt,
                        newToken.isSdtAtLayer,
                        showPlaceInString(
                            iemlExpressionString,
                            newToken.first,
                            newToken.last,
                        ),
                    ))
                
    if allTokensHaveIsSdtAtLayerAttribute:
        if hasattr( thisToken, 'isSdtAtLayer'):
            if thisToken.isSdtAtLayer != layerThatTheyAreAllAt:
                errMsg( 'Internal parser error.%s%s' % (
                    showPlaceInString(
                        iemlExpressionString,
                        thisToken.first,
                        thisToken.last,
                    ),
                    printToken( thisToken, 0, 2),
                ))
                sys.exit( 1)
        else:
            thisToken.isSdtAtLayer = layerThatTheyAreAllAt

    if allTokensHaveIsSdtAtLayerAttribute and \
       ( ( thisToken.tokenType not in setOperationTokenTypeNames and len( newTokenList) > 1) or \
         ( thisToken.tokenType     in setOperationTokenTypeNames and len( newTokenList) > 2)):
        userError( 'This has more than one genOp in it.  Have you omitted a layermark?%s' % (
            showPlaceInString(
                iemlExpressionString,
                newTokenList[ 0].first,
                newTokenList[ -1].last,
            ),
        ))
        sys.exit( 1)


    if len( newTokenList) > 0:
        thisToken.zubTokenLists.append( newTokenList)


#######################################################
def subsequentLayerMarkerExists( tokenCounter, tokenList):
    tokenCounter += 1
    while tokenCounter < len( tokenList):
        zubToken3 = tokenList[ tokenCounter]
        if zubToken3.tokenType in layerMarkTokenTypes:
            return True
        tokenCounter += 1
    return False

#######################################################
def parseSetOperations( tokenList, newTokenList, thisParent):
    foundOperandOrOperator = False
    for thisToken in tokenList:
        if thisToken.tokenType in [ 'setOperand', 'setOperator']:
            foundOperandOrOperator = True
            break
    if not foundOperandOrOperator: return

    tokenCounter = 0
    while tokenCounter < len( tokenList):
        thisToken = tokenList[ tokenCounter]
        if tokenCounter % 2 == 0 and thisToken.tokenType == 'setOperator':
            userError( 'Nongenerative set operator %s has no operand on its left side.%s' % (
                thisToken.string,
                showPlaceInString( iemlExpressionString,
                                   thisToken.first,
                                   thisToken.last,
                                 )
            ))
            sys.exit( 1)
        tokenCounter += 1

    if tokenList[ -1].tokenType == 'setOperator':
        userError( 'Nongenerative set operator %s has no operand on its right side.%s' % (
            thisToken.string,
            showPlaceInString( iemlExpressionString,
                               thisToken.first,
                               thisToken.last,
                             )
        ))
        sys.exit( 1)

    tmpTokens = []
    tmpTokens.append( token(
        setOperationMap[ tokenList[ 1].string],
        [ tokenList[ 0], tokenList[ 2]],
    ))

##     now tmpTokens is
##     [
##             firstSetOperationToken
##     ]


## After the next 2 iterations of the while loop below, tmpTokens will be
##     [
##             firstSetOperationToken
##                 subtokens: operand1
##                            operand2
##             secondSetOperationToken
##                 subtokens: firstSetOperationToken
##                            operand3
##             thirdSetOperationToken
##                 subtokens: secondSetOperationToken
##                            operand4
##     ]

    tokenCounter = 3
    while tokenCounter < len( tokenList):
        tmpTokens.append( 
            token(
                setOperationMap[ tokenList[ tokenCounter].string],
                [ tmpTokens[ -1], tokenList[ tokenCounter + 1]],
            )
        )
        tokenCounter += 2
    
    newTokenList.append( tmpTokens[ -1])
    dispatch( newTokenList[ -1])
    
#######################################################
def parseSetOperands( tokenList, newTokenList, thisParent):                    
    foundOperator = False
    for thisToken in tokenList:
        if thisToken.tokenType == 'char' and\
           thisToken.string in [ star_union, star_intersection, star_difference]:
            foundOperator = True
            break
    if not foundOperator: return

    if thisParent.tokenType not in [ 'terminalGroup', 'nonterminalGroup']:
        userError( 'Nongenerative set operations, such as the one specified by this %s operator, must be surrounded by "group" operators ().%s' % (
            thisToken.string,
            showPlaceInString( 
                iemlExpressionString,
                thisToken.first,
                thisToken.last,
            ),
        ))
        sys.exit( 1)
         
    operandTokenList = []
    for thisToken in tokenList:
        if thisToken.tokenType == 'char' and\
           thisToken.string in [ star_union, star_intersection, star_difference]:
            if len( operandTokenList) > 0:
                newTokenList.append( token(
                    'setOperand',
                    operandTokenList,
                ))
                operandTokenList = []
                dispatch( newTokenList[ -1])
            newTokenList.append( token(
                'setOperator',
                [ thisToken],
            ))
            dispatch( newTokenList[ -1])
        else:
            operandTokenList.append( thisToken)
    if len( operandTokenList) > 0:
        newTokenList.append( token(
            'setOperand',
            operandTokenList,
        ))
        dispatch( newTokenList[ -1])


#######################################################
def parseUndeterminedSubsets( tokenList, newTokenList, thisParent):

    inUndeterminedSubset = False
    undeterminedSubsetLevel = 0
    tokenCtr = 0
    while tokenCtr < len( tokenList):

        thisToken = tokenList[ tokenCtr]
        foundUndeterminedSubsetStart = False
        foundUndeterminedSubsetEnd = False
        if thisToken.tokenType == 'char' and thisToken.string == star_undeterminedSubsetOf[ 0]:
            foundUndeterminedSubsetStart = True
            undeterminedSubsetLevel += 1
        elif thisToken.tokenType == 'char' and thisToken.string == star_undeterminedSubsetOf[ 1]:
            foundUndeterminedSubsetEnd = True
            undeterminedSubsetLevel -= 1

        if inUndeterminedSubset:
            if foundUndeterminedSubsetEnd and undeterminedSubsetLevel == 0:
                if len( undeterminedSubsetTokenList) == 0:
                    userError( 'Empty undetermined subset.%s' % ( showPlaceInString(
                        iemlExpressionString,
                        thisToken.first -1,
                        thisToken.first,
                    )))
                    sys.exit( 1)
                inUndeterminedSubset = False
                if undeterminedSubsetHasUndeterminedSubsetStartsInIt:
                    tokenType = 'nonterminalUndeterminedSubset'
                else:
                    tokenType = 'terminalUndeterminedSubset'
                    
                newTokenList.append( token(
                    tokenType,
                    undeterminedSubsetTokenList,
                ))
                
                ## now fix the numbers so that the numbers become the value of
                ## an extra attribute (parameterIdentifier) of the token just created.
                
                tokenJustAdded = newTokenList[ -1]
                listOfSubTokens = tokenJustAdded.zubTokenLists[ -1]
                tokenCtr2 = len( listOfSubTokens) - 1
                numberString = ''
                while tokenCtr2 >= 0:
                    if listOfSubTokens[ tokenCtr2].tokenType != 'char':
                        break
                    if listOfSubTokens[ tokenCtr2].string not in string.digits:
                        break
                    numberString = '%s%s' % ( listOfSubTokens[ tokenCtr2].string, numberString)
                    del listOfSubTokens[ tokenCtr2]
                    tokenCtr2 -= 1
                    
                if len( numberString) == 0:
                    numberString = 'noIdNumber'
                setattr( newTokenList[ -1], 'parameterIdentifier', numberString)

                dispatch( newTokenList[ -1])

                tokenCtr += 1
                continue

        else:  ## we are not in an undeterminedSubset
            if foundUndeterminedSubsetEnd:
                userError( 'Undetermined Subset end %s found with no preceding undetermined subset start %s.%s' % (
                    star_undeterminedSubsetOf[ 1],
                    star_undeterminedSubsetOf[ 0],
                    showPlaceInString(
                        iemlExpressionString,
                        thisToken.first,
                        thisToken.first,
                    ),
                ))
                sys.exit( 1)
            elif foundUndeterminedSubsetStart and undeterminedSubsetLevel == 1:
                inUndeterminedSubset = True
                undeterminedSubsetTokenList = []
                           ## we don't do newTokenList.append( thisToken) because
                           ## we don't want the '(' to be saved in the undeterminedSubset string
                undeterminedSubsetHasUndeterminedSubsetStartsInIt = False

                tokenCtr += 1
                continue

        if not inUndeterminedSubset:
            newTokenList.append( thisToken)
        else:  ## we're in a undeterminedSubset
            if thisToken.tokenType == 'char' and thisToken.string == star_undeterminedSubsetOf[ 0]:
                undeterminedSubsetHasUndeterminedSubsetStartsInIt = True
            undeterminedSubsetTokenList.append( thisToken)

        tokenCtr += 1

    if inUndeterminedSubset:
        userError( 'Undetermined subset not closed.%s' % ( showPlaceInString(
            iemlExpressionString,
            undeterminedSubsetTokenList[ 0].first,
            thisToken.last,
        )))
        sys.exit( 1)
    return newTokenList


#######################################################
def parseDiagonals( tokenList, newTokenList, thisParent):

    inDiagonal = False
    diagonalLevel = 0
    tokenCtr = 0
    while tokenCtr < len( tokenList):

        thisToken = tokenList[ tokenCtr]

        foundDiagonalStart = False
        foundDiagonalEnd = False
        if thisToken.tokenType == 'char' and thisToken.string == star_diagonal[ 0]:
            foundDiagonalStart = True
            diagonalLevel += 1
        elif thisToken.tokenType == 'char' and thisToken.string == star_diagonal[ 1]:
            foundDiagonalEnd = True
            diagonalLevel -= 1

        if inDiagonal:
            if foundDiagonalEnd and diagonalLevel == 0:
                if len( diagonalTokenList) == 0:
                    userError( 'Empty diagonal.%s' % ( showPlaceInString(
                        iemlExpressionString,
                        thisToken.first -1,
                        thisToken.first,
                    )))
                    sys.exit( 1)
                inDiagonal = False
                if diagonalHasDiagonalStartsInIt:
                    tokenType = 'nonterminalDiagonal'
                else:
                    tokenType = 'terminalDiagonal'
                    
                newTokenList.append( token(
                    tokenType,
                    diagonalTokenList,
                ))
                ## now fix the numbers so that the numbers become the value of
                ## an extra attribute of the token just created.
                
                tokenJustAdded = newTokenList[ -1]
                listOfSubTokens = tokenJustAdded.zubTokenLists[ -1]
                tokenCtr2 = len( listOfSubTokens) - 1
                numberString = ''
                while tokenCtr2 >= 0:
                    if listOfSubTokens[ tokenCtr2].tokenType != 'char':
                        break
                    if listOfSubTokens[ tokenCtr2].string not in string.digits:
                        break
                    numberString = '%s%s' % ( listOfSubTokens[ tokenCtr2].string, numberString)
                    del listOfSubTokens[ tokenCtr2]
                    tokenCtr2 -= 1
                    
                if len( numberString) == 0:
                    numberString = 'noIdNumber'
                setattr( newTokenList[ -1], 'parameterIdentifier', numberString)

                dispatch( newTokenList[ -1])

                tokenCtr += 1
                continue

        else:  ## we are not in a diagonal
            if foundDiagonalEnd:
                userError( 'Diagonal end %s found with no preceding diagonal start %s.%s' % (
                    star_diagonal[ 1],
                    star_diagonal[ 0],
                    showPlaceInString(
                        iemlExpressionString,
                        thisToken.first,
                        thisToken.first,
                    ),
                ))
                sys.exit( 1)
            elif foundDiagonalStart and diagonalLevel == 1:
                inDiagonal = True
                diagonalTokenList = []
                           ## we don't do newTokenList.append( thisToken) because
                           ## we don't want the '(' to be saved in the diagonal string
                diagonalHasDiagonalStartsInIt = False

                tokenCtr += 1
                continue

        if not inDiagonal:
            newTokenList.append( thisToken)
        else:  ## we're in a diagonal
            if thisToken.tokenType == 'char' and thisToken.string == star_diagonal[ 0]:
                diagonalHasDiagonalStartsInIt = True
            diagonalTokenList.append( thisToken)

        tokenCtr += 1

    if inDiagonal:
        userError( 'Diagonal not closed.%s' % ( showPlaceInString(
            iemlExpressionString,
            diagonalTokenList[ 0].first,
            thisToken.last,
        )))
        sys.exit( 1)
    return newTokenList


#######################################################
def parseGroups( tokenList, newTokenList, thisParent):

    inGroup = False
    groupLevel = 0
    tokenCtr = 0
    while tokenCtr < len( tokenList):

        thisToken = tokenList[ tokenCtr]

        foundGroupStart = False
        foundGroupEnd = False
        if thisToken.tokenType == 'char' and thisToken.string == star_group[ 0]:
            foundGroupStart = True
            groupLevel += 1
        elif thisToken.tokenType == 'char' and thisToken.string == star_group[ 1]:
            foundGroupEnd = True
            groupLevel -= 1

        if inGroup:
            if foundGroupEnd and groupLevel == 0:
                if len( groupTokenList) == 0:
                    userError( 'Empty group.%s' % ( showPlaceInString(
                        iemlExpressionString,
                        thisToken.first -1,
                        thisToken.first,
                    )))
                    sys.exit( 1)
                inGroup = False
                if groupHasGroupStartsInIt:
                    tokenType = 'nonterminalGroup'
                else:
                    tokenType = 'terminalGroup'
                    
                newTokenList.append( token(
                    tokenType,
                    groupTokenList,
                ))


                dispatch( newTokenList[ -1])

                tokenCtr += 1
                continue

        else:  ## we are not in a group
            if foundGroupEnd:
                userError( 'Group end %s found with no preceding group start %s.%s' % (
                    star_group[ 1],
                    star_group[ 0],
                    showPlaceInString(
                        iemlExpressionString,
                        thisToken.first,
                        thisToken.first,
                    ),
                ))
                sys.exit( 1)
            elif foundGroupStart and groupLevel == 1:
                inGroup = True
                groupTokenList = []
                           ## we don't do newTokenList.append( thisToken) because
                           ## we don't want the '(' to be saved in the group string
                groupHasGroupStartsInIt = False

                tokenCtr += 1
                continue

        if not inGroup:
            newTokenList.append( thisToken)
        else:  ## we're in a group
            if thisToken.tokenType == 'char' and thisToken.string == star_group[ 0]:
                groupHasGroupStartsInIt = True
            groupTokenList.append( thisToken)

        tokenCtr += 1

    if inGroup:
        userError( 'Group not closed.%s' % ( showPlaceInString(
            iemlExpressionString,
            groupTokenList[ 0].first,
            thisToken.last,
        )))
        sys.exit( 1)
    return newTokenList


#######################################################
def parseInstantiators( tokenList, newTokenList, thisParent):

    inInstantiator = False

    tokenCtr = 0
    while tokenCtr < len( tokenList):

        thisToken = tokenList[ tokenCtr]

        foundInstantiatorStart = False
        foundInstantiatorEnd = False
        if thisToken.tokenType == 'char' and thisToken.string == star_instantiator[ 0]:
            foundInstantiatorStart = True
        if thisToken.tokenType == 'char' and thisToken.string == star_instantiator[ 1]:
            foundInstantiatorEnd = True

        if inInstantiator:
            if foundInstantiatorEnd:
                inInstantiator = False
                instantiatorTokenList.extend( [ thisToken])
                newTokenList.append( token(
                    'instantiator',
                    instantiatorTokenList,
                ))
                dispatch( newTokenList[ -1])
                tokenCtr += 1
                
                tokenCtr2 = tokenCtr
                while tokenCtr2 < len( tokenList):
                    thisToken2 = tokenList[ tokenCtr2]
                    if thisToken2.string in layerNameMap or \
                           thisToken2.string == star_instantiator[ 0] or \
                           thisToken2.string == star_comment[ 0][ 0]:
                        break

                    elif thisToken2.tokenType in [ 'comment', 'instantiator']:
                        pass

                    elif thisToken2.string in star_whitespace:
                        pass
                    else:
                        userError( 'Instantiator not followed by a layer mark.%s' % (
                            showPlaceInString(
                                iemlExpressionString,
                                instantiatorTokenList[ 0].first,
                                instantiatorTokenList[ -1].last,
                            ),
                        ))
                        sys.exit( 1)
                    tokenCtr2 += 1
                continue
        else:
            if foundInstantiatorEnd:
                userError( 'Instantiator end %s found with no preceding instantiator start %s.%s' % (
                    star_instantiator[ 1],
                    star_instantiator[ 0],
                    showPlaceInString(
                        iemlExpressionString,
                        thisToken.first,
                        thisToken.first
                    ),
                ))
                sys.exit( 1)
            elif foundInstantiatorStart:
                inInstantiator = True
                instantiatorTokenList = [ thisToken ]
                           ## we want the ']' to be saved in the instantiator string
                tokenCtr += 1
                continue

        if not inInstantiator:
            newTokenList.append( thisToken)
        else:  ## we're in a instantiator
            instantiatorTokenList.append( thisToken)

        tokenCtr += 1



    if inInstantiator:
        userError( 'Instantiator not closed.%s' % ( showPlaceInString(
            iemlExpressionString,
            instantiatorTokenList[ 0].first,
            thisToken.last,
        )))
        sys.exit( 1)
    return newTokenList


#######################################################
def parseIemlSymbols( tokenList, newTokenList, thisParent):
    global iemlSymbolTokenList

    tokenCtr = 0
    while tokenCtr < len( tokenList):

        thisToken = tokenList[ tokenCtr]

        if thisToken.tokenType != 'char':
            newTokenList.append( thisToken)
            tokenCtr += 1
            continue

        elif thisToken.tokenType == 'char' and thisToken.string[ 0] in [
                star_wo[ 0], 
                star_wa[ 0], 
                star_wu[ 0], 
                star_we[ 0], 
            ]:
            try:
                nextToken = tokenList[ tokenCtr + 1]
            except IndexError:
                userError( '%s is not followed by one of %s , %s , %s , or %s.%s' % (
                    thisToken.string,
                    star_wo[ 1], 
                    star_wa[ 1], 
                    star_wu[ 1], 
                    star_we[ 1], 
                    showPlaceInString( iemlExpressionString,
                                       thisToken.first,
                                       thisToken.last,
                                     ),
                ))
                sys.exit( 1)
            if nextToken.tokenType == 'char' and nextToken.string[ 0] in [
                    star_wo[ 1], 
                    star_wa[ 1], 
                    star_wu[ 1], 
                    star_we[ 1], 
                ]:

                # create new symbol token here
                newTokenList.append( token(
                    wMap[ nextToken.string[ 0]],
                    [ thisToken, nextToken],
                    inherentLayer=2,
                ))
                iemlSymbolTokenList.append( newTokenList[ -1])
                dispatch( newTokenList[ -1])
                tokenCtr += 2
                continue
            else:
                userError( '%s is not followed by one of %s , %s , %s , or %s.%s' % (
                    thisToken.string,
                    star_wo[ 1], 
                    star_wa[ 1], 
                    star_wu[ 1], 
                    star_we[ 1], 
                    showPlaceInString( iemlExpressionString,
                                       thisToken.first,
                                       thisToken.first,
                                     ),
                ))
                sys.exit( 1)
        elif thisToken.tokenType == 'char' and thisToken.string in star_eventSymbols:
            newTokenList.append( token(
                singleCharacterMap[ thisToken.string],
                [ thisToken],
                inherentLayer=2,
            ))
            iemlSymbolTokenList.append( newTokenList[ -1])
            dispatch( newTokenList[ -1])
            tokenCtr += 1
            continue
        elif thisToken.tokenType == 'char' and thisToken.string in star_primitiveSymbols:
            newTokenList.append( token(
                singleCharacterMap[ thisToken.string],
                [ thisToken],
                inherentLayer=1,
            ))
            iemlSymbolTokenList.append( newTokenList[ -1])
            dispatch( newTokenList[ -1])
            tokenCtr += 1
            continue
        elif thisToken.tokenType == 'char' and thisToken.string in star_whitespace:
##  All remaining whitespace just gets eaten here.  It's for the best.
##  The comments and instantiators have already been preserved, including their
##  whitespace.            
##             newTokenList.append( token(   ## just suppress these altogether
##                 'whitespace',
##                 [ thisToken],
##             ))
##             dispatch( newTokenList[ -1])
            tokenCtr += 1
            continue
        elif thisToken.tokenType == 'char' and thisToken.string in layerNameMap:
            newTokenList.append( token(
                layerNameMap[ thisToken.string],
                [ thisToken],
            ))
            dispatch( newTokenList[ -1])
            tokenCtr += 1
            continue
        elif thisToken.tokenType == 'char' and thisToken.string in star_group:
            newTokenList.append( thisToken)
            tokenCtr += 1
            continue
        elif thisToken.tokenType == 'char' and thisToken.string in star_diagonal:
            newTokenList.append( thisToken)
            tokenCtr += 1
            continue
        elif thisToken.tokenType == 'char' and thisToken.string in star_undeterminedSubsetOf:
            newTokenList.append( thisToken)
            tokenCtr += 1
            continue
        elif thisToken.tokenType == 'char' and thisToken.string in setOperationMap:
            newTokenList.append( thisToken)
            tokenCtr += 1
            continue
        elif thisToken.tokenType == 'char' and thisToken.string in string.digits:
            newTokenList.append( thisToken)
            tokenCtr += 1
            continue
        else:  ## this must be a 'char' and we haven't recognized it.
            userError( 'Symbol %s is not recognized.%s' % (
                thisToken.string,
                showPlaceInString( iemlExpressionString,
                                   thisToken.first,
                                   thisToken.first,
                                 ),
            ))
            sys.exit( 1)
    return newTokenList


#######################################################
def parseComments( tokenList, newTokenList, thisParent):
    inComment = False

    tokenCtr = 0
    while tokenCtr < len( tokenList):

        thisToken = tokenList[ tokenCtr]


        foundCommentStart = foundCommentEnd = False

        if thisToken.string == star_comment[ 0][ 0]:
            try:
                if tokenList[ tokenCtr + 1].string == star_comment[ 0][ 1]:
                    foundCommentStart = True
            except IndexError:  ## this '/' might be the last token of the tokenList.
                pass

        if thisToken.string == star_comment[ 1][ 0]:
            try:
                if tokenList[ tokenCtr + 1].string == star_comment[ 1][ 1]:
                    foundCommentEnd = True
            except IndexError:  ## this '$' might be the last token of the tokenList.
                pass

        if inComment:
            if foundCommentEnd:
                inComment = False
                commentTokenList.extend( [ thisToken, tokenList[ tokenCtr + 1]])
                           ## we want the '/' and the '$' to be saved in the comment string
                ## do something about the commentTokenList here
                newTokenList.append( token(
                    'comment',
                    commentTokenList,
                ))
                dispatch( newTokenList[ -1])
                tokenCtr += 2

                tokenCtr2 = tokenCtr
                while tokenCtr2 < len( tokenList):
                    thisToken2 = tokenList[ tokenCtr2]
                    if thisToken2.string in layerNameMap or \
                           thisToken2.string == star_instantiator[ 0] or \
                           thisToken2.string == star_comment[ 0][ 0]:
                        break
                    elif thisToken2.string in star_whitespace:
                        pass
                    else:
                        userError( 'Comment not followed by a layer mark.%s' % (
                            showPlaceInString(
                                iemlExpressionString,
                                commentTokenList[ 0].first,
                                commentTokenList[ -1].last,
                            ),
                        ))
                        sys.exit( 1)
                    tokenCtr2 += 1


                continue
        else:  ## not in a comment
            if foundCommentEnd:
                userError( 'Comment end %s found with no preceding comment start %s.%s' % (
                    star_comment[ 1],
                    star_comment[ 0],
                    showPlaceInString(
                        iemlExpressionString,
                        thisToken.first,
                        thisToken.first + 1,
                    ),
                ))
                sys.exit( 1)
            elif foundCommentStart:
                inComment = True
                commentTokenList = [ thisToken, tokenList[ tokenCtr + 1]]
                           ## we want the '/' and the '$' to be saved in the comment string
                tokenCtr += 2
                continue

        if not inComment:
            newTokenList.append( thisToken)
        else:  ## we're in a comment
            commentTokenList.append( thisToken)

        tokenCtr += 1

    if inComment:
        userError( 'Comment not closed.%s' % ( showPlaceInString(
            iemlExpressionString,
            commentTokenList[ 0].first,
            thisToken.last,
        )))
        sys.exit( 1)
    return newTokenList


#######################################################
def userError( msg):

    if not msg.endswith( '\n'):
        msg = '%s\n' % ( msg)
    msg = 'ERROR: %s' % ( msg)
    sys.stdout.write( msg)
    sys.exit( 1)

#######################################################
def makeListOfCharTokensFromString( s, tokenList, **kwargs):

    if kwargs.has_key( 'startPosition'):
        charPosition = kwargs[ 'startPosition'] - 1
    else:
        charPosition = -1
    for c in s:
        charPosition += 1
        tokenList.append( token(
            'char',
            [],
            c,
            first=charPosition,
            last=charPosition,
            dontShow=True,
        ))
    return tokenList

#######################################################
dispatchDict = {
## This dict is global.
## Keys are token type names.  Values are the functions that
## handle those token types.
               'iemlExpression': [
                                     parseComments,
                                     parseInstantiators,
                                     parseIemlSymbols,
                                     parseGroups,
                                     parseUndeterminedSubsets,
                                     parseDiagonals,
                                     parseSetOperands,
                                     parseSetOperations,
                                 ],
             'nonterminalGroup': [
                                     parseGroups,
                                     parseUndeterminedSubsets,
                                     parseDiagonals,
                                     parseSetOperands,
                                     parseSetOperations,
                                 ],
                'terminalGroup': [
                                     parseUndeterminedSubsets,
                                     parseDiagonals,
                                     parseSetOperands,
                                     parseSetOperations,
                                 ],
'nonterminalUndeterminedSubset': [
                                     parseUndeterminedSubsets,
                                     parseDiagonals,
                                     parseSetOperands,
                                     parseSetOperations,
                                 ],
   'terminalUndeterminedSubset': [
                                     parseDiagonals,
                                     parseSetOperands,
                                     parseSetOperations,
                                 ],
          'nonterminalDiagonal': [
                                     parseDiagonals,
                                     parseSetOperands,
                                     parseSetOperations,
                                 ],
             'terminalDiagonal': [
                                     parseSetOperands,
                                     parseSetOperations,
                                 ],
                   'setOperand': [
                                 ],
                  'setOperator': [
                                 ],
                        'union': [
                                 ],
                   'difference': [
                                 ],
                 'intersection': [
                                 ],

                        'genOp': [
                                 ],

                       'star_I': [
                                 ],
                       'star_F': [
                                 ],
                       'star_E': [
                                 ],
                       'star_M': [
                                 ],
                       'star_O': [
                                 ],
                       'star_U': [
                                 ],
                       'star_A': [
                                 ],
                       'star_S': [
                                 ],
                       'star_B': [
                                 ],
                       'star_T': [
                                 ],

                      'star_wo': [
                                 ],
                      'star_wa': [
                                 ],
                      'star_wu': [
                                 ],
                      'star_we': [
                                 ],
                       'star_y': [
                                 ],
                       'star_o': [
                                 ],
                       'star_e': [
                                 ],
                       'star_u': [
                                 ],
                       'star_a': [
                                 ],
                       'star_i': [
                                 ],
                       'star_j': [
                                 ],
                       'star_g': [
                                 ],
                       'star_h': [
                                 ],
                       'star_c': [
                                 ],
                       'star_p': [
                                 ],
                       'star_x': [
                                 ],
                       'star_s': [
                                 ],
                       'star_b': [
                                 ],
                       'star_t': [
                                 ],
                       'star_k': [
                                 ],
                       'star_m': [
                                 ],
                       'star_n': [
                                 ],
                       'star_d': [
                                 ],
                       'star_f': [
                                 ],
                       'star_l': [
                                 ],

      'star_primitiveLayerMark': [
                                 ],
          'star_eventLayerMark': [
                                 ],
       'star_relationLayerMark': [
                                 ],
           'star_ideaLayerMark': [
                                 ],
         'star_phraseLayerMark': [
                                 ],
           'star_semeLayerMark': [
                                 ],

          'star_tildeLayerMark': [
                                 ],

           'star_bangLayerMark': [
                                 ],

                      'comment': [
                                 ],
                 'instantiator': [
                                 ],
                   'whitespace': [
                                 ],
}


#######################################################
if __name__ == '__main__':
    main()
