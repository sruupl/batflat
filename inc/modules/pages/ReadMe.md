`{$page.title}` — displays the title of the page

`{$page.desc}` — displays the page description

`{$page.content}` — displays the contents of the page

`{$pages}` — array with the data of all pages

`{$pages.ID}` — array with the data of specific page

If you want to make a "one page" website, you can use the loop:

```
{loop: $pages}
    <h1>{$value.title}</h1>
    <p>{$value.content}</p>
{/loop}
```