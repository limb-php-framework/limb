# Step 3. Adding forms to allow news creating and editing. Data validation. News removal
## Adding news
### news/create.html template
Let's add new template create.html into crud/template/news/ folder with the following contents:

    <html>
    <head>
      <title>Limb3 tutorial</title>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>
    <body bgcolor="#FFFFFF" text="#000000" >
 
    <h1>Create news</h1>
 
    <form id='news_form' name='news_form' method='post' runat='server'>
 
    <label for="title">Title</label> : 
    <input name='title' type='text' size='60' title='Title'/><br/>
 
    <label for="date">Date</label> : 
    <input name='date' type='text' size='15' title='Date'/><br/>
 
    <label for="annotation">Annotation</label> : 
    <textarea name='annotation' rows='2' cols='40' title='Annotation'></textarea><br/>
 
    <label for="content">Content</label> : 
    <textarea name='content' rows='5' cols='40'></textarea><br/>
 
    <input type='submit' value='Create' runat='client'/>
 
    </form>
 
    </body>
    </html>

It looks like a regular html-page but there are some small gotchas you should be aware of:

* `<form>` tag has **runat='server'** attribute, that tells template engine that it's not a regular html-tag but a tag with so called *runtime component* (or active component) attached. Runtime component is an object that is created while template is executed and that has some specific behavior. Tags with runtime components usually have some unique identifier specified by **id** attribute.
* Please note that form elements have identifiers matching the names of the fields of table «news»

### Changes in NewsController class
Now we will expand our controller NewsController so that it can receive data from the form «news_form» and add news entries into the db table:

    <?php
    lmb_require('limb/web_app/src/controller/lmbController.class.php');
    lmb_require('src/model/News.class.php');
 
    class NewsController extends lmbController
    {
      function doCreate()
      {
        if(!$this->request->hasPost())
          return;
 
        $news = new News();
        $news->import($this->request);
 
        $news->save();
 
        $this->redirect();
      }
    }

We added doCreate() method that will be called when our application receives requests like http://%tutorial_address%/news/create. As you may have already guessed template crud/template/news/create.html will be picked and rendered automatically(similar to default «display» action).

Let's examine the body of doCreate() method line by line:

    if(!$this->request->hasPost())
      return;

lmbController has request and response objects as its attributes for convenience. In the lines above we check if our application received POST request. If there is no POST request it means that the page is requested for the first time and our application should just show the create form.

    $news = new News();
    $news->import($this->request);

Here we initialize an empty News object and fill it with data from the request. News automatically imports only those fields from the request that present in the table «news» (slightly simplified description, but it's ok for now). That's why we have form fields with identifiers matching fields of the «news» table.

    $news->save();

Here we save new record in database and …

    $this->redirect();

make redirection to the starting page of the current controller(that is /news).

Let's type http://%tutorial_address%/news/create in browser and try to add a couple of news. You should see something like this:

![Alt-simple_create](http://wiki.limb-project.com/2011.1/lib/exe/fetch.php?cache=&media=limb3:ru:tutorials:basic:simple_create.png)

## Adding data validation
Our first implementation of adding news doesn't check any fields. But in the real world applications there are usually some validation rules that have to be applied to the raw data. Let's try to implement data validation with Limb3.

Ok, suppose we'd like to apply the following rules for our news:

* title, date and annotation are required fields
* title must be less than 75 characters long
* content field is optional

If user provides error input while filling create form then after submitting the form she should see the same form with all data entered into form fields as well as validation errors. To do so we will need to make some changes in News and NewsController classes and expand news/create.html template.

### Extending News class
News version of News class will looks like this:

    <?php
    lmb_require('limb/active_record/src/lmbActiveRecord.class.php');
 
    class News extends lmbActiveRecord
    {
      protected function _createValidator()
      {
        $validator = new lmbValidator();
 
        $validator->addRequiredRule('title');
        $validator->addRequiredRule('annotation');
        $validator->addRequiredRule('date');
 
        lmb_require('limb/validation/src/rule/lmbSizeRangeRule.class.php');
        $validator->addRule(new lmbSizeRangeRule('title', 75));
        return $validator;
      }
    }
    ?>

lmbActiveRecord class performs self-validation using a validator object while saving data. Validator is created with _createValidator() method. By default _createValidator() returns an empty validator but in real applications we usually override this method.

Validator — is an object of **lmbValidator** class (can be found in [VALIDATION package](../../../../validation/docs/en/validation.md)) or any lmbValidator child class. Validator consists of a set of validation rules responsible for actual data sanity checks.

Please note that the line

    $validator->addRequiredRule('title');

can be rewritten as

    lmb_require('limb/validation/src/rule/lmbRequiredRule.class.php');
    $validator->addRule(new lmbRequiredRule('title'));

As a result we used two validation rules here:

* **lmbRequiredRule** — forces field to be present in validated data container (such as lmbActiveRecord object).
* **lmbSizeRangeRule** — checks whether field has minimum and (or) maximum length

### Changes in NewsController
NewsController :: doCreate() method requires some modifications too:

    <?php
    lmb_require('limb/web_app/src/controller/lmbController.class.php');
    lmb_require('src/model/News.class.php');
 
    class NewsController extends lmbController
    {
      function doCreate()
      {
        if(!$this->request->hasPost())
          return;
 
        $news = new News();
        $news->import($this->request);
 
        $this->useForm('news_form');
        $this->setFormDatasource($news);
 
        if($news->trySave($this->error_list))
          $this->redirect();
      }
    }
    ?>

The new lines are:

    $this->useForm('news_form');
    $this->setFormDatasource($news);
 
    if($news->trySave($this->error_list))
      $this->redirect();

With the first two line we wire «news_form» runtime component of template with $news object. This allows us not to lose data in the form fields after form submission if validation fails.

lmbActiveRecord :: **trySave($error_list)** method returns true if data is valid or false in case of any validation errors. trySave() is just a wrapper for save() method. lmbActiveRecord :: save() in case of any validation errors throws an exception of lmbValidationException class which is caught by trySave().

### Displaying validation errors by news/create.html template
Now let's add some code to news/create.html template to display validation errors:

    [...]
    <h1>Create news</h1>
 
    <form id='news_form' name='news_form' method='post' runat='server'>
 
    <form:errors target='errors'/>
 
    <list:list id='errors'>
    <table>
    <list:ITEM>
        <tr valign="top">
          <td width="20%">&nbsp;</td>
          <td width="80%"><FONT COLOR="RED">{$message}</FONT></td>
        </tr>
    </list:ITEM>
    </table>
    </list:list>
 
    <label for="title">Title</label> : <input name='title' type='text' size='60' title='Title'/><br/>
    [...]

`<form:errors>` tag passes list of validation errors to tag marked with **target** attribute. Validation error messages will be rendered by `<list:list>` and `<list:item>` tags which we described before.

Validation errors are passed to the template automatically since we wired object of News class with form runtime component in NewsController :: doCreate() method.

Now try entering incorrect data and test our validation sub-system:

![Alt-simple_create_errors](http://wiki.limb-project.com/2011.1/lib/exe/fetch.php?cache=&media=limb3:ru:tutorials:basic:simple_create_errors.png)

If you see something similar to above, then you are doing right.

## Editing news
### news/edit.html template
Template for editing news crud/template/news/edit.html looks almost the same as the one for creating news:

    <html>
    <head>
      <title>Limb3 tutorial</title>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>
    <body bgcolor="#FFFFFF" text="#000000" >
 
    <h1>Edit news</h1>
 
    <form id='news_form' name='news_form' method='post' runat='server'>
 
    <form:errors target='errors'/>
 
    <list:list id='errors'>
    <table>
    <list:ITEM>
        <tr valign="top">
          <td width="20%">&nbsp;</td>
          <td width="80%"><FONT COLOR="RED">{$message}</FONT></td>
        </tr>
    </list:ITEM>
    </table>
    </list:list>
 
    <label for="title">Title</label> : <input name='title' type='text' size='60' title='Title'/><br/>
 
    <label for="date">Date</label> : <input name='date' type='text' size='15' title='Date'/><br/>
 
    <label for="annotation">Annotation</label> : <textarea name='annotation' rows='2' cols='40' title='Annotation'></textarea><br/>
 
    <label for="content">Content</label> : <textarea name='content' rows='5' cols='40'></textarea><br/>
 
    <input type='submit' value='Edit' runat='client'/>
 
    </form>
 
    </body>
    </html>

There is a large duplication between news/create.html and news/edit.html which can be easily removed. We will optimize these templates later.

### NewsController :: doEdit() method
Let's add doEdit() method to the NewsController so it can handle «edit» action:

    <?php
      [...]
    class NewsController extends lmbController
    {
      [...]
      function doEdit()
      {
        $news = new News((int)$this->request->get('id'));
 
        $this->useForm('news_form');
        $this->setFormDatasource($news);
 
        if(!$this->request->hasPost())
          return;
 
        $news->import($this->request);
 
        if($news->trySave($this->error_list))
          $this->redirect();
      } 
    ?>

With the following line:

    $news = new News((int)$this->request->get('id'));

a particular News object is loaded from the database using identifier retrieved from the request.

Note that we tied form runtime component with $news object before $this→request→hasPost() check since we need to pass initial data to the template in any case. Thus form fields initially will be filled with data from $news object.

### Modifying news/display.html
It's time to change news/display.html in order to display links to edit action page for each news. Let's make it an extra column in newsline table:

    <html>
    <head>
      <title>Newsline</title>
      <meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
    </head>
    <body>
    <h1>Newsline.</h1>
 
    <route_url params="action:create">Create news</route_url>
    <p/>
 
    <active_record:fetch using='News' target="news"/>
 
    <list:LIST id="news">
      <table border="1">
      <tr>
        <th>ID</th>
        <th>Date</th>
        <th>Title</th>
        <th>Actions</th>
      </tr>
      <list:ITEM>
      <tr>
        <td>{$id}</td>
        <td>{$date}</td>
        <td>{$title}</td>
        <td>
          <route_url params="action:edit,id:{$id}">Edit</route_url>&nbsp;&nbsp;
          <route_url params="action:delete,id:{$id}">Delete</route_url>
        </td>
      </tr>
      <tr>
        <td colspan='4'>
          {$annotation}
        </td>
      </tr>
      </list:ITEM>
      </table>
    </list:LIST>
 
    </body>
    </html>

Here we used `<route_url>` tag, which is essentially similar to `<a>` tag, but the former makes up the href attribute based on the parameters from **params** attribute. In fact, `<route_url>` does request dispatching in reverse order i.e. it forms something like /news/edit/5 from params ('action' ⇒ 'edit', 'id' ⇒ 5). All stuff about request dispatching and so called «routes» is beyond the scope of this tutorial, but if you are interested what rules are used to parse request string you may have a look at limb/web_app/settings/routes.conf.php file (or crud/settings/routes.conf.php depending on how you started this tutorial).

Let's explain how `<route_url>` tag works:

    <route_url params="action:create">Create news</route_url>

This line will output something like `<a href=»/news/create»>Create news</a>`. News controller will be used by default since we are on the news controller page. The same happens for edit and delete links actions of news controller. Parameters in «params» attribute of `<route_url>` tag are separated with commas. Accordingly `<route_url params=«action:edit,id:{$id}»>Edit</route_url>` will generate `<a href=»/news/edit/4»>Edit</a>`

If you did everything right, you would be able to create and edit news (delete action is not implemented yet).

## News removal
To remove objects from database lmbActiveRecord :: destroy() method should be used. Let's add NewsController :: doDelete() method to perform news removal:

    <?php
      [...]
    class NewsController extends lmbController
    {
      [...]
      function doDelete()
      {
        $news = new News((int)$this->request->get('id'));
        $news->destroy();
        $this->redirect();
      }
    }
    ?>

Obviously there is no need to comment this code.

## What's next?
[Step 4. Templates optimization. Adding news pagination](./step4.md)
