Units List Filter
=================

This will list courses from the same category as the course that it is executed within (and excluding the current course) that match a custom attribute called 'unittype' against one of these values - Core, Elective, Optional, Introduction. These would be a course custom attribute (drop down) with these values in that order.

It returns a hyperlinked list of these courses containing a figure showing the course image, full name and shortname. If the course uses completions, a checkmark will be rendered if the unit has been completed by the user.

It proxies the course hyperlink through a file called `open.php` which sets the url of page the filter was rendered on as `$SESSION->coursehome`, then redirects to the requested course homepage. You could author your theme or other similar plugin to access this session variable to create a link back to the page that rendered this unit list. 

### Sample output

```html
<figure class="coursebox">
    <a href="https://myserver.test/filter/units/open.php?from=https%3A%2F%2Fmyserver.test%2Fmod%2Fpage%2Fview.php%3Fid%3D1725&amp;to=https%3A%2F%2Fmyserver.test%2Fcourse%2Fview.php%3Fid%3D107"><img src="https://myserver.test/pluginfile.php/3759/course/overviewfiles/Header.jpg"></a>
    <figcaption>
        <span class="course-id"><a href="https://myserver.test/filter/units/open.php?from=https%3A%2F%2Fmyserver.test%2Fmod%2Fpage%2Fview.php%3Fid%3D1725&amp;to=https%3A%2F%2Fmyserver.test%2Fcourse%2Fview.php%3Fid%3D107">BSBPMG636</a></span>
        <span class="course-title"><a href="https://myserver.test/filter/units/open.php?from=https%3A%2F%2Fmyserver.test%2Fmod%2Fpage%2Fview.php%3Fid%3D1725&amp;to=https%3A%2F%2Fmyserver.test%2Fcourse%2Fview.php%3Fid%3D107">BSBPMG636 Manage benefits</a></span>
    </figcaption>
    <span class="completed"><i class="icon fa fa-check fa-fw " title="Complete" aria-label="Complete"></i></span>
</figure>
```

### Sample coursehome renderer

![screenshot](screenshot.jpg)

Though not part of this plugins code explitly, you could use a function to render a link back to the `coursehome` value set by this plugin. There is some sample code over at https://gist.github.com/frumbert/221536dac8f65a5f853b939873dbba24

Licence
-------
GPL3