 # WP Scripts

A collection of utility scripts for WordPress, meant to be run over http or https.

The goal is to provide a small set of tools for performing diagnostics or common devops tasks in an environment where you do not have access to the command line or such tools as `wp cli`.

## Getting started

Place the `wp-scripts` repository in the root directory of your WordPress install.

You can access the script you want to run in your browser. For example:

    http://mysite.com/wp-scripts/list_active_plugins.php?blog_id=10

## How it works

### The WPScriptCmd class

All of your scripts/commands should extend the `WPScriptCmd` class.

This class does several important things:

- Loads core WordPress
- Requires you to be authenticated, returns `403 Forbidden` if you are not
- When something goes wrong:
    - Sets the proper HTTP header (e.g., `500 Internal Server Error`)
    - Returns output with the message from any `Exception` thrown during execution

### An example command

#### Extending WPScriptCmd

Let's say you have the ID for a blog and want to verify its name. We can create a simple command to do so.

Let's add a file in the `wp-scripts/inc/` directory called `class-cmd-get-blog-name.php`. It's contents should read:

    <?php

    include_once __DIR__ . '/class-cmd.php';

    class GetBlogNameCmd extends WPScriptCmd {

        function __construct($attributes) {
            parent::__construct(null, $attributes);
        }

        function main() {
            switch_to_blog($this->blog_id);
            $ret = get_bloginfo('name');
            restore_current_blog();
            return $ret;
        }

    }

The only method that your class must define when extending `WPScriptCmd` is `main`.

The `main` method should return a string with any information you want to have printed to the screen when your script/command is requested.

If the command produces no output, `main` should return nothing.

#### The script/command file

Now, let's create a new file in the `wp-scripts/` directory called `get_blog_name.php`.

The contents of `get_blog_name.php` should read:

    <?php

    include_once __DIR__ . '/inc/class-cmd-get-blog-name.php';

    $cmd = new GetBlogNameCmd($_GET);
    $cmd->execute();

Notes about the `__construct` function:

- By default, `__construct` accepts two arguments: `action` and `attributes`
    - The `action` argument is a name or label for your command, which can be used as a flag to modify behavior in the `main` method. In our example, our command only does one simple task, so we override the `__construct` function and let `$action == null`.
    - The `attributes` argument should be an array of keys and values to set as attributes of the WPScriptCmd instance. In this case, we pass `$_GET` parameters. If `$_GET = array('blog_id' => 10)`, we will be able to access this data in our `main` method with `$this->blog_id`.

#### Access the script

Now, go to http://mysite.com/wp-scripts/get_blog_name.php?blog_id=10
