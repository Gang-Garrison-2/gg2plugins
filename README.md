# README

This is the git repo used to track the Gang Garrison 2 server-sent plugins source (http://ganggarrison.com/plugins/).

**Pull requests are not accepted.** The reason Gang Garrison 2 has a centralised plugin source is so that server-sent plugins can be checked to make sure they are safe. Please ask the relevant person to review your plugin, and they can add it to the repository.

## Details

Plugins are packaged in ZIP files. The specific details of how the contents are used is beyond the scope of the README. For that, see [this guide on the GG2 forums](http://www.ganggarrison.com/forums/index.php?topic=33509.0) or [the GG2 source code itself](https://github.com/Medo42/Gang-Garrison-2/blob/master/Source/gg2/Scripts/Plugins/loadserverplugins.gml).

GG2 uses the MD5 hash of a plugin's ZIP file to distinguish different versions. It fetches `/<pluginname>.md5` over HTTP to download the MD5 hash of the latest version of a plugin. The plugin itself is downloaded from `/<pluginname>@<md5hash>.zip`, where `<md5hash>` is the MD5 hash of the specific version required. The file organisation used by this repo's `/htdocs` directory matches these request patterns.

Alongside the `.zip` and `.md5` files in `/htdocs`, there is also a PHP page and stylesheet (`index.php` and `style.css`) which displays a list of plugins. This page uses the `data.json` file in the root of the repo as its data source.

To make adding and updating the plugins in this repository more convenient, a PHP script named `md5.php` is provided to automate the process. It can be used as follows:

1. Place the ZIP file of the plugin update/initial version at `htdocs/<pluginname>.zip`
2. Run `php md5.php htdocs/<pluginname>.zip` from the root of the repository - This will automatically rename the file to `htdocs/<pluginname>@<md5hash>.zip`, create `htdocs/<pluginname>.md5` and add/update the `data.json` entry
3. If you're adding a new plugin, edit `data.json` manually to populate the `"author"` and `"topic"` fields for the plugin

## JSON file format

Plugins have an entries in the JSON file `data.json`, which has the following format:

```
{
    "plugins" {
        <plugin name>: {
            "author": <name of the plugin author>,
            "topic": <string id of Gang Garrison 2 Forums topic, including .0>,
            "md5s": [
                <MD5 hash of latest version>,
                <MD5 hash of older version>,
                ...
            ]
        },
        ...
    }
}
```

For example, a hypothetical chat plugin might have an entry that looks like this:

```JSON
"example_chat": {
    "author": "Jane Blogges",
    "topic": "123456.0",
    "md5s": [
        "1234567890abcdef1234567890abcdef"
    ]
}
```
