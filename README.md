# mobilequiz

The MobileQuiz is an ILIAS Plug-In for audience response or feedback scenarios within the classroom.
It can be added as an ILIAS Repository Object, so that lecturers can create Questions, which can then be answered by the students.


## Features

* Four Question Types
    * Single Choice Questions
    * Multiple Choice Questions
    * Numeric Slider Questions
    * Text Questions with Feedback on right or wrong answer
* Full web application, no installation by students or lecturers required.
* No authentication required by students.
* Quiz round can be activ, inactiv and "direct feedback".
* Quizzes in direct feedback can be performed several times and give a immidiate feedback on the given answers.
* Dynamic live charts with green, red and blue bars.
* Excel Export of Results.
* QR-Code view for faster access.
* Option to use a URL-Shortener for better readability or URL and QR-Code.
* Markdown and LaTeX support for questions.
* Quizzes can be copied to other courses.
* Multi-Client Support


## Installation

* Copy MobileQuiz folder to `../ilias/Customizing/global/plugins/Services/Repository/RepositoryObject/MobileQuiz`
* URL-Shortener and LaTeX Support are activated via Settings of the Plugin in the Plugin menu of the administrator.
* If included as symlink, connection of the Frontend (Student-View) to the database may not work. Then the ILIAS-Root directory has to be configured manually at `../MobileQuiz/interfaces/ilDBConnector.php´ line 15. (iliasRoot)


## User Documentation

### Lecturer

* Lecturers can create a new MobileQuiz object like other repository objects via add object.
* A quiz can be build of several questions, which further can have several answer choices.
* Whenever a quiz is started, a new round is created. Every quiz can have multiple rounds.
* Each round can be activ, closed or "direct feedback" 
* Several rounds of the same quiz can be active at once.

#### Current Quiz Round

* Start Round starts a new Quiz round, and creates a new QR-Code
* Stop Round stops that round.
* The QR-Code can be enlarged for better readability.
* The page only shows the QR-Code of the newest round of that quiz.

#### Questions & Answers

* New Questions and answer choices can be created with "Add".
* Questions and choices can be edited by clicking on the Question.
* Questions can be deleted and moved up or down.

#### Rounds and Results

* The QR-Code of old rounds can be shown again.
* Rounds can be set to "activ", "closed" and "direct feedback"
* The result charts can be viewed by clicking on the round.
* "Export Result" exports all results of all rounds in an Excel-file with seperate tables.
* Individual rounds can be deleted as well.


### Student

* Students access Quiz rounds via the QR-Code, the URL, or directly via the cource in ILIAS.
* When accessing via QR-Code or URL, no authentication is required. 
* Usual rounds can only be submitted once.
* Quiz rounds in "direct feedback" can be performed several times


## Used Technologies

* MathJax is used for LaTeX Transformation
* jQuery and iQueryMobile for student fronted
* Markdown.php for PHP-Markdown translation
* simpleMDE as Javascript-markdown editor
* phpQrCode for QR-Code generation
* chart.js for charts in the Result view
