# contrib extension

Actions and templates to reuse in your projects. Maintenance is done by the YesWiki
community, not by an appointed maintainer.

## Actions

### `{{sonogramme}}`

Interactive visualisation built from an audio file.

![Example of a sonogram generated from an audio file](../../presentation/action-sonogramme.png)

### `{{limitentries}}`

Caps how many entries a form accepts.

```
{{limitentries id="1" limit="30" message_max="No seats left, the limit was %{limit}, sorry..." message_count="Booking %{nb} of %{limit}"}}
```

| Parameter | Required | Purpose |
|---|---|---|
| `id` | yes | bazar form id |
| `limit` | yes | maximum number of entries |
| `message_max` | no | message shown once the limit is reached |
| `message_count` | no | message shown while seats remain |

Both messages accept `%{limit}` and `%{nb}`, replaced by the limit and the number of
entries already submitted.

The cap only applies to entries submitted through this action. Bazar administration can
still add entries beyond it.

## Templates

| Template | Purpose |
|---|---|
| `compteur_simple.tpl.html` | shows only the number of entries found |
| `photoswipe.twig` | photo gallery |
| `kanban.twig` | drag and drop kanban board |

### Kanban parameters

```
{{bazarliste id="X" template="kanban.twig" kanbanfield="bf_statut"}}
```

| Parameter | Required | Purpose |
|---|---|---|
| `kanbanfield` | yes | field used to group entries into columns |
| `kanbanaddpage` | no | wiki page carrying the input form |
| `kanbanPersonField` | no | field for the associated people |
| `nbcol` | no | number of columns per row |
| `kanbancolumns` | no | comma-separated column values, in the wanted order |
| `kanbancolumnslabels` | no | labels matching `kanbancolumns` |

Without `kanbancolumnslabels`, and with `groups=kanbanfield` set, labels come from the
filter nodes.
