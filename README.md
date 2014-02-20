Sahar-Framework
===============

Sahar Framework is an experimental implementation of bringing annotation to PHP

read sf.json
read local.json which overrides all the configuration inside sf.json.

We are going to have couple of annotation which I need to implement.
1: @Resource for Fields
2: @RequestMapping() for methods not for class yet
3: @Security(value = "") security on top of each method cause to check session is available.

/** @Value(key=Version, type=number) default type is string */



every item save as RequestMapping will have the following attributes

"app\controller\profile\ProfileController": {
    "path": "src\app\controller\profile\ProfileController.php",

}

