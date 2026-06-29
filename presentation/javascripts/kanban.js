(function() {
  'use strict'

  const BASE_URL = wiki.baseUrl.replace(/\?+$/, '')

  async function loadEntry(pageTag) {
    const res = await fetch(`${BASE_URL}?${pageTag}`, {
      headers: { Accept: 'application/json' }
    })
    if (!res.ok) throw new Error(`HTTP ${res.status}`)
    return res.json()
  }

  async function saveEntryField(entryId, fieldName, newValue) {
    const entry = await loadEntry(entryId)
    entry[fieldName] = newValue
    const res = await fetch(`${BASE_URL}api/entries/${entry.id_typeannonce}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
      body: JSON.stringify(entry)
    })
    if (!res.ok) throw new Error(`HTTP ${res.status}`)
    return res.json()
  }

  function getCount(column) {
    return column.querySelectorAll('.kanban-card').length
  }

  function updateBadge(column) {
    const badge = column.querySelector('.kanban-column-count')
    if (badge) badge.textContent = getCount(column)
  }

  function syncEmptyPlaceholder(body) {
    let placeholder = body.querySelector('.kanban-empty-column')
    const hasCards = body.querySelector('.kanban-card') !== null
    if (hasCards && placeholder) {
      placeholder.remove()
    } else if (!hasCards && !placeholder) {
      placeholder = document.createElement('div')
      placeholder.className = 'kanban-empty-column'
      placeholder.textContent = '—'
      body.appendChild(placeholder)
    }
  }

  function rollback(card, fromBody, oldIndex) {
    const ref = fromBody.children[oldIndex] ?? null
    fromBody.insertBefore(card, ref)
  }

  function initKanbanBoard(board) {
    const kanbanField = board.dataset.kanbanField
    if (!kanbanField) return

    const groupName = 'kanban-' + Math.random().toString(36).slice(2)

    board.addEventListener('click', function(e) {
      const btn = e.target.closest('.kanban-archive-btn')
      if (!btn) return
      e.preventDefault()

      const card = btn.closest('.kanban-card')
      const body = card && card.closest('.kanban-column-body')
      const column = body && body.closest('.kanban-column')
      const entryId = card && card.getAttribute('data-id_fiche')
      if (!entryId) return

      card.classList.add('kanban-card--loading')
      btn.disabled = true

      saveEntryField(entryId, kanbanField, 'archive')
        .then(function() {
          window.location.reload()
        })
        .catch(function(err) {
          console.error('[kanban] archive failed:', err)
          card.classList.remove('kanban-card--loading')
          card.classList.add('kanban-card--error')
          btn.disabled = false
          setTimeout(function() { card.classList.remove('kanban-card--error') }, 2500)
        })
    })

    board.querySelectorAll('.kanban-column').forEach(function(column) {
      const body = column.querySelector('.kanban-column-body')
      if (!body) return

      Sortable.create(body, {
        group: groupName,
        animation: 150,
        ghostClass: 'kanban-card--ghost',
        chosenClass: 'kanban-card--chosen',
        dragClass: 'kanban-card--dragging',
        filter: '[data-no-drag]',
        preventOnFilter: false,

        onEnd: function(evt) {
          const card = evt.item
          const fromBody = evt.from
          const toBody = evt.to
          const oldIndex = evt.oldIndex

          if (fromBody === toBody) return

          const toColumn = toBody.closest('.kanban-column')
          const newValue = toColumn ? toColumn.dataset.columnValue : ''
          const entryId = card.getAttribute('data-id_fiche')

          if (!entryId) return

          card.classList.add('kanban-card--loading')
          syncEmptyPlaceholder(fromBody)
          syncEmptyPlaceholder(toBody)
          updateBadge(fromBody.closest('.kanban-column'))
          updateBadge(toColumn)

          saveEntryField(entryId, kanbanField, newValue)
            .then(function() {
              card.classList.remove('kanban-card--loading')
            })
            .catch(function(err) {
              console.error('[kanban] save failed:', err)
              card.classList.remove('kanban-card--loading')
              card.classList.add('kanban-card--error')
              rollback(card, fromBody, oldIndex)
              syncEmptyPlaceholder(fromBody)
              syncEmptyPlaceholder(toBody)
              updateBadge(fromBody.closest('.kanban-column'))
              updateBadge(toColumn)
              setTimeout(function() { card.classList.remove('kanban-card--error') }, 2500)
            })
        }
      })
    })
  }

  function stringToHue(str) {
    var hash = 0
    for (var i = 0; i < str.length; i++) {
      hash = str.charCodeAt(i) + ((hash << 5) - hash)
    }
    return Math.abs(hash) % 360
  }

  function colorizePersonBadges() {
    document.querySelectorAll('.kanban-person-badge').forEach(function(badge) {
      var hue = stringToHue(badge.dataset.person || badge.textContent.trim())
      badge.style.backgroundColor = 'hsl(' + hue + ', 55%, 42%)'
    })
  }

  function initKanbanAddReload() {
    if (!document.querySelector('.kanban-add-btn')) return

    var pendingReload = false

    document.addEventListener('click', function(e) {
      if (e.target && e.target.closest && e.target.closest('.kanban-add-btn')) {
        pendingReload = true
      }
    }, true)

    if (typeof $ !== 'undefined') {
      $(document).on('hidden.bs.modal', function() {
        if (pendingReload) {
          pendingReload = false
          window.location.reload()
        }
      })
    }
  }

  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.kanban-board').forEach(initKanbanBoard)
    colorizePersonBadges()
    initKanbanAddReload()
  })
})()